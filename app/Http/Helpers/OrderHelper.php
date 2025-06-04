<?php

namespace App\Http\Helpers;

use App\Enums\OrderStatusEnum;

class OrderHelper
{
    public static function getStatusIcon($status): string
    {
        return match ($status) {
            OrderStatusEnum::CANCELED => 'fa-ban',
            OrderStatusEnum::SUCCESS  => 'fa-check',
            OrderStatusEnum::PENDING  => 'fa-redo'
        };
    }

    public static function getStatusColor($status): string
    {
        return match ($status) {
            OrderStatusEnum::CANCELED => 'secondary',
            OrderStatusEnum::SUCCESS  => 'success',
            OrderStatusEnum::PENDING  => 'primary'
        };
    }

    public static function getPrice($product): string
    {
        if ($product->discount != null and $product->discount != 0) {
            return number_format($product->discount) . ' ' . __('currency');
        } else {
            return number_format($product->price) . ' ' . __('currency');
        }
    }
}
