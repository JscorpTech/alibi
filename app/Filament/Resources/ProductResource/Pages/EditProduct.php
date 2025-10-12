<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    public array $stocks = [];

    private function normalizeUploadPath($val): ?string
    {
        if ($val instanceof TemporaryUploadedFile) {
            return $val->store('products', 'public');
        }
        if (is_array($val)) {
            return $val['path'] ?? (reset($val) ?: null);
        }
        return is_string($val) ? $val : null;
    }

    private function mapColorImagesFromState($state): array
    {
        $out = [];

        foreach ((array) $state as $row) {
            // пропустим мусор вроде ["products/..."] или просто строк
            if (!is_array($row) || !array_key_exists('color', $row)) {
                continue;
            }

            $color = trim((string) ($row['color'] ?? ''));
            if ($color === '') {
                continue;
            }

            // нормализуем пути
            $paths = [];
            foreach ((array) ($row['paths'] ?? []) as $p) {
                $np = $this->normalizeUploadPath($p);
                if ($np) {
                    $paths[] = $np;
                }
            }
            $paths = array_values(array_filter($paths));
            if (!$paths) {
                continue;
            }

            // переставим выбранную обложку по индексу (если указан)
            $idx = $row['cover_index'] ?? null;
            if (is_numeric($idx) && isset($paths[(int) $idx])) {
                $cover = $paths[(int) $idx];
                $paths = collect($paths)
                    ->reject(fn($x) => $x === $cover)
                    ->prepend($cover)
                    ->values()
                    ->all();
            }

            $out[$color] = $paths;
        }

        return $out;
    }


    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var \App\Models\Product $product */
        $product = $this->record;

        // 1) variant_state из модели
        $variantState = method_exists($product, 'buildVariantState')
            ? $product->buildVariantState()
            : ['variant_options' => [], 'variants_draft' => [], 'variants_editor' => []];

        $data['variant_state'] = array_merge([
            'variant_options' => [],
            'variants_draft' => [],
            'variants_editor' => [],
            'stocks' => [],
        ], $variantState);

        // 2) color_images -> repeater rows
        $rows = [];
        foreach ((array) $product->color_images as $color => $val) {
            $arr = is_string($val) ? [$val] : array_values((array) $val);
            $rows[] = [
                'color' => (string) $color,
                'paths' => $arr,
                'cover_index' => 0,
            ];
        }
        $data['color_images'] = $rows;

        // 3) Сформировать карту stocks для wire:model
        $stocks = [];
        foreach ((array) ($data['variant_state']['variants_editor'] ?? []) as $r) {
            $attrs = (array) ($r['attrs'] ?? []);
            ksort($attrs);
            $rowKey = !empty($r['id'])
                ? 'id:' . (int) $r['id']
                : 'attrs:' . substr(md5(json_encode($attrs, JSON_UNESCAPED_UNICODE)), 0, 12);

            $stocks[$rowKey] = (int) ($r['stock'] ?? 0);
        }
        $data['variant_state']['stocks'] = $stocks;

        // 🔍 ЛОГ
        \Log::info('Loading stocks from DB (mutateFormDataBeforeFill)', [
            'stocks' => $stocks,
            'variants_count' => count($data['variant_state']['variants_editor'] ?? []),
        ]);

        return $data;
    }

    // ✅ ДОБАВЬТЕ ЭТОТ МЕТОД (вызывается ПОСЛЕ fill)
    protected function afterFill(): void
    {
        // Принудительно установить stocks в публичное свойство Livewire
        $stocks = data_get($this->data, 'variant_state.stocks', []);

        if (!empty($stocks)) {
            // ✅ Установить в публичное свойство
            $this->stocks = $stocks;

            \Log::info('afterFill: stocks injected', [
                'stocks' => $stocks,
                'public_property_set' => true,
            ]);
        }
    }


    // 1) Хелпер: вливаем quantities из plain-инпутов (View) в строки редактора
    /**
     * Посчитать стабильный ключ строки варианта (как в Blade).
     */
    private function makeRowKey(array $row): string
    {
        $id = (int) ($row['id'] ?? 0);
        if ($id > 0) {
            return 'id:' . $id;
        }
        $attrs = (array) ($row['attrs'] ?? []);
        ksort($attrs);
        return 'attrs:' . substr(md5(json_encode($attrs, JSON_UNESCAPED_UNICODE)), 0, 12);
    }


    // было: mergeStocksFromRequestIntoRows(array $rows)
