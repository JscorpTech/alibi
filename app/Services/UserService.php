<?php

namespace App\Services;

use App\Enums\CardEnum;
use App\Enums\CardPriceEnum;
use App\Enums\CashbackEnum;
use App\Enums\DiscountEnum;
use App\Enums\DiscountTypeEnum;
use App\Enums\OrderStatusEnum;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class UserService
{
    /**
     * Get product price for calculate discount
     *
     * @param $price
     * @return object
     */
    public function getProductPrice($price): object
    {
        $discount = ($price * ($this->getDiscount()->discount / 100));
        $price = $price - $discount;

        return (object) [
            'price'    => (int) $price,
            'discount' => $discount,
        ];
    }

    /**
     * Get user discount
     *
     * @return object
     */
    public function getDiscount(): object
    {
        $user = Auth::user();

        if ($user->is_first_order) {
            $discount = DiscountEnum::FIRST_ORDER;
            $discountType = DiscountTypeEnum::FIRST_ORDER;
        }

        return (object) [
            'discount' => (int) ($discount ?? 0),
            'type'     => $discountType ?? null,
        ];
    }

    /**
     * Get Cashback
     *
     * @param $price
     * @return int
     */
    public static function getCashback($user = null): int
    {
        $user = $user ?? Auth::user();
        $card = UserService::getCard($user);

        $discount = match ($card) {
            CardEnum::BLACK    => CashbackEnum::BLACK,
            CardEnum::PLATINUM => CashbackEnum::PLATINUM,
            CardEnum::GOLD     => CashbackEnum::GOLD
        };

        return (int) $discount;
    }

    public static function getCard($user = null)
    {
        $user = $user ?? Auth::user();

        return CacheService::remember(function () use ($user) {
            $orders = Order::query()->whereHas('OrderGroup', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where(['status' => OrderStatusEnum::SUCCESS]);
            });
            $orders_price = $orders->sum('price');
            if ($orders_price >= CardPriceEnum::GOLD) {
                return CardEnum::GOLD;
            } elseif ($orders_price >= CardPriceEnum::PLATINUM) {
                return CardEnum::PLATINUM;
            } else {
                return CardEnum::BLACK;
            }
        }, key: md5("card_$user->id"));
    }
}
