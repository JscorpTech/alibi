<?php

namespace App\Http\Resources\Api\Option;

use App\Http\Resources\Api\ColorResource;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ColorOptionResource extends JsonResource
{
    use BaseResource;


    public function toArray(Request $request): array
    {
        $product = $this->color_product;
        if ($product) {
            $product = [
                "id" => $product?->id,
                "image" => Storage::url($product?->image)
            ];
        } else {
            $product = null;
        }
        return [
            "name" => $this->color->name,
            "product" => $product,
            "color" => ColorResource::make($this->color),
        ];
    }
}
