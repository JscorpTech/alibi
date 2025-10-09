<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Variant;

class VariantGenerator
{
    public function run(Product $product): void
    {
        // Собираем оси → значения (из option_items)
        $options = $product->options()
            ->with('items:id,option_id,name')
            ->get()
            ->mapWithKeys(fn($opt) => [
                $opt->name => $opt->items->pluck('name')->filter()->unique()->values()->all(),
            ])
            ->toArray();

        $sizes  = $options['Size']  ?? [];
        $colors = $options['Color'] ?? [];

        // Декартово произведение
        $combos = [];
        if ($sizes && $colors) {
            foreach ($colors as $c) foreach ($sizes as $s) $combos[] = ['Size' => $s, 'Color' => $c];
        } elseif ($sizes) {
            foreach ($sizes as $s) $combos[] = ['Size' => $s];
        } elseif ($colors) {
            foreach ($colors as $c) $combos[] = ['Color' => $c];
        }

        // Создаём недостающие варианты (уникальность по product_id + attrs)
        foreach ($combos as $attrs) {
            $exists = Variant::query()
                ->where('product_id', $product->id)
                ->whereJsonContains('attrs', $attrs)
                ->exists();

            if (! $exists) {
                Variant::create([
                    'product_id' => $product->id,
                    'attrs'      => $attrs,             // $casts в модели Variant: ['attrs'=>'array']
                    'price'      => $product->price ?? 0,
                    'stock'      => 0,
                    'available'  => true,
                ]);
            }
        }
    }
}