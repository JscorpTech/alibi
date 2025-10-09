<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->record->getKey()]);
    }

 
    protected function afterSave(): void
    {
        $product = $this->record;
        $data = $this->form->getState();

        // 📦 Склад по умолчанию / выбранный
        $locationId = $data['stock_location_id']
            ?? \App\Models\StockLocation::where('code', 'alibi')->value('id')
            ?? \App\Models\StockLocation::where('type', 'warehouse')->value('id')
            ?? \App\Models\StockLocation::value('id');

        if ($locationId) {
            // 🧾 productSizes = [{size_id, count}]
            $rows = $data['productSizes'] ?? [];
            if (is_string($rows)) {
                $rows = json_decode($rows, true) ?? [];
            }

            $total = 0;
            foreach ($rows as $r) {
                $sizeId = (int) ($r['size_id'] ?? 0);
                $qty = (int) ($r['count'] ?? 0);
                $total += $qty;

                if ($sizeId > 0) {
                    \App\Models\InventoryLevel::updateOrCreate(
                        ['product_id' => $product->id, 'size_id' => $sizeId, 'stock_location_id' => $locationId],
                        ['qty_on_hand' => $qty, 'qty_reserved' => 0]
                    );
                }
            }

            // агрегат по продукту (без size_id)
            \App\Models\InventoryLevel::updateOrCreate(
                ['product_id' => $product->id, 'size_id' => null, 'stock_location_id' => $locationId],
                ['qty_on_hand' => $total, 'qty_reserved' => 0]
            );

            // привязываем склад к товару, если изменили
            if (empty($product->stock_location_id)) {
                $product->update(['stock_location_id' => $locationId]);
            }
        }

        // 🚀 Генерация/обновление вариантов по текущим размерам/цветам
        $this->generateVariantsFor($product);
    }

    /**
     * Генерация вариантов (Size × Color) + SKU/Barcode/Image
     */
    private function generateVariantsFor(\App\Models\Product $record): void
    {
        // размеры из product_sizes
        $sizeNames = \DB::table('product_sizes')
            ->join('sizes', 'sizes.id', '=', 'product_sizes.size_id')
            ->where('product_sizes.product_id', $record->id)
            ->pluck('sizes.name')
            ->filter()->unique()->values()->all();

        // цвета из product_colors
        $colorNames = \DB::table('product_colors')
            ->join('colors', 'colors.id', '=', 'product_colors.color_id')
            ->where('product_colors.product_id', $record->id)
            ->pluck('colors.name')
            ->filter()->unique()->values()->all();

        // “цвет → фото” (product_colors.path)
        $colorImageByName = \DB::table('product_colors')
            ->join('colors', 'colors.id', '=', 'product_colors.color_id')
            ->where('product_colors.product_id', $record->id)
            ->pluck('product_colors.path', 'colors.name')
            ->toArray();

        // все комбинации
        $combos = [];
        if ($sizeNames && $colorNames) {
            foreach ($colorNames as $c) {
                foreach ($sizeNames as $s) {
                    $combos[] = ['Size' => (string) $s, 'Color' => (string) $c];
                }
            }
        } elseif ($sizeNames) {
            foreach ($sizeNames as $s) {
                $combos[] = ['Size' => (string) $s];
            }
        } elseif ($colorNames) {
            foreach ($colorNames as $c) {
                $combos[] = ['Color' => (string) $c];
            }
        } else {
            // нет осей — ничего не делаем
            return;
        }

        foreach ($combos as $attrs) {
            // ищем существующий вариант по product_id + attrs(JSON)
            $variant = \App\Models\Variant::query()
                ->where('product_id', $record->id)
                ->whereJsonContains('attrs', $attrs)
                ->first();

            if (!$variant) {
                $variant = new \App\Models\Variant();
                $variant->product_id = $record->id;
                $variant->attrs = $attrs;
                $variant->price = (int) ($record->price ?? 0);

                // если только размер — подтянем стартовый остаток из product_sizes
                if (isset($attrs['Size']) && !isset($attrs['Color'])) {
                    $sizeId = \DB::table('sizes')->where('name', $attrs['Size'])->value('id');
                    $variant->stock = (int) \DB::table('product_sizes')
                        ->where(['product_id' => $record->id, 'size_id' => $sizeId])
                        ->value('count') ?? 0;
                } else {
                    $variant->stock = 0;
                }
            }

            // фото: цветовое, иначе главное фото товара
            $variant->image = isset($attrs['Color'])
                ? ($colorImageByName[$attrs['Color']] ?? $record->image)
                : ($variant->image ?? $record->image);

            // SKU
            if (empty($variant->sku)) {
                $base = $record->sku ?: 'SKU' . $record->id;
                $parts = [];
                if (isset($attrs['Size']))
                    $parts[] = (string) $attrs['Size'];
                if (isset($attrs['Color']))
                    $parts[] = Str::upper(Str::substr($attrs['Color'], 0, 3));
                $variant->sku = $base . '-' . implode('-', $parts);
            }

            // Barcode
            if (empty($variant->barcode)) {
                do {
                    $code = rand(1000000000000, 9999999999999); // заглушка; замени на свой генератор EAN13
                } while (
                    \DB::table('variants')->where('barcode', $code)->exists() ||
                    \DB::table('products')->where('barcode', $code)->exists()
                );
                $variant->barcode = $code;
            }

            $variant->available = $variant->stock > 0;
            $variant->save();
        }
    }
}