<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * GET /api/v2/products
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 20);

        $products = DB::table('products')
            ->select([
                'id',
                DB::raw("COALESCE(name_ru, '') as name"),
                'price',
                DB::raw("NULLIF(image,'') as thumbnail"),
                DB::raw("COALESCE(is_active, TRUE) as active"),
            ])
            ->when(Schema::hasColumn('products', 'is_active'), fn($q) => $q->where('is_active', true))
            ->orderByDesc('id')
            ->paginate($perPage);

        // привести thumbnail к строке/NULL и сделать абсолютным URL
        $products->getCollection()->transform(function ($p) {
            $thumb = $p->thumbnail ? (string) $p->thumbnail : null;
            $p->thumbnail = $this->toUrl($thumb);
            return $p;
        });

        return response()->json(['success' => true, 'data' => $products]);
    }

    /**
     * GET /api/v2/products/{id}
     */
    public function view($id)
    {
        // 1) Продукт: читаем основные поля + JSON-колонки (если есть)
        $hasGallery = Schema::hasColumn('products', 'gallery');
        $hasOptionsJson = Schema::hasColumn('products', 'options');
        $hasColorImages = Schema::hasColumn('products', 'color_images');

        $select = [
            'id',
            DB::raw("COALESCE(name_ru, '') as name"),
            'price',
            DB::raw("NULLIF(desc_ru, '') as description"),
            DB::raw("NULLIF(image, '') as image"),
            DB::raw("COALESCE(is_active, TRUE) as active"),
            DB::raw("NULLIF(sku, '') as p_sku"),
            DB::raw("NULLIF(barcode, '') as p_barcode"),
        ];
        if ($hasGallery)
            $select[] = 'gallery';
        if ($hasOptionsJson)
            $select[] = 'options';
        if ($hasColorImages)
            $select[] = 'color_images';

        $product = DB::table('products')->select($select)->where('id', $id)->first();
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        // helpers
        $toArray = function ($val) {
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
        };

        // 2) gallery: сначала из JSON-колонки, иначе из product_images, иначе [image]
        $gallery = [];
        if ($hasGallery)
            $gallery = $toArray($product->gallery);

        if (empty($gallery) && Schema::hasTable('product_images')) {
            $imgCol = Schema::hasColumn('product_images', 'url') ? 'url'
                : (Schema::hasColumn('product_images', 'path') ? 'path' : null);
            if ($imgCol) {
                $gallery = DB::table('product_images')
                    ->where('product_id', $id)
                    ->when(Schema::hasColumn('product_images', 'position'), fn($q) => $q->orderBy('position'))
                    ->pluck($imgCol)->filter()->values()->all();
            }
        }
        if (empty($gallery) && !empty($product->image)) {
            $gallery = [(string) $product->image];
        }
        // абсолютные URL
        $gallery = array_map([$this, 'toUrl'], $gallery);
        $cover = $gallery[0] ?? $this->toUrl($product->image ?? null);

        // 3) colorImages: JSON-колонка, а если пусто – соберём из legacy product_color_images
        $colorImages = [];
        if ($hasColorImages) {
            $raw = $toArray($product->color_images ?? null);
            foreach ($raw as $color => $val) {
                if (is_string($val)) {
                    $colorImages[$color] = [$val];
                } else {
                    $colorImages[$color] = array_values(array_map(
                        fn($x) => is_string($x) ? $x : (string) $x,
                        (array) $val
                    ));
                }
            }
        }
        if (empty($colorImages) && Schema::hasTable('product_colors') && Schema::hasTable('colors')) {
            // cover по цвету
            $map = DB::table('product_colors as pc')
                ->join('colors as c', 'c.id', '=', 'pc.color_id')
                ->where('pc.product_id', $id)
                ->pluck('pc.id', 'c.name'); // ["Black" => pc_id, ...]
            if ($map && Schema::hasTable('product_color_images')) {
                foreach ($map as $colorName => $pcId) {
                    $paths = DB::table('product_color_images')
                        ->where('product_color_id', $pcId)
                        ->when(Schema::hasColumn('product_color_images', 'position'), fn($q) => $q->orderBy('position'))
                        ->pluck('path')->filter()->values()->all();
                    if ($paths) {
                        $colorImages[$colorName] = array_map([$this, 'toUrl'], $paths);
                    }
                }
            }
        }

        // 4) options: из JSON-колонки, иначе собираем из variants.attrs или legacy-таблиц
        $options = [];
        if ($hasOptionsJson)
            $options = $toArray($product->options ?? null);

        // variants из таблицы
        $variants = collect();
        if (Schema::hasTable('variants')) {
            $variants = DB::table('variants')
                ->where('product_id', $id)
                ->orderBy('id')->get();
        }

        if (empty($options)) {
            if ($variants->isNotEmpty()) {
                // из attrs
                $map = [];
                foreach ($variants as $v) {
                    $attrs = $toArray($v->attrs);
                    foreach ($attrs as $k => $val) {
                        $k = (string) $k;
                        $val = (string) $val;
                        $map[$k] = $map[$k] ?? [];
                        if (!in_array($val, $map[$k], true))
                            $map[$k][] = $val;
                    }
                }
                foreach ($map as $name => $values) {
                    $options[] = ['name' => $name, 'values' => array_values($values)];
                }
            } else {
                // legacy sizes/colors
                if (Schema::hasTable('product_sizes')) {
                    $hasDict = Schema::hasTable('sizes');
                    $sizes = DB::table('product_sizes as ps')
                        ->when($hasDict, fn($q) => $q->leftJoin('sizes as s', 's.id', '=', 'ps.size_id'))
                        ->where('ps.product_id', $id)
                        ->pluck($hasDict ? 's.name' : 'ps.size_id')
                        ->filter()->unique()->values()->all();
                    if ($sizes)
                        $options[] = ['name' => 'Size', 'values' => $sizes];
                }
                if (Schema::hasTable('product_colors') && Schema::hasTable('colors')) {
                    $colors = DB::table('product_colors as pc')
                        ->join('colors as c', 'c.id', '=', 'pc.color_id')
                        ->where('pc.product_id', $id)
                        ->pluck('c.name')->filter()->unique()->values()->all();
                    if ($colors)
                        $options[] = ['name' => 'Color', 'values' => $colors];
                }
            }
        }

        // 5) variantsOut: БЕЗ 'image' (фото только на уровне цвета)
        $variantsOut = collect();
        if ($variants->isNotEmpty()) {
            $variantsOut = $variants->map(function ($v) use ($toArray) {
                $attrs = $toArray($v->attrs);
                $stock = (int) ($v->stock ?? 0);
                return [
                    'id' => $v->id,
                    'sku' => $v->sku ?? null,
                    'barcode' => $v->barcode ?? null,
                    'attrs' => $attrs,                 // {"Color":"Black","Size":"41"}
                    'price' => (int) ($v->price ?? 0),
                    'stock' => $stock,
                    // 'image' НЕТ по нашей спецификации
                ];
            })->values();
        } else {
            // синтез (если реальных variants нет) — тоже без image
            $sizesRows = collect();
            if (Schema::hasTable('product_sizes')) {
                $hasDict = Schema::hasTable('sizes');
                $sizesRows = DB::table('product_sizes as ps')
                    ->when($hasDict, fn($q) => $q->leftJoin('sizes as s', 's.id', '=', 'ps.size_id'))
                    ->where('ps.product_id', $id)
                    ->select([
                        'ps.size_id',
                        DB::raw('COALESCE(ps.count,0) as stock'),
                        DB::raw($hasDict ? 's.name as size_name' : 'NULL as size_name'),
                    ])->get();
            }
            $colorRows = collect();
            if (Schema::hasTable('product_colors') && Schema::hasTable('colors')) {
                $colorRows = DB::table('product_colors as pc')
                    ->join('colors as c', 'c.id', '=', 'pc.color_id')
                    ->where('pc.product_id', $id)
                    ->select(['pc.color_id', 'c.name as color_name'])->get();
            }

            if ($sizesRows->isNotEmpty() && $colorRows->isNotEmpty()) {
                foreach ($sizesRows as $s) {
                    foreach ($colorRows as $c) {
                        $stock = (int) ($s->stock ?? 0);
                        $variantsOut->push([
                            'id' => null,
                            'sku' => $product->p_sku ?? null,
                            'barcode' => $product->p_barcode ?? null,
                            'attrs' => [
                                'Size' => (string) ($s->size_name ?? ''),
                                'Color' => (string) ($c->color_name ?? ''),
                            ],
                            'price' => (int) ($product->price ?? 0),
                            'stock' => $stock,
                        ]);
                    }
                }
            } elseif ($sizesRows->isNotEmpty()) {
                foreach ($sizesRows as $s) {
                    $stock = (int) ($s->stock ?? 0);
                    $variantsOut->push([
                        'id' => null,
                        'sku' => $product->p_sku ?? null,
                        'barcode' => $product->p_barcode ?? null,
                        'attrs' => ['Size' => (string) ($s->size_name ?? '')],
                        'price' => (int) ($product->price ?? 0),
                        'stock' => $stock,
                    ]);
                }
            } elseif ($colorRows->isNotEmpty()) {
                foreach ($colorRows as $c) {
                    $variantsOut->push([
                        'id' => null,
                        'sku' => $product->p_sku ?? null,
                        'barcode' => $product->p_barcode ?? null,
                        'attrs' => ['Color' => (string) ($c->color_name ?? '')],
                        'price' => (int) ($product->price ?? 0),
                        'stock' => 0,
                    ]);
                }
            }
        }

        // 6) totalStock по сумме вариантов
        $totalStock = (int) $variantsOut->sum('stock');

        // 7) Итоговый ответ строго под Product v1
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (int) $product->price,
                'description' => $product->description,
                'image' => $cover,        // дефолтная обложка (gallery[0] или image)
                'gallery' => $gallery,      // общая галерея
                'options' => $options,      // [{name:"Color",values:[...]}, {name:"Size",values:[...]}]
                'colorImages' => $colorImages,  // {"Black": [".../black1.webp","..."], ...}
                'variants' => $variantsOut->values(), // без image
                'totalStock' => $totalStock,
                'active' => (bool) $product->active,
            ],
        ]);
    }

    /** Безопасный json_decode → всегда массив */
    private function safeJson($value): array
    {
        if (is_array($value))
            return $value;
        if (is_object($value))
            return (array) $value;
        if (!is_string($value) || $value === '')
            return [];
        try {
            $arr = json_decode($value, true, flags: JSON_THROW_ON_ERROR);
            return is_array($arr) ? $arr : [];
        } catch (\Throwable) {
            return [];
        }
    }

    /** Делает абсолютный URL для локального пути (storage/...), иначе возвращает как есть */
    private function toUrl(?string $path): ?string
    {
        if (!$path)
            return null;
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }
        // если уже начинается с storage/ или products/ — считаем локальным
        return asset(Str::startsWith($path, ['storage/', 'products/']) ? $path : ('storage/' . $path));
    }
}