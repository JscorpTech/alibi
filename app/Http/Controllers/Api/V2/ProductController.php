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
        // 1) Тянем продукт
        $product = DB::table('products')
            ->select([
                'id',
                DB::raw("COALESCE(name_ru, '') as name"),
                'price',
                DB::raw("NULLIF(desc_ru, '') as description"),
                DB::raw("NULLIF(image, '') as image"),
                DB::raw("COALESCE(is_active, TRUE) as active"),
                // на случай синтет. вариантов
                DB::raw("NULLIF(sku, '') as p_sku"),
                DB::raw("NULLIF(barcode, '') as p_barcode"),
            ])
            ->where('id', $id)
            ->first();

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        // 2) Галерея картинок товара
        $images = [];
        if (Schema::hasTable('product_images')) {
            $imgCol = Schema::hasColumn('product_images', 'url') ? 'url' : (Schema::hasColumn('product_images', 'path') ? 'path' : null);
            if ($imgCol) {
                $images = DB::table('product_images')
                    ->where('product_id', $id)
                    ->when(Schema::hasColumn('product_images', 'position'), fn($q) => $q->orderBy('position'))
                    ->pluck($imgCol)
                    ->filter()
                    ->values()
                    ->all();
            }
        }
        if (empty($images) && $product->image) {
            $images = [(string) $product->image];
        }
        // Абсолютные URL
        $images = array_map([$this, 'toUrl'], $images);

        // 3) Реальные variants?
        $variants = collect();
        if (Schema::hasTable('variants')) {
            $variants = DB::table('variants')
                ->where('product_id', $id)
                ->orderBy('id')
                ->get();
        }

        $optionsList = [];
        $variantsOut = collect();

        if ($variants->isNotEmpty()) {
            // 3A) ВЕТКА: есть реальные variants → собираем options из attrs
            $optionsMap = [];
            foreach ($variants as $v) {
                $attrs = $this->safeJson($v->attrs);
                foreach ($attrs as $k => $val) {
                    $k = (string) $k;
                    $val = (string) $val;
                    $optionsMap[$k] = $optionsMap[$k] ?? [];
                    if (!in_array($val, $optionsMap[$k], true)) {
                        $optionsMap[$k][] = $val;
                    }
                }
            }
            foreach ($optionsMap as $name => $values) {
                $optionsList[] = ['name' => $name, 'values' => array_values($values)];
            }

            $variantsOut = $variants->map(function ($v) {
                $attrs = $this->safeJson($v->attrs);
                $stock = (int) ($v->stock ?? 0);
                return [
                    'id' => $v->id,
                    'sku' => $v->sku ?? null,
                    'barcode' => $v->barcode ?? null,
                    'attrs' => $attrs,
                    'price' => (int) ($v->price ?? 0),
                    'stock' => $stock,
                    'image' => $this->toUrl($v->image ?? null),
                    'available' => $stock > 0,
                ];
            })->values();

        } else {
            // 3B) ВЕТКА: variants нет → синтезируем из product_sizes × product_colors
            // sizes
            $sizesRows = collect();
            if (Schema::hasTable('product_sizes')) {
                $sizesHasDict = Schema::hasTable('sizes');
                $sizesRows = DB::table('product_sizes as ps')
                    ->when($sizesHasDict, fn($q) => $q->leftJoin('sizes as s', 's.id', '=', 'ps.size_id'))
                    ->where('ps.product_id', $id)
                    ->select([
                        'ps.size_id',
                        DB::raw('COALESCE(ps.count,0) as stock'),
                        DB::raw($sizesHasDict ? 's.name as size_name' : 'NULL as size_name'),
                    ])
                    ->orderByRaw($sizesHasDict ? 's.name NULLS LAST' : 'ps.size_id')
                    ->get();
            }

            // colors
            $colorRows = collect();
            if (Schema::hasTable('product_colors') && Schema::hasTable('colors')) {
                $colorRows = DB::table('product_colors as pc')
                    ->join('colors as c', 'c.id', '=', 'pc.color_id')
                    ->where('pc.product_id', $id)
                    ->select([
                        'pc.id as pc_id',
                        'pc.color_id',
                        'c.name as color_name',
                    ])
                    ->orderBy('c.name')
                    ->get();
            }

            // фотки по цвету (если таблица есть)
            $colorImageByColorId = [];
            if ($colorRows->isNotEmpty() && Schema::hasTable('product_color_images')) {
                $colorImageByColorId = DB::table('product_color_images as pci')
                    ->join('product_colors as pc', 'pc.id', '=', 'pci.product_color_id')
                    ->where('pc.product_id', $id)
                    ->select('pc.color_id', 'pci.path')
                    ->get()
                    ->groupBy('color_id')
                    ->map(fn($g) => $this->toUrl(optional($g->first())->path))
                    ->toArray();
            }

            // options
            if ($sizesRows->isNotEmpty()) {
                $optionsList[] = [
                    'name' => 'Size',
                    'values' => $sizesRows->pluck('size_name')->filter()->unique()->values()->all(),
                ];
            }
            if ($colorRows->isNotEmpty()) {
                $optionsList[] = [
                    'name' => 'Color',
                    'values' => $colorRows->pluck('color_name')->filter()->unique()->values()->all(),
                ];
            }

            // синтез вариантов
            $baseImg = $images[0] ?? $this->toUrl($product->image ?? null);

            if ($sizesRows->isNotEmpty() && $colorRows->isNotEmpty()) {
                // Size × Color
                foreach ($sizesRows as $s) {
                    foreach ($colorRows as $c) {
                        $stock = (int) ($s->stock ?? 0); // у тебя сток на уровне размера
                        $img = $colorImageByColorId[$c->color_id] ?? $baseImg;

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
                            'image' => $img,
                            'available' => $stock > 0,
                        ]);
                    }
                }
            } elseif ($sizesRows->isNotEmpty()) {
                // только размеры
                foreach ($sizesRows as $s) {
                    $stock = (int) ($s->stock ?? 0);
                    $variantsOut->push([
                        'id' => null,
                        'sku' => $product->p_sku ?? null,
                        'barcode' => $product->p_barcode ?? null,
                        'attrs' => ['Size' => (string) ($s->size_name ?? '')],
                        'price' => (int) ($product->price ?? 0),
                        'stock' => $stock,
                        'image' => $baseImg,
                        'available' => $stock > 0,
                    ]);
                }
            } elseif ($colorRows->isNotEmpty()) {
                // только цвета
                foreach ($colorRows as $c) {
                    $img = $colorImageByColorId[$c->color_id] ?? $baseImg;
                    $variantsOut->push([
                        'id' => null,
                        'sku' => $product->p_sku ?? null,
                        'barcode' => $product->p_barcode ?? null,
                        'attrs' => ['Color' => (string) ($c->color_name ?? '')],
                        'price' => (int) ($product->price ?? 0),
                        'stock' => 0, // если нет стока по цвету — 0
                        'image' => $img,
                        'available' => false,
                    ]);
                }
            }
        }

        $totalStock = (int) $variantsOut->sum('stock');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (int) $product->price,
                'description' => $product->description,
                'images' => $images,
                'options' => $optionsList,
                'variants' => $variantsOut->values(),
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