// стало:
    // 👇 добавь в класс, если ещё нет
    private function mergeStocksFromStateIntoRows(array $rows, array $stocks): array
    {
        if (!$stocks) {
            return $rows;
        }

        // 🔍 ЛОГ: что вливаем
        \Log::info('mergeStocksFromStateIntoRows', [
            'input_rows_count' => count($rows),
            'input_stocks' => $stocks,
        ]);

        // маппим по ключам: "id:123" или "attrs:<hash>"
        foreach ($rows as &$row) {
            $id = (int) ($row['id'] ?? 0);

            // ключ от id варианта
            if ($id && array_key_exists("id:{$id}", $stocks)) {
                $oldStock = $row['stock'] ?? 0;
                $row['stock'] = (int) $stocks["id:{$id}"];

                \Log::info("Row id:{$id} stock updated", [
                    'old' => $oldStock,
                    'new' => $row['stock'],
                ]);
                continue;
            }

            // ключ от атрибутов (если строки ещё без id)
            $attrs = (array) ($row['attrs'] ?? []);
            ksort($attrs);
            $hash = substr(md5(json_encode($attrs, JSON_UNESCAPED_UNICODE)), 0, 12);
            $key = "attrs:{$hash}";

            if (array_key_exists($key, $stocks)) {
                $row['stock'] = (int) $stocks[$key];
            }
        }
        unset($row);

        // 🔍 ЛОГ: что получилось
        \Log::info('mergeStocksFromStateIntoRows result', [
            'output_rows' => array_map(fn($r) => ['id' => $r['id'] ?? null, 'stock' => $r['stock'] ?? null], $rows),
        ]);

        return $rows;
    }
    // 2) Перед сохранением формы: собираем color_images и вливаем stocks[] в state
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $state = $this->form->getState();

        // ✅ Берём stocks из публичного свойства (а не из state)
        $stocks = $this->stocks ?? [];

        // 🔍 Лог
        \Log::info('Stock values before save', [
            'stocks_from_property' => $this->stocks,
            'stocks_from_state' => data_get($state, 'variant_state.stocks'),
            'final_stocks' => $stocks,
        ]);

        // color_images маппинг
        $raw = $state['color_images'] ?? $state['color_images_ui'] ?? [];
        $data['color_images'] = $this->mapColorImagesFromState($raw);
        unset($data['color_images_ui']);

        // ✅ Вливаем stocks в rows
        $rows = data_get($state, 'variant_state.variants_editor', []);

        if (!empty($rows) && !empty($stocks)) {
            $rows = $this->mergeStocksFromStateIntoRows($rows, $stocks);
            data_set($state, 'variant_state.variants_editor', $rows);
        }

        return $data;
    }

    // 3) После сохранения записи: создаём/обновляем варианты с актуальным stock
    // ✅ окончательный afterSave
    protected function afterSave(): void
    {
        $state = $this->form->getState();

        // ✅ БЕРЁМ ИЗ БД через buildVariantState (не из state!)
        $variantState = $this->record->buildVariantState();
        $rows = $variantState['variants_editor'] ?? [];

        $stocks = $this->stocks ?? [];

        \Log::info('afterSave: stocks source', [
            'from_property' => $this->stocks ?? 'empty',
            'final_stocks' => $stocks,
        ]);

        \Log::info('afterSave: rows from buildVariantState', [
            'count' => count($rows),
            'first_row_id' => $rows[0]['id'] ?? null,
            'first_row_stock' => $rows[0]['stock'] ?? null,
        ]);

        // 1) вливаем стоки и сохраняем варианты
        if (!empty($rows) && !empty($stocks)) {
            $rows = $this->mergeStocksFromStateIntoRows($rows, $stocks);

            \Log::info('afterSave: rows AFTER merge', [
                'first_row_id' => $rows[0]['id'] ?? null,
                'first_row_stock' => $rows[0]['stock'] ?? null,
            ]);

            $this->record->matrixToVariants($rows);
            $this->record->refresh();
        }

        // 2) считаем остатки и синкаем InventoryLevel
        /** @var \App\Models\Product $product */
        $product = $this->record;

        $locationId = data_get($state, 'stock_location_id')
            ?? $product->stock_location_id
            ?? \App\Models\StockLocation::where('code', 'alibi')->value('id')
            ?? \App\Models\StockLocation::where('type', 'warehouse')->value('id')
            ?? \App\Models\StockLocation::value('id');

        if (!$locationId) {
            return;
        }

        $variants = $product->variants()->get(['stock', 'attrs']);
        $total = (int) $variants->sum('stock');

        // остатков по размерам
        $qtyBySizeName = [];
        foreach ($variants as $v) {
            $sizeName = (string) data_get($v->attrs, 'Size', '');
            if ($sizeName === '')
                continue;
            $qtyBySizeName[$sizeName] = ($qtyBySizeName[$sizeName] ?? 0) + (int) $v->stock;
        }

        // upsert по размерам
        $keptSizeIds = [];
        if ($qtyBySizeName) {
            $sizeIdsByName = \App\Models\Size::whereIn('name', array_keys($qtyBySizeName))
                ->pluck('id', 'name')->all();

            foreach ($qtyBySizeName as $sizeName => $qty) {
                $sizeId = (int) ($sizeIdsByName[$sizeName] ?? 0);
                if (!$sizeId)
                    continue;

                \App\Models\InventoryLevel::updateOrCreate(
                    ['product_id' => $product->id, 'size_id' => $sizeId, 'stock_location_id' => $locationId],
                    ['qty_on_hand' => (int) $qty, 'qty_reserved' => 0]
                );
                $keptSizeIds[] = $sizeId;
            }
        }

        // удалить лишние строки по размерам
        \App\Models\InventoryLevel::where('product_id', $product->id)
            ->where('stock_location_id', $locationId)
            ->whereNotNull('size_id')
            ->when(!empty($keptSizeIds), fn($q) => $q->whereNotIn('size_id', $keptSizeIds))
            ->when(empty($keptSizeIds), fn($q) => $q)
            ->delete();

        // общая строка (size_id = null)
        \App\Models\InventoryLevel::updateOrCreate(
            ['product_id' => $product->id, 'size_id' => null, 'stock_location_id' => $locationId],
            ['qty_on_hand' => $total, 'qty_reserved' => 0]
        );

        // если у товара не проставлена локация — ставим
        if (empty($product->stock_location_id)) {
            $product->update(['stock_location_id' => $locationId]);
        }

        // ✅ Синхронизируем публичное свойство с актуальными данными
        $freshStocks = [];
        foreach ($this->record->variants()->get(['id', 'stock']) as $v) {
            $freshStocks['id:' . $v->id] = (int) $v->stock;
        }
        $this->stocks = $freshStocks;

        \Log::info('afterSave: complete', [
            'updated_stocks' => $this->stocks,
        ]);
    }

    /**
     * Твоя складская логика — без изменений.
     */

}