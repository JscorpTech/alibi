<?php

namespace App\Http\Resources\Api\Product;

use App\Http\Resources\Api\BrandResource;
use App\Http\Resources\Api\ImageResource;
use App\Http\Resources\Api\Option\ColorOptionResource;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Api\TagResource;

/**
 * @method getPriceNumber()
 * @method getDiscountNumber()
 */
class ProductListResource extends JsonResource
{
    use BaseResource;

    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'price'    => (int) $this->price,
            'discount' => (int) $this->getDiscountNumber(),
            'image'    => Storage::url($this->image),
            "tags"     => TagResource::collection($this->tags),
            "brand"    => BrandResource::make($this->brand),
            "label"    => $this->label,
            "views"    => $this->views,
            'colors'   => ColorOptionResource::collection($this->options),
            'images'   => ImageResource::collection($this->images),

        ];
    }
}
