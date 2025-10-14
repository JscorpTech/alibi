<?php

namespace App\Http\Resources\Api\Product;

use App\Http\Resources\Api\BrandResource;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Http\Resources\Api\TagResource;

class ProductResource extends JsonResource
{
    use BaseResource;

    /* ---------- helpers ---------- */

    private function urlize(?string $path): ?string
    {
        if (!$path)
            return null;
        if (Str::startsWith($path, ['http://', 'https://']))
            return $path;
        return Storage::url($path);
    }

    private function toArraySafe($val): array
    {
        if (is_array($val))
            return $val;
        if (is_object($val))
            return (array) $val;
        if (!is_string($val) || $val === '')
            return [];
        try {
            $arr = json_decode($val, true, flags: JSON_THROW_ON_ERROR);
            return is_array($arr) ? $arr : [];
        } catch (\Throwable) {
            return [];
        }
    }

    public function toArray(Request $request): array
    {
        /* 1) cover */
        $imageUrl = $this->urlize($this->image ?? null);

        /* 2) gallery → абсолютные URL */
        $gallery = $this->toArraySafe($this->gallery ?? null);
        if (empty($gallery) && Schema::hasTable('product_images')) {
            $imgCol = Schema::hasColumn('product_images', 'url')
                ? 'url'
                : (Schema::hasColumn('product_images', 'path') ? 'path' : null);
            if ($imgCol) {
                $gallery = DB::table('product_images')
                    ->where('product_id', $this->id)
                    ->when(Schema::hasColumn('product_images', 'position'), fn($q) => $q->orderBy('position'))
                    ->pluck($imgCol)->filter()->values()->all();
            }
        }
        if (empty($gallery) && !empty($this->image)) {
            $gallery = [(string) $this->image];
        }
        $gallery = array_map(fn($u) => $this->urlize(is_string($u) ? $u : (string) $u), $gallery);
        $cover = $gallery[0] ?? $imageUrl;

        /* 3) colorImages → нормализуем + абсолютные URL, с fallback на legacy-таблицы */
        $colorImages = [];
        foreach ($this->toArraySafe($this->color_images ?? null) as $color => $val) {
            $arr = is_string($val) ? [$val] : array_values((array) $val);
            $colorImages[$color] = array_map(fn($x) => $this->urlize(is_string($x) ? $x : (string) $x), $arr);
        }
        if (empty($colorImages) && Schema::hasTable('product_colors') && Schema::hasTable('colors')) {
            $map = DB::table('product_colors as pc')
                ->join('colors as c', 'c.id', '=', 'pc.color_id')
                ->where('pc.product_id', $this->id)
                ->pluck('pc.id', 'c.name');
            if ($map && Schema::hasTable('product_color_images')) {
                foreach ($map as $colorName => $pcId) {
                    $paths = DB::table('product_color_images')
                        ->where('product_color_id', $pcId)
                        ->when(Schema::hasColumn('product_color_images', 'position'), fn($q) => $q->orderBy('position'))
                        ->pluck('path')->filter()->values()->all();
                    if ($paths) {
                        $colorImages[$colorName] = array_map(fn($p) => $this->urlize($p), $paths);
                    }
                }
            }
        }

        /* 4) variants: БЕЗ image; price fallback на product.price */
        $variants = [];
        $productPrice = (int) ($this->price ?? 0);
        if ($this->relationLoaded('variants')) {
            $variants = $this->variants->map(function ($v) use ($productPrice) {
                $price = (int) ($v->price ?? 0);
                if ($price <= 0)
                    $price = $productPrice;
                return [
                    'id' => $v->id,
                    'sku' => $v->sku,
                    'barcode' => $v->barcode,
                    'attrs' => (array) $v->attrs, // {"Color":"Black","Size":"41"}
                    'price' => $price,
                    'stock' => (int) ($v->stock ?? 0),
                ];
            })->values()->all();
        }

        /* 5) options: JSON products.options → иначе собираем из variants.attrs */
        $options = $this->options ?? [];
        if (empty($options) && $this->relationLoaded('variants') && $this->variants->isNotEmpty()) {
            $map = [];
            foreach ($this->variants as $v) {
                foreach ((array) $v->attrs as $name => $val) {
                    $name = (string) $name;
                    $val = (string) $val;
                    $map[$name] = $map[$name] ?? [];
                    if (!in_array($val, $map[$name], true))
                        $map[$name][] = $val;
                }
            }
            $options = [];
            foreach ($map as $name => $values) {
                $options[] = ['name' => $name, 'values' => array_values($values)];
            }
        }

        /* 6) totalStock */
        $totalStock = 0;
        foreach ($variants as $v)
            $totalStock += (int) ($v['stock'] ?? 0);

        /* 7) Итог — ЧИСТЫЙ Product v1 без legacy */
        return [
            'id' => $this->id,
            'name' => (string) ($this->name ?? $this->name_ru ?? ''),
            'description' => $this->desc ?? $this->desc_ru ?? null,
            'price' => (int) $this->price,
            'image' => $cover,
            'gallery' => $gallery,
            'options' => $options,
            'colorImages' => $colorImages,
            'variants' => $variants,
            'totalStock' => (int) $totalStock,
            'active' => (bool) ($this->is_active ?? true),

            // мета
            'tags' => $this->relationLoaded('tags') ? TagResource::collection($this->tags) : [],
            'brand' => $this->brand ? BrandResource::make($this->brand) : null,
            'views' => (int) $this->views,
        ];
    }
}