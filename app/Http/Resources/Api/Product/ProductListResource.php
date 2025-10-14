<?php

namespace App\Http\Resources\Api\Product;

use App\Http\Resources\Api\BrandResource;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Api\TagResource;

class ProductListResource extends JsonResource
{
    use BaseResource;

    public function toArray(Request $request): array
    {
        // name fallback
        $name = (string) ($this->name ?? $this->name_ru ?? '');

        // gallery (JSON) → массив строк
        $gallery = [];
        foreach ((array) ($this->gallery ?? []) as $u) {
            $gallery[] = is_string($u) ? $u : (string) $u;
        }

        // cover: gallery[0] → иначе image; всегда абсолютный URL
        $coverPath = $gallery[0] ?? ($this->image ?? null);
        $image = $coverPath
            ? (str_starts_with($coverPath, 'http') ? $coverPath : Storage::url($coverPath))
            : null;

        return [
            'id' => $this->id,
            'name' => $name,
            'price' => (int) $this->price,
            'discount' => (int) ($this->getDiscountNumber() ?? 0),
            'image' => $image,                 // cover, как и раньше
            // оставляем как ты хотел:
            'tags' => $this->relationLoaded('tags') ? TagResource::collection($this->tags ?? []) : [],
            'brand' => $this->brand ? BrandResource::make($this->brand) : null,
            'views' => (int) $this->views,
            'label' => $this->label,


        ];
    }
}