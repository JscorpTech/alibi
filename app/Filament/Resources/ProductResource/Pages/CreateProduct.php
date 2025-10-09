<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function afterCreate(): void
    {
        $product = $this->record;
        $data = $this->form->getState();

        // 📦 Склад по умолчанию
        $locationId = $data['stock_location_id']
            ?? \App\Models\StockLocation::where('code', 'alibi')->value('id')
            ?? \App\Models\StockLocation::where('type', 'warehouse')->value('id')
            ?? \App\Models\StockLocation::value('id');

        if (!$locationId) {
            return;
        }

        // 💾 Привязка склада
        $product->update(['stock_location_id' => $locationId]);

        // 🚀 После сохранения — автогенерация вариантов (оставляем, но без размеров)
        $this->generateVariantsFor($product);
    }

    /**
     * Генерация вариантов (только по цветам)
     */
    protected function generateVariantsFor(\App\Models\Product $record): void
    {
        // 🎨 Цвета
        $colorNames = \DB::table('product_colors')
            ->join('colors', 'colors.id', '=', 'product_colors.color_id')
            ->where('product_colors.product_id', $record->id)
            ->pluck('colors.name')
            ->filter()
            ->unique()
            ->values()
            ->all();

        // 🖼 Карта “цвет → фото”
        $colorImageByName = \DB::table('product_colors as pc')
            ->join('colors as c', 'c.id', '=', 'pc.color_id')
            ->leftJoin('product_color_images as pci', 'pci.product_color_id', '=', 'pc.id')
            ->where('pc.product_id', $record->id)
            ->pluck('pci.path', 'c.name')
            ->toArray();

        // 🧩 Комбинации (только цвета)
        if (empty($colorNames)) {
            return;
        }

        foreach ($colorNames as $color) {
            $attrs = ['Color' => (string) $color];

            $variant = \App\Models\Variant::query()
                ->where('product_id', $record->id)
                ->whereJsonContains('attrs', $attrs)
                ->first();

            if (!$variant) {
                $variant = new \App\Models\Variant();
                $variant->product_id = $record->id;
                $variant->attrs = $attrs;
                $variant->price = (int) ($record->price ?? 0);
                $variant->stock = 0;
            }

            // 🖼 Фото по цвету или fallback на главное
            $variant->image = $colorImageByName[$color] ?? $record->image;

            // 🔢 SKU + Barcode
            if (empty($variant->sku)) {
                $variant->sku = ($record->sku ?: 'SKU' . $record->id) . '-' . strtoupper(substr($color, 0, 3));
            }

            if (empty($variant->barcode)) {
                do {
                    $code = rand(1000000000000, 9999999999999);
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