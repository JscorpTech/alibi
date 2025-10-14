<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\Api\Product\ProductListResource;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BasketResource extends JsonResource
{
    use BaseResource;

    public function toArray(Request $request): array
    {
        $product = $this->whenLoaded('product');
        $variant = $this->whenLoaded('variant');

        // приоритет цены варианта (если не задана — берём цену продукта)
        $productPrice = (int) ($product->price ?? 0);
        $unitPrice = (int) ($variant->price ?? 0);
        if ($unitPrice <= 0) {
            $unitPrice = $productPrice;
        }

        $count = (int) $this->count;
        $linePrice = $unitPrice * $count;

        return [
            'id' => $this->id,
            'product' => ProductListResource::make($product),   // краткая инфа о продукте (image, name, price и т.д.)
            'variant' => [
                'id' => $variant->id ?? null,
                'sku' => $variant->sku ?? null,
                'barcode' => $variant->barcode ?? null,
                'attrs' => (array) ($variant->attrs ?? []),       // {"Size":"M","Color":"Black"}
                'stock' => (int) ($variant->stock ?? 0),
            ],

            'count' => $count,

            // цены
            'unit_price' => $unitPrice,        // применённая цена за единицу (variant.price > 0 ? variant.price : product.price)
            'line_price' => $linePrice,        // unit_price * count (что реально платить за строку)
            'price' => $linePrice,        // для совместимости со старым фронтом

            // больше НЕ отдаём legacy:
            // 'size' => SizeResource::make($this->size),
            // 'color' => ColorResource::make($this->color),
        ];
    }
}