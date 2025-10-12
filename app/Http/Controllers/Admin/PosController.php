<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class PosController extends Controller
{
    public function __construct(protected InventoryService $inventory)
    {
    }

    public function index()
    {
        return view('admin.pos.index');
    }


    protected function searchBySkuVariant(string $sku): array
    {
        $rows = \App\Models\Variant::query()
            ->whereNotNull('sku')
            ->where('sku', 'ILIKE', '%' . $sku . '%')
            ->with(['product:id,price,name_ru,name,image']) // если нужны поля
            ->limit(20)
            ->get(['id', 'product_id', 'attrs', 'sku']);

        return $rows->map(function ($v) {
            $size = (string) data_get($v->attrs, 'Size', '');
            $color = (string) data_get($v->attrs, 'Color', '');

            return [
                'id' => (int) $v->product_id,
                'name' => ($v->product?->name_ru ?? $v->product?->name ?? ('Товар #' . $v->product_id))
                    . ($size ? (' • ' . $size) : '')
                    . ($color ? (' • ' . $color) : ''),
                'price' => (int) ($v->product->price ?? 0),
                'image' => $this->imageUrl((object) ['image' => $v->product?->image]),
                'sizes' => [], // при клике сразу скан/добавление точного варианта
                'sku' => $v->sku,
                'variant_id' => (int) $v->id,
            ];
        })->toArray();
    }
    /**
     * Скан: code может быть штрихкод варианта, SKU варианта, SKU модели или числовой ID.
     */
    public function scan(Request $request)
    {
        $code = trim((string) $request->input('barcode', ''));
        if ($code === '') {
            return response()->json(['ok' => false, 'message' => 'Пустой код']);
        }

        // 1) Ищем ВАРИАНТ по точному совпадению barcode или sku
        $v = Variant::query()
            ->where('barcode', $code)
            ->orWhere('sku', $code)
            ->first(['id', 'product_id', 'attrs', 'sku', 'barcode', 'image']);

        if (!$v) {
            return response()->json(['ok' => false, 'message' => 'Вариант не найден']);
        }

        // 2) Товар (для имени/цены)
        $p = Product::withoutGlobalScopes()->find($v->product_id);
        if (!$p) {
            return response()->json(['ok' => false, 'message' => 'Товар не найден']);
        }

        // 3) Распакуем оси варианта -> получим ID из справочников
        $sizeName = (string) data_get($v->attrs, 'Size', '');
        $colorName = (string) data_get($v->attrs, 'Color', '');

        $sizeId = $sizeName !== '' ? Size::where('name', $sizeName)->value('id') : null;
        $colorId = $colorName !== '' ? Color::where('name', $colorName)->value('id') : null;

        // 4) Нормализуем картинку (если нужна)
        $image = null;
        if (!empty($v->image)) {
            $image = str_starts_with($v->image, 'http') ? $v->image : asset('storage/' . ltrim(preg_replace('#^/?public/#', '', $v->image), '/'));
        } elseif (!empty($p->image)) {
            $image = str_starts_with($p->image, 'http') ? $p->image : asset('storage/' . ltrim(preg_replace('#^/?public/#', '', $p->image), '/'));
        }

        return response()->json([
            'ok' => true,
            'product' => [
                'id' => (int) $p->id,
                'variant_id' => (int) $v->id,
                'size_id' => $sizeId ? (int) $sizeId : null,
                'color_id' => $colorId ? (int) $colorId : null,
                'name' => $p->name_ru ?? $p->name ?? ('#' . $p->id),
                'price' => (int) ($p->price ?? 0), // если есть price у варианта — можно подменить здесь
                'sku' => $v->sku,
                'barcode' => $v->barcode,
                'image' => $image,
            ],
        ]);
    }

    protected function searchByVariantCodeLike(string $q): array
{
    $q = trim($q);
    if ($q === '') return [];

    $variants = \App\Models\Variant::query()
        ->where(function ($w) use ($q) {
            $w->where('barcode', 'ILIKE', '%' . $q . '%')
              ->orWhere('sku',     'ILIKE', '%' . $q . '%');
        })
        ->with(['product' => function ($p) {
            $p->select(['id','price',
                \Schema::hasColumn('products','name_ru') ? 'name_ru' : 'name',
                \Schema::hasColumn('products','image')   ? 'image'   : \DB::raw('NULL as image'),
            ]);
        }])
        ->limit(20)
        ->get(['id','product_id','attrs','sku','barcode','image']);

    return $variants->map(function ($v) {
        $p = $v->product;

        $size  = (string) data_get($v->attrs,'Size','');
        $color = (string) data_get($v->attrs,'Color','');

        // нормализуем картинку: сначала у варианта, потом у товара
        $img = null;
        if (!empty($v->image)) {
            $img = str_starts_with($v->image, 'http')
                ? $v->image
                : asset('storage/' . ltrim(preg_replace('#^/?public/#', '', $v->image), '/'));
        } elseif (!empty($p?->image)) {
            $img = str_starts_with($p->image, 'http')
                ? $p->image
                : asset('storage/' . ltrim(preg_replace('#^/?public/#', '', $p->image), '/'));
        }

        // id справочников
        $sizeId  = $size  !== '' ? \App\Models\Size::where('name',$size)->value('id')   : null;
        $colorId = $color !== '' ? \App\Models\Color::where('name',$color)->value('id') : null;

        return [
            'id'         => (int) ($p?->id ?? 0),
            'variant_id' => (int) $v->id,
            'name'       => ($p?->name_ru ?? $p?->name ?? ('Товар #' . ($p?->id ?? ''))),
            'price'      => (int) ($p?->price ?? 0), // при желании можно подменить ценой варианта
            'image'      => $img,
            'sku'        => $v->sku,
            'barcode'    => $v->barcode,
            // для UI (чипсы/подписи)
            'size_id'    => $sizeId ? (int) $sizeId : null,
            'size_name'  => $size ?: null,
            'color_id'   => $colorId ? (int) $colorId : null,
            'color_name' => $color ?: null,
            // подсказка, что это именно попадание по коду
            '_hit'       => 'code',
        ];
    })->toArray();
}
    /**
     * Списание: поддерживает barcode/sku варианта, sku модели, числовой id.
     * Можно дополнительно передать size_id (если выбрали размер вручную).
     */
    public function checkout(Request $request)
    {
        $data = $request->validate([
            'barcode' => 'required|string',   // тут приходит barcode ИЛИ sku ИЛИ id
            'stock_location_id' => 'required|integer',
            'qty' => 'required|integer|min:1',
            'size_id' => 'nullable|integer',
        ]);

        $code = trim($data['barcode']);
        $sizeId = $data['size_id'] ?? null;
        $product = null;

        // variant by barcode
        if (!$product) {
            $ps = DB::table('product_sizes')->where('barcode', $code)->first();
            if ($ps) {
                $product = Product::withoutGlobalScopes()->find($ps->product_id);
                $sizeId = $ps->size_id;
            }
        }
        // variant by sku
        if (!$product) {
            $ps = DB::table('product_sizes')->where('sku', $code)->first();
            if ($ps) {
                $product = Product::withoutGlobalScopes()->find($ps->product_id);
                $sizeId = $ps->size_id;
            }
        }
        // product by sku
        if (!$product) {
            $product = Product::withoutGlobalScopes()->where('sku', $code)->first();
        }
        // product by numeric id
        if (!$product && ctype_digit($code)) {
            $product = Product::withoutGlobalScopes()->find((int) $code);
        }

        if (!$product) {
            return response()->json(['ok' => false, 'message' => 'Товар не найден']);
        }

        // Списание по складу
        $this->inventory->sell(
            productId: $product->id,
            sizeId: $sizeId, // null — безразмерный
            locationId: (int) $data['stock_location_id'],
            qty: (int) $data['qty'],
            meta: ['pos' => true, 'user' => auth()->id()]
        );

        // Синхронно уменьшим быстрый остаток в pivot, если есть размер
        if ($sizeId) {
            DB::table('product_sizes')
                ->where('product_id', $product->id)
                ->where('size_id', $sizeId)
                ->decrement('count', (int) $data['qty']);
        }

        return response()->json(['ok' => true, 'message' => 'Продажа успешно проведена']);
    }
}