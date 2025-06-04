<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\Api\Product\ProductListResource;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    use BaseResource;

    public function toArray(Request $request): array
    {
        return [
            'id'      => $this->id,
            'product' => ProductListResource::make($this->product),
            'price'   => $this->getTotalPrice(),
            'color'   => SizeResource::make($this->color),
            'size'    => SizeResource::make($this->size),
            'count'   => $this->count,
        ];
    }
}
