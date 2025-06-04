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
        return [
            'id'      => $this->id,
            'product' => ProductListResource::make($this->product),
            'count'   => $this->count,
            'price'   => $this->getTotalPrice(),
            'size'    => SizeResource::make($this->size),
            'color'   => ColorResource::make($this->color),
        ];
    }
}
