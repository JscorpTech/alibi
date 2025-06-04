<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\Api\Product\ProductResource;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LikeResource extends JsonResource
{
    use BaseResource;

    public function toArray(Request $request): array
    {
        return [
            'product' => ProductResource::make($this->product),
        ];
    }
}
