<?php

namespace App\Observers;

use App\Enums\OrderStatusEnum;
use App\Models\OrderGroup;
use App\Services\Admin\OrderService;
use App\Services\Inventory\StockService;  // ⭐ НОВОЕ
use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderGroupObserver
{
    // ⭐ ДОБАВИТЬ КОНСТРУКТОР
    public function __construct(
        private StockService $stockService
    ) {
    }

    /**
     * Срабатывает один раз после создания OrderGroup.
     * Здесь НЕ трогаем остатки (это делает OrderWriter через StockService),
     * только «мягкие» побочки:
     *  - снимаем is_first_order у пользователя,
     *  - списываем применённый кэшбэк с баланса,
     *  - отправляем Telegram-уведомление для заказов из приложения.
     */
    public function created(OrderGroup $orderGroup): void
    {
        try {
            $orderGroup->loadMissing(['user', 'address', 'orders']);

            // Первый заказ — убираем флаг
            if ($orderGroup->user) {
                $orderGroup->user->update(['is_first_order' => false]);
            }

            // Списываем кешбэк у текущего юзера, если он был применён
            if (!empty($orderGroup->cashback) && auth()->check()) {
                $u = auth()->user();
                $u->balance = max(0, (int) $u->balance - (int) $orderGroup->cashback);
                $u->save();
            }

            // ⭐ ВАЖНО: Склад уже изменён в OrderWriter через StockService!
            // Здесь НЕ трогаем stock

            // --- Telegram уведомление только для app-заказов ---
            if (($orderGroup->source ?? null) !== 'pos') {
                $this->sendTelegramNotification($orderGroup);
            }

            Log::info('✅ OrderGroupObserver.created выполнен', [
                'order_group_id' => $orderGroup->id,
                'order_number' => $orderGroup->order_number,
                'source' => $orderGroup->source,
                'type' => $orderGroup->type,
            ]);
        } catch (\Throwable $e) {
            Log::error('❌ OrderGroupObserver.created failed', [
                'order_group_id' => $orderGroup->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Срабатывает ПЕРЕД сохранением, когда изменились поля.
     * Используем, чтобы на уровне статуса подвинуть баланс по применённому кэшбэку,
     * если статус идёт в CANCELED / выходит из CANCELED.
     */
    public function updating(OrderGroup $orderGroup): void
    {
        if (!$orderGroup->isDirty('status')) {
            return;
        }

        $status = $orderGroup->status;
        $oldStatus = $orderGroup->getOriginal('status');
        $user = $orderGroup->user;

        if (!$user) {
            return;
        }

        // Возвращаем применённый кэшбэк при отмене
        if ($status === OrderStatusEnum::CANCELED) {
            $user->balance += (int) $orderGroup->cashback;
            $user->save();

            Log::info('💰 Возврат применённого кэшбэка при отмене', [
                'order_group_id' => $orderGroup->id,
                'cashback' => $orderGroup->cashback,
                'user_id' => $user->id,
                'new_balance' => $user->balance,
            ]);
        }
        // Списываем кэшбэк если статус вышел из CANCELED
        elseif ($oldStatus === OrderStatusEnum::CANCELED) {
            $user->balance -= (int) $orderGroup->cashback;
            $user->save();

            Log::info('💰 Списание кэшбэка при восстановлении заказа', [
                'order_group_id' => $orderGroup->id,
                'cashback' => $orderGroup->cashback,
                'user_id' => $user->id,
                'new_balance' => $user->balance,
            ]);
        }
    }

    /**
     * Срабатывает ПОСЛЕ сохранения, когда статус реально изменился.
     * Здесь:
     *  - для APP (source != 'pos') возвращаем stock при отмене через StockService,
     *  - на SUCCESS проставляем paid_at/total, считаем/записываем given_cashback и увеличиваем баланс,
     *  - на CANCELED — откатываем ранее выданный given_cashback.
     */
    public function updated(OrderGroup $orderGroup): void
    {
        if (!$orderGroup->isDirty('status') || $orderGroup->isDirty('given_cashback')) {
            return;
        }

        $orderGroup->loadMissing(['user', 'orders']);

        $newStatus = $orderGroup->status;
        $oldStatus = $orderGroup->getOriginal('status');
        $source = (string) ($orderGroup->source ?? 'app');
        $user = $orderGroup->user;

        if ($user) {
            OrderService::first_order_sync($user);
        }

        DB::beginTransaction();
        try {
            // ========================================
            // ⭐ СКЛАД: Возврат товаров при отмене APP заказа
            // ========================================
            if ($source !== 'pos') {
                if ($newStatus === OrderStatusEnum::CANCELED && $oldStatus !== OrderStatusEnum::CANCELED) {
                    $this->returnStockOnCancel($orderGroup);
                }
            }

            $updateGroup = [];

            // ========================================
            // SUCCESS: проставить paid_at/total, начислить кешбэк
            // ========================================
            if ($newStatus === OrderStatusEnum::SUCCESS) {
                if (empty($orderGroup->paid_at)) {
                    $updateGroup['paid_at'] = now();
                }

                if (empty($orderGroup->total)) {
                    $sum = $orderGroup->orders->reduce(function ($acc, $o) {
                        $unit = max(0, (int) $o->price - (int) ($o->discount ?? 0));
                        return $acc + $unit * (int) $o->count;
                    }, 0);
                    $updateGroup['total'] = (int) $sum;
                }

                // Начисляем given_cashback
                if ($user && $orderGroup->type !== 'return') {
                    $sumForCashback = (int) $orderGroup->orders()->sum('price');
                    $cashback = (int) round(($sumForCashback / 100) * UserService::getCashback($user));

                    if ($cashback > 0) {
                        $user->balance += $cashback;
                        $user->save();

                        DB::table('order_groups')
                            ->where('id', $orderGroup->id)
                            ->update(['given_cashback' => $cashback]);

                        Log::info('💰 Начислен given_cashback', [
                            'order_group_id' => $orderGroup->id,
                            'cashback' => $cashback,
                            'user_id' => $user->id,
                            'new_balance' => $user->balance,
                        ]);
                    }
                }
            }

            // ========================================
            // CANCELED: откат given_cashback
            // ========================================
            if ($newStatus === OrderStatusEnum::CANCELED) {
                if ($user && $orderGroup->given_cashback > 0) {
                    $user->balance -= (int) $orderGroup->given_cashback;
                    if ($user->balance < 0) {
                        $user->balance = 0;
                    }
                    $user->save();

                    DB::table('order_groups')
                        ->where('id', $orderGroup->id)
                        ->update(['given_cashback' => 0]);

                    Log::info('💰 Откат given_cashback при отмене', [
                        'order_group_id' => $orderGroup->id,
                        'returned_cashback' => $orderGroup->given_cashback,
                        'user_id' => $user->id,
                        'new_balance' => $user->balance,
                    ]);
                }
            }

            // Сохраняем изменения в order_groups без событий
            if (!empty($updateGroup)) {
                DB::table('order_groups')
                    ->where('id', $orderGroup->id)
                    ->update($updateGroup);
            }

            DB::commit();

            Log::info('✅ OrderGroupObserver.updated выполнен', [
                'order_group_id' => $orderGroup->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'source' => $source,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('❌ OrderGroupObserver.updated failed', [
                'order_group_id' => $orderGroup->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    // ========================================
    // ⭐ НОВЫЕ ПРИВАТНЫЕ МЕТОДЫ
    // ========================================

    /**
     * Возврат товаров на склад при отмене APP заказа
     */
    private function returnStockOnCancel(OrderGroup $orderGroup): void
    {
        foreach ($orderGroup->orders as $order) {
            $variantId = (int) ($order->variant_id ?? 0);
            $qty = (int) ($order->count ?? 0);

            if ($variantId <= 0 || $qty <= 0) {
                continue;
            }

            try {
                // ⭐ ИСПОЛЬЗУЕМ StockService вместо adjustStock
                $this->stockService->increase(
                    variantId: $variantId,
                    qty: $qty,
                    reason: 'cancel',
                    source: $orderGroup->source ?? 'app',
                    orderGroupId: $orderGroup->id
                );

                Log::info('📦 Товар возвращён на склад при отмене', [
                    'order_group_id' => $orderGroup->id,
                    'order_id' => $order->id,
                    'variant_id' => $variantId,
                    'qty' => $qty,
                ]);
            } catch (\Exception $e) {
                Log::error('❌ Ошибка возврата товара на склад', [
                    'order_group_id' => $orderGroup->id,
                    'order_id' => $order->id,
                    'variant_id' => $variantId,
                    'error' => $e->getMessage(),
                ]);

                throw $e;
            }
        }
    }

    /**
     * Отправка Telegram уведомления
     */
    private function sendTelegramNotification(OrderGroup $orderGroup): void
    {
        try {
            $addressLabel = optional($orderGroup->address)->label ?? 'Без адреса';
            $payment = $orderGroup->payment_method ?? $orderGroup->payment_type ?? 'Не указан';
            $url = route('filament.admin.resources.order-groups.view', ['record' => $orderGroup->id]);

            app(\App\Services\BotService::class)->sendMessage(
                env('ADMIN_CHAT_ID'),
                __(
                    "Yangi buyurtma: 💵\n\nBuyurtma: <a href=':order'>#:order_id</a>\nManzil: :address\nTo'lov turi: :payment_type",
                    [
                        'order' => $url,
                        'order_id' => $orderGroup->id,
                        'address' => $addressLabel,
                        'payment_type' => $payment
                    ]
                )
            );

            Log::info('📱 Telegram уведомление отправлено', [
                'order_group_id' => $orderGroup->id,
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Ошибка отправки Telegram уведомления', [
                'order_group_id' => $orderGroup->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    // ========================================
    // ⚠️ DEPRECATED: Старый метод (НЕ ИСПОЛЬЗУЕТСЯ)
    // Оставлен для обратной совместимости
    // ========================================

    /**
     * @deprecated Используй StockService вместо этого
     */
    private function adjustStock(OrderGroup $group, string $op): void
    {
        Log::warning('⚠️ Используется deprecated метод adjustStock', [
            'order_group_id' => $group->id,
            'operation' => $op,
            'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3),
        ]);

        foreach ($group->orders as $o) {
            $vid = (int) ($o->variant_id ?? 0);
            $qty = (int) ($o->count ?? 0);
            if ($vid <= 0 || $qty <= 0) {
                continue;
            }

            if ($op === 'decrement') {
                $affected = DB::table('variants')
                    ->where('id', $vid)
                    ->where('stock', '>=', $qty)
                    ->decrement('stock', $qty);

                if ($affected === 0) {
                    throw new \RuntimeException("Variant #{$vid}: insufficient stock");
                }
            } else {
                DB::table('variants')
                    ->where('id', $vid)
                    ->increment('stock', $qty);
            }
        }
    }
}