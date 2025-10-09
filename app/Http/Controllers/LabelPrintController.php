<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class LabelPrintController extends Controller
{
    /**
     * 🏷️ Печать для одного товара:
     * Всегда печатает по размерам (product_sizes.barcode, count, sku)
     */
    public function one(Product $product)
    {
        // Тянем размеры и данные pivot
        $product->load(['sizes' => function ($q) {
            $q->withPivot(['count', 'sku', 'barcode']);
        }]);

        $items = [];

        foreach ($product->sizes as $size) {
            $stock = (int) ($size->pivot->count ?? 0);
            if ($stock <= 0 || empty($size->pivot->barcode)) {
                continue; // пропускаем если нет остатков или штрихкода
            }

            $items[] = [
                'name'    => $product->name_ru ?? $product->name ?? ('#' . $product->id),
                'sku'     => $size->pivot->sku ?? ($product->sku ? $product->sku . '-' . $size->name : null),
                'size'    => $size->name,
                'price'   => (int) ($product->price ?? 0),
                'barcode' => $size->pivot->barcode,   // ✅ только по размеру
                'repeat'  => $stock,                  // столько этикеток печатаем
            ];
        }

        // Если вообще нет размеров → ничего не печатаем
        if (empty($items)) {
            return back()->with('error', 'Нет размеров с остатком или штрихкодом');
        }

        return view('print.barcodes_by_sizes', compact('items'));
    }

    /**
     * 🏷️ Печать для нескольких товаров (?ids=1,2,3)
     * Всегда печатает по размерам, если они есть.
     */
    public function many(Request $r)
    {
        $ids = array_filter(explode(',', (string) $r->query('ids', '')));

        $products = Product::withoutGlobalScopes()
            ->withoutTrashed()
            ->with(['sizes' => function ($q) {
                $q->withPivot(['count', 'sku', 'barcode']);
            }])
            ->whereIn('id', $ids)
            ->get();

        $items = [];

        foreach ($products as $p) {
            foreach ($p->sizes as $size) {
                $stock = (int) ($size->pivot->count ?? 0);
                if ($stock <= 0 || empty($size->pivot->barcode)) {
                    continue;
                }

                $items[] = [
                    'name'    => $p->name_ru ?? $p->name ?? ('#' . $p->id),
                    'sku'     => $size->pivot->sku ?? ($p->sku ? $p->sku . '-' . $size->name : null),
                    'size'    => $size->name,
                    'price'   => (int) ($p->price ?? 0),
                    'barcode' => $size->pivot->barcode,  // ✅ только по размеру
                    'repeat'  => $stock,
                ];
            }
        }

        if (empty($items)) {
            return back()->with('error', 'Нет товаров с размерами для печати');
        }

        return view('print.barcodes_by_sizes', compact('items'));
    }
}