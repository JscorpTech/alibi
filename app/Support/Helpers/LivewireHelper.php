<?php

namespace App\Support\Helpers;

use App\Models\ProductOption;

class LivewireHelper
{
    public static function getValue($product_id, $size, $color)
    {
        return ProductOption::query()->where(['size_id' => $size, 'color_id' => $color, 'product_id' => $product_id])->first()->count ?? 1;
    }
}
