<?php
/**
 * File: app/Observers/OrderGroupObserver.php
 *
 * ЗА ЧТО ОТВЕЧАЕТ ЭТОТ OBSERVER
 * --------------------------------
 * Этот класс слушает события Eloquent-модели OrderGroup и выполняет побочные
 * действия при создании/изменении заказа:
 *
 * 1) created:
 *    - снимает флаг "первый заказ" у пользователя;
 *    - если пользователь применил кэшбэк — уменьшает его баланс;
 *    - отправляет Telegram-уведомление для заказов из приложения (source != 'pos').
 *    - ВАЖНО: сейчас в created НЕ трогаем остатки склада (stock).
 *
 * 2) updated (когда меняется статус):
 *    - ДЛЯ APP (source != 'pos'): возвращает stock при отмене (CANCELED);
 *    - На SUCCESS: если пустые, проставляет paid_at и total, начисляет given_cashback
 *      и увеличивает баланс пользователя;
 *    - На CANCELED: откатывает ранее выданный given_cashback.
 *
 * 3) adjustStock():
 *    - Служебная функция, которая умеет безопасно инкрементить/декрементить
 *      stock у variants по позициям заказа.
 *
 * С ЧЕМ ЭТО РАБОТАЕТ
 * -------------------
 * - Модель: App\Models\OrderGroup (+ связи user, address, orders)
 * - Модель: App\Models\Variant (поле stock)
 * - Сервисы:
 *     * App\Services\Admin\OrderService::first_order_sync() — синхронизация признака первого заказа.
 *     * App\Services\UserService::getCashback() — ставка кэшбэка.
 *     * App\Services\BotService — отправка уведомлений в Telegram.
 *
 * ГДЕ СЕЙЧАС МЕНЯЕТСЯ STOCK
 * --------------------------
 * - Для APP: списание делается в сервисе создания заказа (OrderService::create),
 *            а ВОЗВРАТ при отмене делаем здесь, в updated().
 * - Для POS: списание/возврат управляется POS-сервисами (например, OrderWriter),
 *            а в этом observer мы POS-остатки НЕ трогаем (см. условие $source !== 'pos').
 *
 * ПРИМЕЧАНИЕ
 * ----------
 * Если захотим централизовать движение остатков «по статусам», можно перенести
 * и списание, и возврат целиком сюда (в updated), а из сервисов убрать изменение stock.
 */


namespace App\Observers;

use App\Enums\OrderStatusEnum;
use App\Models\OrderGroup;
use App\Models\Variant;
use App\Services\Admin\OrderService;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;

class OrderGroupObserver
{
    /**
     * Срабатывает один раз после создания OrderGroup.
     * Здесь НЕ трогаем остатки, только «мягкие» побочки:
     *  - снимаем is_first_order у пользователя,
     *  - списываем применённый кэшбэк с баланса,
     *  - отправляем Telegram-уведомление для заказов из приложения.
     */

    public function created(OrderGroup $orderGroup): void
    {
        try {
            $orderGroup->loadMissing(['user', 'address', 'orders']); // ⬅ подгружаем orders тоже

            // первый заказ — убираем флаг
            if ($orderGroup->user) {
                $orderGroup->user->update(['is_first_order' => false]);
            }

            // списываем кешбэк у текущего юзера, если он был применён
            if (!empty($orderGroup->cashback) && auth()->check()) {
                $u = auth()->user();
                $u->balance = max(0, (int) $u->balance - (int) $orderGroup->cashback);
                $u->save();
            }

            // ⚠️ ЕСЛИ POS сразу создал со статусом success — спишем сток уже сейчас
            // if (($orderGroup->source ?? null) === 'pos' && $orderGroup->status === OrderStatusEnum::SUCCESS) {
            //     $this->adjustStock($orderGroup, 'decrement'); // списание
            //     return; // POS: не шлём сообщение в общий канал
            // }

            // --- Telegram уведомление только для app-заказов ---
            if (($orderGroup->source ?? null) !== 'pos') {
                $addressLabel = optional($orderGroup->address)->label ?? 'Без адреса';
                $payment = $orderGroup->payment_method ?? $orderGroup->payment_type ?? 'Не указан';
                $url = route('filament.admin.resources.order-groups.view', ['record' => $orderGroup->id]);

                app(\App\Services\BotService::class)->sendMessage(
                    env('ADMIN_CHAT_ID'),
                    __(
                        "Yangi buyurtma: 💵\n\nBuyurtma: <a href=':order'>#:order_id</a>\nManzil: :address\nTo'lov turi: :payment_type",
                        ['order' => $url, 'order_id' => $orderGroup->id, 'address' => $addressLabel, 'payment_type' => $payment]
                    )
                );
            }
        } catch (\Throwable $e) {
            \Log::error('OrderGroupObserver.created failed: ' . $e->getMessage(), ['order_group_id' => $orderGroup->id]);
        }
    }
    /**
     * Срабатывает ПЕРЕД сохранением, когда изменились поля.
     * Используем, чтобы на уровне статуса подвинуть баланс по применённому кэшбэку,
     * если статус идёт в CANCELED / выходит из CANCELED.
     */

