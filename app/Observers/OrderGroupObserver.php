<?php

namespace App\Observers;

use App\Enums\OrderStatusEnum;
use App\Models\OrderGroup;
use App\Services\Admin\OrderService;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;

class OrderGroupObserver
{
    public function created(OrderGroup $orderGroup): void
    {
        try {
            $orderGroup->loadMissing(['user', 'address']);

            // Баланс — если клиент тратит баллы при оформлении
            if (!empty($orderGroup->cashback) && auth()->check()) {
                $u = auth()->user();
                $u->balance = max(0, (int) $u->balance - (int) $orderGroup->cashback);
                $u->save();
            }

            // POS — не шлём в Telegram
            if (($orderGroup->source ?? null) === 'pos') {
                return;
            }

            // Telegram уведомление
            $addressLabel = optional($orderGroup->address)->label ?? 'Без адреса';
            $payment = $orderGroup->payment_method
                ?? $orderGroup->payment_type
                ?? 'Не указан';

            $url = route('filament.admin.resources.order-groups.view', [
                'record' => $orderGroup->id,
            ]);

            (new \App\Services\BotService())->sendMessage(
                env('ADMIN_CHAT_ID'),
                __(
                    "Yangi buyurtma: 💵\n\nBuyurtma: <a href=':order'>#:order_id</a>\nManzil: :address\nTo'lov turi: :payment_type",
                    [
                        'order'        => $url,
                        'order_id'     => $orderGroup->id,
                        'address'      => $addressLabel,
                        'payment_type' => $payment,
                    ]
                )
            );
        } catch (\Throwable $e) {
            \Log::error('OrderGroupObserver.created failed: ' . $e->getMessage(), ['order_group_id' => $orderGroup->id]);
        }
    }

    public function updated(OrderGroup $orderGroup)
    {
        if ($orderGroup->isDirty('status') && !$orderGroup->isDirty('given_cashback')) {
            $status = $orderGroup->status;
            $user = $orderGroup->user;

            if (!$user) {
                return;
            }

            DB::beginTransaction();
            try {
                if ($status === OrderStatusEnum::SUCCESS) {
                    // Считаем сумму заказа (цена - скидка) * кол-во
                    $orderTotal = (int) $orderGroup->orders()->sum(
                        DB::raw('(price - COALESCE(discount, 0)) * count')
                    );

                    // Получаем процент по уровню
                    $rate = UserService::getCashback($user);
                    $cashback = (int) round(($orderTotal / 100) * $rate);

                    // Начисляем баллы
                    $user->balance += $cashback;

                    // Обновляем total_spent
                    $user->total_spent = ($user->total_spent ?? 0) + $orderTotal;
                    $user->save();

                    // Проверяем повышение уровня
                    UserService::updateLevel($user);

                    // Сохраняем начисленные баллы
                    DB::table('order_groups')->where('id', $orderGroup->id)
                        ->update(['given_cashback' => $cashback]);

                    // Обновляем остатки товаров
                    OrderService::editProductOption($orderGroup, 'remove');

                } elseif ($status == OrderStatusEnum::CANCELED) {
                    // Отмена — забираем баллы назад
                    $user->balance -= $orderGroup->given_cashback;
                    if ($user->balance < 0) {
                        $user->balance = 0;
                    }
                    $user->save();

                    DB::table('order_groups')->where('id', $orderGroup->id)
                        ->update(['given_cashback' => 0]);
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }
    }

    public function updating(OrderGroup $orderGroup)
    {
        if ($orderGroup->isDirty('status')) {
            $status = $orderGroup->status;
            $oldStatus = $orderGroup->getOriginal('status');
            $user = $orderGroup->user;

            if (!$user) {
                return;
            }

            // Возврат потраченных баллов при отмене
            if ($status === OrderStatusEnum::CANCELED) {
                $user->balance += (int) $orderGroup->cashback;
                $user->save();
            } elseif ($oldStatus == OrderStatusEnum::CANCELED) {
                $user->balance -= (int) $orderGroup->cashback;
                $user->save();
            }
        }
    }
}
