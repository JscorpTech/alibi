<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    // ✅ ДОБАВЛЕНО: публичное свойство для Livewire
    public array $stocks = [];

    /**
     * Универсальный нормалайзер значения upload → строка пути.
     */
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

    /**
     * Преобразуем состояние репитера color_images в целевой JSON формат:
     * { "Black": ["products/..cover..","products/..2.."], "White": [...] }
     * cover_index — переставляется первым.
     */
    private function mapColorImagesFromState($state): array
    {
        $out = [];

        foreach ((array) $state as $row) {
            if (!is_array($row) || !array_key_exists('color', $row)) {
                continue;
            }

            $color = trim((string) ($row['color'] ?? ''));
            if ($color === '') {
                continue;
            }

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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $state = $this->form->getState();
        $raw = $state['color_images'] ?? $state['color_images_ui'] ?? [];
        $data['color_images'] = $this->mapColorImagesFromState($raw);
        unset($data['color_images_ui']);
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $state = $this->form->getState();
        $raw = $state['color_images'] ?? $state['color_images_ui'] ?? [];
        $data['color_images'] = $this->mapColorImagesFromState($raw);
        unset($data['color_images_ui']);
        return $data;
    }

    protected function afterCreate(): void
    {
        $state = $this->form->getState();

        // ✅ ИЗМЕНЕНО: строим строки из опций
        $rows = $this->buildRowsFromOptions((array) data_get($state, 'variant_state.variant_options', []));

        if (empty($rows)) {
            return;
        }

        /** @var \App\Models\Product $product */
        $product = $this->record;

        // Составим карту размеров одной выборкой
        $ps = (array) ($state['productSizes'] ?? []);
        $sizeIds = collect($ps)->pluck('size_id')->filter()->unique()->values()->all();
        $sizeNameById = empty($sizeIds)
            ? []
            : \App\Models\Size::whereIn('id', $sizeIds)->pluck('name', 'id')->all();

        // Соберём qty по имени размера
        $qtyBySizeName = collect($ps)->mapWithKeys(function ($r) use ($sizeNameById) {
            $sizeId = (int) ($r['size_id'] ?? 0);
            $qty = (int) ($r['count'] ?? 0);
            $name = $sizeNameById[$sizeId] ?? null;
            return $name ? [$name => $qty] : [];
        })->all();

        // Подольём qty из productSizes → в stock строк
        if (!empty($qtyBySizeName)) {
            $rows = array_map(function ($row) use ($qtyBySizeName) {
                $size = (string) data_get($row, 'attrs.Size', '');
                if ($size !== '' && isset($qtyBySizeName[$size])) {
                    $row['stock'] = (int) $qtyBySizeName[$size];
                }
                return $row;
            }, $rows);
        }

        // ✅ ИЗМЕНЕНО: берём из публичного свойства
        $inlineStocks = $this->stocks ?? (array) data_get($state, 'variant_state.stocks', []);
        if (!empty($inlineStocks)) {
            $rows = $this->mergeStocksIntoRows($rows, $inlineStocks);
        }

        // Сохраняем варианты
        \DB::transaction(function () use ($product, $rows) {
            foreach ($rows as $row) {
                $attrs = (array) ($row['attrs'] ?? []);
                $attrs = $this->normalizeAttrsKeys($attrs);
                ksort($attrs);

                $baseSku = trim((string) $product->sku);
                $suffix = collect($attrs)->map(function ($v) {
                    $v = preg_replace('/\s+/', '-', (string) $v);
                    return strtoupper(substr($v, 0, 3));
                })->implode('-');
                $sku = ($row['sku'] ?? '') ?: trim($baseSku ? ($baseSku . '-' . $suffix) : $suffix, '-');

                $existing = \App\Models\Variant::where('product_id', $product->id)
                    ->whereRaw('attrs::jsonb = ?::jsonb', [json_encode($attrs)])
                    ->first();

                $payload = [
                    'sku' => $sku ?: null,
                    'price' => (int) ($row['price'] ?? 0),
                    'stock' => (int) ($row['stock'] ?? 0),
                    'available' => !empty($row['available']),
                ];

                if ($existing) {
                    $existing->update($payload);
                } else {
                    \App\Models\Variant::create($payload + [
                        'product_id' => $product->id,
                        'attrs' => $attrs,
                    ]);
                }
            }
        });

        // Синхронизируем InventoryLevel из variants
        $locationId = data_get($state, 'stock_location_id')
            ?? \App\Models\StockLocation::where('code', 'alibi')->value('id')
            ?? \App\Models\StockLocation::where('type', 'warehouse')->value('id')
            ?? \App\Models\StockLocation::value('id');

        if ($locationId) {
            $variants = $product->variants()->get(['stock', 'attrs']);
            $total = (int) $variants->sum('stock');

            $qtyBySizeName = [];
            foreach ($variants as $v) {
                $sizeName = (string) data_get($v->attrs, 'Size', '');
                if ($sizeName === '')
                    continue;
                $qtyBySizeName[$sizeName] = ($qtyBySizeName[$sizeName] ?? 0) + (int) $v->stock;
            }

            $keptSizeIds = [];
            if (!empty($qtyBySizeName)) {
                $sizeIdsByName = \App\Models\Size::whereIn('name', array_keys($qtyBySizeName))
                    ->pluck('id', 'name')->all();

                foreach ($qtyBySizeName as $name => $qty) {
                    $sizeId = $sizeIdsByName[$name] ?? null;
                    if (!$sizeId)
                        continue;

                    \App\Models\InventoryLevel::updateOrCreate(
                        ['product_id' => $product->id, 'size_id' => $sizeId, 'stock_location_id' => $locationId],
                        ['qty_on_hand' => (int) $qty, 'qty_reserved' => 0]
                    );
                    $keptSizeIds[] = $sizeId;
                }

                \App\Models\InventoryLevel::where('product_id', $product->id)
                    ->where('stock_location_id', $locationId)
                    ->whereNotNull('size_id')
                    ->when(!empty($keptSizeIds), fn($q) => $q->whereNotIn('size_id', $keptSizeIds))
                    ->delete();
            } else {
                \App\Models\InventoryLevel::where('product_id', $product->id)
                    ->where('stock_location_id', $locationId)
                    ->whereNotNull('size_id')
                    ->delete();
            }

            \App\Models\InventoryLevel::updateOrCreate(
                ['product_id' => $product->id, 'size_id' => null, 'stock_location_id' => $locationId],
                ['qty_on_hand' => $total, 'qty_reserved' => 0]
            );

            if (empty($product->stock_location_id)) {
                $product->update(['stock_location_id' => $locationId]);
            }
        }

        $this->record->refresh();
        $this->fillForm();
    }

    // ✅ ИЗМЕНЕНО: поддержка ключей по атрибутам для создания
    private function mergeStocksIntoRows(array $rows, array $stocks): array
    {
        if (empty($rows) || empty($stocks)) {
            return $rows;
        }

        foreach ($rows as &$row) {
            $id = (int) ($row['id'] ?? 0);

            // Если есть id варианта - используем его
            if ($id && array_key_exists("id:{$id}", $stocks)) {
                $row['stock'] = (int) $stocks["id:{$id}"];
                continue;
            }

            // Если id нет (создание) - используем ключ по атрибутам
            $attrs = (array) ($row['attrs'] ?? []);
            ksort($attrs);
            $hash = substr(md5(json_encode($attrs, JSON_UNESCAPED_UNICODE)), 0, 12);
            $key = "attrs:{$hash}";

            if (array_key_exists($key, $stocks)) {
                $row['stock'] = (int) $stocks[$key];
            }
        }
        unset($row);

        return $rows;
    }

    private function buildRowsFromOptions(array $options): array
    {
        $opts = collect($options)
            ->filter(fn($o) => !empty($o['name']) && !empty($o['values']))
            ->values()->all();

        if (!$opts) {
            return [];
        }

        $result = [[]];
        foreach ($opts as $opt) {
            $tmp = [];
            foreach ($result as $r) {
                foreach ((array) $opt['values'] as $val) {
                    $tmp[] = array_merge($r, [$opt['name'] => $val]);
                }
            }
            $result = $tmp;
        }

        $rows = [];
        foreach ($result as $attrs) {
            $attrs = $this->normalizeAttrsKeys($attrs);
            ksort($attrs);
            $title = collect($attrs)->map(fn($v, $k) => "{$k}: {$v}")->implode(' / ');
            $rows[] = [
                'title' => $title,
                'attrs' => $attrs,
                'price' => 0,
                'stock' => 0,
                'available' => true,
                'sku' => null,
            ];
        }
        return $rows;
    }

    private function normalizeAttrsKeys(array $a): array
    {
        $map = ['color' => 'Color', 'Colour' => 'Color', 'colour' => 'Color', 'size' => 'Size'];
        foreach ($map as $from => $to) {
            if (array_key_exists($from, $a) && !array_key_exists($to, $a)) {
                $a[$to] = $a[$from];
                unset($a[$from]);
            }
        }
        return $a;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Товар создан';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->record->getKey()]);
    }
}