    public function updating(OrderGroup $orderGroup): void
    {
        if ($orderGroup->isDirty('status')) {
            $status = $orderGroup->status;
            $oldStatus = $orderGroup->getOriginal('status');
            $user = $orderGroup->user;

            if ($status === OrderStatusEnum::CANCELED) {
                $user->balance += (int) $orderGroup->cashback;
                $user->save();
            } elseif ($oldStatus === OrderStatusEnum::CANCELED) {
                $user->balance -= (int) $orderGroup->cashback;
                $user->save();
            }
        }
    }

    /**
     * Срабатывает ПОСЛЕ сохранения, когда статус реально изменился.
     * Здесь:
     *  - для APP (source != 'pos') возвращаем stock при отмене,
     *  - на SUCCESS проставляем paid_at/total, считаем/записываем given_cashback и увеличиваем баланс,
     *  - на CANCELED — откатываем ранее выданный given_cashback.
     *
     * ВНИМАНИЕ: изменение stock организовано только для APP-отмены.
     * POS-остатки изменяются в POS-сервисах.
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

        \App\Services\Admin\OrderService::first_order_sync($user);

        DB::beginTransaction();
        try {
            // --- сток ---
            if ($source !== 'pos') {
                if ($newStatus === \App\Enums\OrderStatusEnum::CANCELED && $oldStatus !== \App\Enums\OrderStatusEnum::CANCELED) {
                    $this->adjustStock($orderGroup, 'increment');
                }
            }

            $updateGroup = [];

            // --- SUCCESS: проставить paid_at/total, начислить кешбэк ---
            if ($newStatus === \App\Enums\OrderStatusEnum::SUCCESS) {
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

                $sumForCashback = (int) $orderGroup->orders()->sum('price');
                $cashback = (int) round(($sumForCashback / 100) * \App\Services\UserService::getCashback($user));
                $user->balance += $cashback;

                DB::table('order_groups')
                    ->where('id', $orderGroup->id)
                    ->update(['given_cashback' => $cashback]);
            }

            // --- CANCELED: откат кешбэка ---
            if ($newStatus === \App\Enums\OrderStatusEnum::CANCELED) {
                $user->balance -= (int) $orderGroup->given_cashback;
                if ($user->balance < 0)
                    $user->balance = 0;

                DB::table('order_groups')
                    ->where('id', $orderGroup->id)
                    ->update(['given_cashback' => 0]);
            }

            // Сохраняем только пользователя обычным save()
            $user->save();

            // А для группы — без событий:
            if (!empty($updateGroup)) {
                DB::table('order_groups')
                    ->where('id', $orderGroup->id)
                    ->update($updateGroup);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Утилита для безопасного изменения остатков по всем позициям группы.
     * $op = 'decrement' (списать) или 'increment' (вернуть).
     * Для списания проверяем, что stock >= qty — иначе кидаем исключение.
     */
    private function adjustStock(OrderGroup $group, string $op): void
    {
        foreach ($group->orders as $o) {
            $vid = (int) ($o->variant_id ?? 0);
            $qty = (int) ($o->count ?? 0);
            if ($vid <= 0 || $qty <= 0)
                continue;

            if ($op === 'decrement') {
                // безопасно уменьшаем, только если хватает остатков; иначе бросаем исключение
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