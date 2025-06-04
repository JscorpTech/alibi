<?php

namespace App\Observers;

use App\Enums\OrderStatusEnum;
use App\Models\OrderGroup;
use App\Services\Admin\OrderService;
use App\Services\BotService;
use App\Services\UserService;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderGroupObserver
{
    public function created(OrderGroup $orderGroup)
    {
        $service = new BotService();
        $orderGroup->user()->update([
            'is_first_order' => false,
        ]);
        $user = Auth::user();
        $user->balance -= $orderGroup->cashback;
        $user->save();

        $service->sendMessage(
            Env::get('ADMIN_CHAT_ID'),
            __(
                "Yangi buyurtma: 💵\n\nBuyurtma: <a href=':order'>#:order_id</a>\nManzil: :address\nTo'lov turi: :payment_type",
                [
                    'order' => route('filament.admin.resources.order-groups.view', [
                        'record' => $orderGroup->id,
                    ]),
                    'address'      => $orderGroup->address->label,
                    'payment_type' => __($orderGroup->payment_type),
                    'order_id'     => $orderGroup->id,
                ]
            ),
        );
    }

    public function updated(OrderGroup $orderGroup)
    {
        if ($orderGroup->isDirty('status') && !$orderGroup->isDirty('given_cashback')) {
            $status = $orderGroup->status;
            $user = $orderGroup->user;

            OrderService::first_order_sync($user);

            DB::beginTransaction();
            try {
                if ($status === OrderStatusEnum::SUCCESS) {
                    $cashback = round(($orderGroup->orders()->sum('price') / 100) * UserService::getCashback($user));
                    $user->balance += $cashback;

                    DB::table('order_groups')->where([
                        'id' => $orderGroup->id,
                    ])->update(['given_cashback' => $cashback]);

                    OrderService::editProductOption($orderGroup, 'remove');
                } elseif ($status == OrderStatusEnum::CANCELED) {
                    $user->balance -= $orderGroup->given_cashback;
                    DB::table('order_groups')->where([
                        'id' => $orderGroup->id,
                    ])->update(['given_cashback' => 0]);

                    if ($user->balance < 0) {
                        $user->balance = 0;
                    }
                }

                $user->save();
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
