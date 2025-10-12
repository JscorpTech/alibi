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

class ProductResource extends JsonResource
{
    use BaseResource;

    public function toArray(Request $request): array
    {
        // 1) image (cover)
        $imageUrl = $this->image ? Storage::url($this->image) : null;

        // 2) gallery (JSON на продукте) -> массив строк (URL)
        $gallery = [];
        foreach ((array) ($this->gallery ?? []) as $u) {
            // если у тебя в gallery уже полные URL — Storage::url не нужен
            $gallery[] = is_string($u) ? $u : (string) $u;
        }

        // 3) colorImages (JSON: map Color => string|array) -> нормализуем в массивы
        $colorImages = [];
        foreach ((array) ($this->color_images ?? []) as $color => $val) {
            if (is_string($val)) {
                $colorImages[$color] = [$val];
            } else {
                $colorImages[$color] = array_values(array_map(
                    fn($x) => is_string($x) ? $x : (string) $x,
                    (array) $val
                ));
            }
        }

        // 4) options: берём из JSON поля products.options; если пусто — соберём из твоей БД-логики
        $options = $this->options ?? [];
        if (empty($options)) {
            // Попробуем собрать твоим методом buildVariantState()
            if (method_exists($this->resource, 'buildVariantState')) {
                $state = $this->resource->buildVariantState();
                $options = $state['variant_options'] ?? [];
            } else {
                // или из отношений options/items, если есть
                if ($this->relationLoaded('options')) {
                    $opt = [];
                    foreach ($this->options as $o) {
                        $vals = $o->items->pluck('name')->filter()->unique()->values()->all();
                        if ($vals)
                            $opt[] = ['name' => $o->name, 'values' => $vals];
                    }
                    $options = $opt;
                }
            }
        }

        // 5) variants: без картинок! только attrs, sku, stock, price...
        $variants = [];
        if ($this->relationLoaded('variants')) {
            $variants = $this->variants->map(function ($v) {
                return [
                    'id' => $v->id,
                    'sku' => $v->sku,
                    'barcode' => $v->barcode,
                    'price' => (int) $v->price,
                    'stock' => (int) $v->stock,
                    'attrs' => (array) $v->attrs, // {"Color":"Black","Size":"41"}
                ];
            })->values()->all();
        }

        // 6) Базовый ответ в нашей целевой схеме
        $data = [
            'id' => $this->id,
            'name' => $this->name,                 // или $this->name_ru, если нужно
            'description' => $this->desc ?? null,
            'gender' => $this->gender,
            'label' => $this->label,
            'price' => (int) $this->price,
            'discount' => (int) $this->getDiscountNumber(),
            'image' => $imageUrl,
            'gallery' => $gallery,
            'options' => $options,
            'colorImages' => $colorImages,
            'variants' => $variants,
            // остальное можно оставить (на время) — совместимость с фронтом/админкой
            'tags' => TagResource::collection($this->whenLoaded('tags', $this->tags)),
            'brand' => BrandResource::make($this->whenLoaded('brand', $this->brand)),
            'views' => $this->views,
        ];

        // ====== Наследие (если фронт ещё ждёт эти поля; можно удалить позже) ======
        // ВНИМАНИЕ: они не относятся к Product v1, оставляем лишь для обратной совместимости
        $data['colors'] = ColorOptionResource::collection($this->whenLoaded('options', $this->options));
        $data['colors2'] = ColorResource::collection($this->whenLoaded('colors', $this->colors));
        $data['sizes'] = SizeResource::collection($this->whenLoaded('sizes', $this->sizes));
        $data['images'] = ImageResource::collection($this->whenLoaded('images', $this->images));
        $data['options_legacy'] = OptionResource::collection($this->whenLoaded('product_options', $this->product_options));

        return $data;
    }
}