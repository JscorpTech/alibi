<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class PosController extends Controller
{
    public function __construct(protected InventoryService $inventory) {}

    public function index()
    {
        return view('admin.pos.index');
    }

    /**
     * Скан: code может быть штрихкод варианта, SKU варианта, SKU модели или числовой ID.
     */
    public function scan(Request $request)
    {
        $code = trim((string)$request->input('barcode', ''));

        if ($code === '') {
            return response()->json(['ok' => false, 'message' => 'Пустой код']);
        }

        // 1) Вариант по BARCODE (product_sizes.barcode)
        if ($ps = DB::table('product_sizes')->where('barcode', $code)->first()) {
            $p = Product::withoutGlobalScopes()->find($ps->product_id);
            if (!$p) return response()->json(['ok' => false, 'message' => 'Товар не найден']);

            return response()->json([
                'ok' => true,
                'product' => [
                    'id'      => (int)$p->id,
                    'size_id' => (int)$ps->size_id,
                    'name'    => $p->name_ru ?? $p->name ?? ('#'.$p->id),
                    'price'   => (int)($p->price ?? 0),
                    'sku'     => $ps->sku,           // SKU варианта
                    'barcode' => $ps->barcode,       // штрихкод варианта
                ],
            ]);
        }

        // 2) Вариант по SKU (product_sizes.sku)
        if ($ps = DB::table('product_sizes')->where('sku', $code)->first()) {
            $p = Product::withoutGlobalScopes()->find($ps->product_id);
            if (!$p) return response()->json(['ok' => false, 'message' => 'Товар не найден']);

            return response()->json([
                'ok' => true,
                'product' => [
                    'id'      => (int)$p->id,
                    'size_id' => (int)$ps->size_id,
                    'name'    => $p->name_ru ?? $p->name ?? ('#'.$p->id),
                    'price'   => (int)($p->price ?? 0),
                    'sku'     => $ps->sku,
                    'barcode' => $ps->barcode,
                ],
            ]);
        }

        // 3) SKU модели (products.sku)
        if ($p = Product::withoutGlobalScopes()->where('sku', $code)->first()) {
            return response()->json([
                'ok' => true,
                'product' => [
                    'id'      => (int)$p->id,
                    'size_id' => null,  // размер не определён
                    'name'    => $p->name_ru ?? $p->name ?? ('#'.$p->id),
                    'price'   => (int)($p->price ?? 0),
                    'sku'     => $p->sku,
                    'barcode' => null,
                ],
            ]);
        }

        // 4) Last resort: числовой ID продукта
        if (ctype_digit($code)) {
            if ($p = Product::withoutGlobalScopes()->find((int)$code)) {
                return response()->json([
                    'ok' => true,
                    'product' => [
                        'id'      => (int)$p->id,
                        'size_id' => null,
                        'name'    => $p->name_ru ?? $p->name ?? ('#'.$p->id),
                        'price'   => (int)($p->price ?? 0),
                        'sku'     => $p->sku,
                        'barcode' => null,
                    ],
                ]);
            }
        }

        return response()->json(['ok' => false, 'message' => 'Товар не найден']);
    }

    /**
     * Списание: поддерживает barcode/sku варианта, sku модели, числовой id.
     * Можно дополнительно передать size_id (если выбрали размер вручную).
     */
    public function checkout(Request $request)
    {
        $data = $request->validate([
            'barcode'           => 'required|string',   // тут приходит barcode ИЛИ sku ИЛИ id
            'stock_location_id' => 'required|integer',
            'qty'               => 'required|integer|min:1',
            'size_id'           => 'nullable|integer',
        ]);

        $code  = trim($data['barcode']);
        $sizeId = $data['size_id'] ?? null;
        $product = null;

        // variant by barcode
        if (!$product) {
            $ps = DB::table('product_sizes')->where('barcode', $code)->first();
            if ($ps) {
                $product = Product::withoutGlobalScopes()->find($ps->product_id);
                $sizeId  = $ps->size_id;
            }
        }
        // variant by sku
        if (!$product) {
            $ps = DB::table('product_sizes')->where('sku', $code)->first();
            if ($ps) {
                $product = Product::withoutGlobalScopes()->find($ps->product_id);
                $sizeId  = $ps->size_id;
            }
        }
        // product by sku
        if (!$product) {
            $product = Product::withoutGlobalScopes()->where('sku', $code)->first();
        }
        // product by numeric id
        if (!$product && ctype_digit($code)) {
            $product = Product::withoutGlobalScopes()->find((int)$code);
        }

        if (!$product) {
            return response()->json(['ok' => false, 'message' => 'Товар не найден']);
        }

        // Списание по складу
        $this->inventory->sell(
            productId: $product->id,
            sizeId: $sizeId, // null — безразмерный
            locationId: (int)$data['stock_location_id'],
            qty: (int)$data['qty'],
            meta: ['pos' => true, 'user' => auth()->id()]
        );

        // Синхронно уменьшим быстрый остаток в pivot, если есть размер
        if ($sizeId) {
            DB::table('product_sizes')
                ->where('product_id', $product->id)
                ->where('size_id', $sizeId)
                ->decrement('count', (int)$data['qty']);
        }

        return response()->json(['ok' => true, 'message' => 'Продажа успешно проведена']);
    }
}