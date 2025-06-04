<?php

namespace App\Http\Resources\Api\Product;

use App\Http\Resources\Api\BrandResource;
use App\Http\Resources\Api\ColorResource;
use App\Http\Resources\Api\ImageResource;
use App\Http\Resources\Api\Option\ColorOptionResource;
use App\Http\Resources\Api\Option\OptionResource;
use App\Http\Resources\Api\SizeResource;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Api\TagResource;


/**
 * @method getPriceNumber()
 * @method getDiscountNumber()
 */
class ProductResource extends JsonResource
{
    use BaseResource;

    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->desc,
            'gender'   => $this->gender,
            "label"    => $this->label,
            'price'    => (int) $this->price,
            'discount' => (int) $this->getDiscountNumber(),
            'image'    => Storage::url($this->image),
            'colors'   => ColorOptionResource::collection($this->options),
            'colors2'   => ColorResource::collection($this->colors),
            'sizes'    => SizeResource::collection($this->sizes),
            'images'   => ImageResource::collection($this->images),
            "tags"     => TagResource::collection($this->tags),
            "brand"    => BrandResource::make($this->brand),
            "options"  => OptionResource::collection($this->product_options),
            "views"    => $this->views
        ];
    }
}
