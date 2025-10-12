<?php

namespace App\Filament\Pages\POS;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Schema;

trait SearchTrait
{
    public string $barcode = '';
    /** Результаты поиска по имени/коду */
    public array $results = [];

    // выбор цвета/размера из карточек
    public array $selectedColor = []; // [productId => colorId]
    public array $selectedSize  = []; // [productId => sizeId]

    protected function resolveVariantId(int $productId, ?int $sizeId, ?int $colorId): ?int
    {
        $sizeName  = $sizeId  ? \App\Models\Size::find($sizeId)?->name   : null;
        $colorName = $colorId ? \App\Models\Color::find($colorId)?->name : null;

        $q = \App\Models\Variant::where('product_id', $productId);
        if ($sizeName)  $q->whereJsonContains('attrs->Size', $sizeName);
        if ($colorName) $q->whereJsonContains('attrs->Color', $colorName);

        return $q->value('id');
    }

    // вычисление stock по Variant.stock (через attrs->Size/Color)
    protected function getStock(int $productId, ?int $sizeId = null, ?int $colorId = null): ?int
    {
        if (!$sizeId && !$colorId) return null;

        $sizeName  = $sizeId  ? \App\Models\Size::find($sizeId)?->name   : null;
        $colorName = $colorId ? \App\Models\Color::find($colorId)?->name : null;

        $q = \App\Models\Variant::where('product_id', $productId);
        if ($sizeName)  $q->where('attrs->Size',  $sizeName);
        if ($colorName) $q->where('attrs->Color', $colorName);

        $stock = $q->value('stock');
        return $stock === null ? 0 : (int) $stock;
    }

    public function scan(): void
    {
        $code = trim((string) $this->barcode);
        if ($code === '') return;

        if ($this->tryAddByCode($code)) return;

        $this->results = $this->searchByName($code);
        if (empty($this->results)) {
            Notification::make()->title('Ничего не найдено')->danger()->send();
        }
    }

    protected function tryAddByCode(string $code): bool
    {
        $code = trim($code); if ($code === '') return false;

        $v = \App\Models\Variant::query()
            ->where('barcode', $code)
            ->orWhere('sku', $code)
            ->first(['id','product_id','attrs','image','sku','stock']);

        if (!$v) return false;

        $sizeName  = (string) data_get($v->attrs, 'Size', '');
        $colorName = (string) data_get($v->attrs, 'Color', '');

        $sizeId  = $sizeName  !== '' ? \App\Models\Size::where('name', $sizeName)->value('id')   : null;
        $colorId = $colorName !== '' ? \App\Models\Color::where('name', $colorName)->value('id') : null;

        $this->addToCart((int) $v->product_id, $sizeId ? (int)$sizeId : null, $colorId ? (int)$colorId : null);

        $this->barcode = '';
        $this->results = [];
        return true;
    }

    public function updatedBarcode(string $value): void
    {
        $q = trim($value);
        if ($q === '' || mb_strlen($q) < 2) { $this->results = []; return; }

        $codeHits = $this->searchByVariantCodeLike($q);
        $nameHits = $this->searchByName($q);

        $seen = []; $merged = [];
        foreach ($codeHits as $r) {
            $key = 'v:' . ($r['variant_id'] ?? 0);
            if (!isset($seen[$key])) { $merged[] = $r; $seen[$key] = true; }
        }
        foreach ($nameHits as $r) {
            $key = 'p:' . ($r['id'] ?? 0);
            if (!isset($seen[$key])) { $merged[] = $r + ['_hit' => 'name']; $seen[$key] = true; }
        }
        $this->results = $merged;
    }

    protected function searchByVariantCodeLike(string $q): array
    {
        $q = trim($q); if ($q === '') return [];

        $variants = \App\Models\Variant::query()
            ->where(fn($w) => $w->where('barcode','ILIKE','%'.$q.'%')->orWhere('sku','ILIKE','%'.$q.'%'))
            ->with(['product' => function ($p) {
                $cols = ['id','price'];
                if (Schema::hasColumn('products', 'name_ru')) $cols[] = 'name_ru';
                elseif (Schema::hasColumn('products', 'name')) $cols[] = 'name';
                if (Schema::hasColumn('products', 'image'))   $cols[] = 'image';
                $p->select($cols);
            }])
            ->limit(20)
            ->get(['id','product_id','attrs','sku','barcode','image']);

        return $variants->map(function ($v) {
            $p = $v->product;
            $sizeName  = (string) data_get($v->attrs, 'Size', '');
            $colorName = (string) data_get($v->attrs, 'Color', '');

            $sizeId  = $sizeName  !== '' ? \App\Models\Size::where('name',$sizeName)->value('id')   : null;
            $colorId = $colorName !== '' ? \App\Models\Color::where('name',$colorName)->value('id') : null;

            $img = null;
            foreach ([$v->image ?? null, $p?->image ?? null] as $raw) {
                if (!$img && $raw) {
                    if (is_array($raw)) $raw = reset($raw);
                    if (!is_string($raw) || $raw === '') continue;
                    $norm = preg_replace('#^/?public/#', '', $raw);
                    $img  = str_starts_with($norm,'http') ? $norm : asset('storage/'.ltrim($norm,'/'));
                }
            }

            return [
                'id' => (int)($p?->id ?? 0),
                'variant_id' => (int)$v->id,
                'name' => ($p?->name_ru ?? $p?->name ?? ('Товар #'.($p?->id ?? '')))
                         . ($sizeName ? ' • '.$sizeName : '')
                         . ($colorName ? ' • '.$colorName : ''),
                'price' => (int)($p?->price ?? 0),
                'image' => $img,
                'sku' => $v->sku,
                'barcode' => $v->barcode,
                'size_id' => $sizeId ?: null,
                'color_id' => $colorId ?: null,
                '_hit' => 'code',
            ];
        })->toArray();
    }

    protected function searchByName(string $name): array
    {
        $q = \App\Models\Product::withoutGlobalScopes()->withoutTrashed();

        if (Schema::hasColumn('products','name_ru')) $q->where('name_ru','ILIKE','%'.$name.'%');
        elseif (Schema::hasColumn('products','name')) $q->where('name','ILIKE','%'.$name.'%');
        else return [];

        $select = ['id','price'];
        foreach (['name_ru','name','image'] as $c) if (Schema::hasColumn('products',$c)) $select[] = $c;

        return $q->select($select)->limit(20)->get()->map(function ($p) {
            $pid    = (int)$p->id;
            $colors = $this->getColors($pid);
            $sizes  = $this->getSizes($pid);
            $matrix = $this->getVariantMatrix($pid);
            $qtyTotal = array_sum(array_map(fn($grp) => (int)($grp['qty_total'] ?? 0), $matrix));

            return [
                'id' => $pid,
                'name' => $p->name_ru ?? $p->name ?? ('Товар #'.$pid),
                'price' => (int)($p->price ?? 0),
                'image' => $this->imageUrl($p),
                'colors' => $colors,
                'sizes' => $sizes,
                'matrix' => $matrix,
                'qty_total' => $qtyTotal ?: null,
            ];
        })->toArray();
    }

    protected function searchBySku(string $sku): array
    {
        $out = [];

        if (Schema::hasTable('product_sizes') && Schema::hasColumn('product_sizes','sku')) {
            $qb = \DB::table('product_sizes as ps')
                ->join('products as p','p.id','=','ps.product_id')
                ->leftJoin('sizes as s','s.id','=','ps.size_id')
                ->whereNotNull('ps.sku')
                ->where('ps.sku','ILIKE','%'.$sku.'%');

            $select = [
                'p.id as product_id','p.price','p.image','ps.size_id',
                'ps.sku as variant_sku','ps.barcode as variant_barcode',
                's.name as size_name',
            ];
            if (Schema::hasColumn('products','name_ru')) $select[] = 'p.name_ru as product_name_ru';
            if (Schema::hasColumn('products','name'))    $select[] = 'p.name as product_name';

            $rows = $qb->select($select)->limit(20)->get();
            foreach ($rows as $r) {
                $name = $r->product_name_ru ?? $r->product_name ?? ('Товар #'.$r->product_id);
                $out[] = [
                    'id' => (int)$r->product_id,
                    'name' => $name . ($r->size_name ? (' • '.$r->size_name) : ''),
                    'price' => (int)($r->price ?? 0),
                    'image' => $this->imageUrl((object)['image'=>$r->image]),
                    'sizes' => $r->size_id ? [[
                        'id' => (int)$r->size_id,
                        'name' => (string)$r->size_name,
                        'stock' => $this->getStock((int)$r->product_id, (int)$r->size_id, null),
                    ]] : [],
                    'sku' => $r->variant_sku,
                    'barcode' => $r->variant_barcode,
                ];
            }
            if (!empty($out)) return $out;
        }

        if (Schema::hasColumn('products','sku')) {
            $rows = \App\Models\Product::withoutGlobalScopes()->withoutTrashed()
                ->whereNotNull('sku')->where('sku','ILIKE','%'.$sku.'%')
                ->select($this->selectFields())->limit(20)->get();

            foreach ($rows as $p) {
                $out[] = [
                    'id' => (int)$p->id,
                    'name' => $p->name_ru ?? $p->name ?? ('Товар #'.$p->id),
                    'price' => (int)($p->price ?? 0),
                    'image' => $this->imageUrl($p),
                    'sizes' => $this->getSizes((int)$p->id),
                ];
            }
        }
        return $out;
    }

    public function selectColor(int $productId, int $colorId): void
    {
        $this->selectedColor[$productId] = $colorId;
    }

    public function selectSize(int $productId, int $sizeId): void
    {
        $this->selectedSize[$productId] = $sizeId;
    }

    public function addSelected(int $productId): void
    {
        $sizes  = $this->getSizes($productId);
        $colors = $this->getColors($productId);

        $needSize  = !empty($sizes);
        $needColor = !empty($colors);

        $sizeId  = $this->selectedSize[$productId]  ?? null;
        $colorId = $this->selectedColor[$productId] ?? null;

        if ($needSize && !$sizeId)  { \Filament\Notifications\Notification::make()->title('Выберите размер')->warning()->send(); return; }
        if ($needColor && !$colorId){ \Filament\Notifications\Notification::make()->title('Выберите цвет')->warning()->send();  return; }

        $this->addToCart($productId, $sizeId, $colorId);
    }

    public function addToCart(int $productId, ?int $sizeId = null, ?int $colorId = null): void
    {
        try {
            $product = \App\Models\Product::find($productId);
            if (!$product) return;

            $stock = $this->getStock($productId, $sizeId, $colorId);

            foreach ($this->cart as &$row) {
                if ($row['id'] === $productId && ($row['size_id'] ?? null) === $sizeId && ($row['color_id'] ?? null) === $colorId) {
                    if ($stock !== null && $row['qty'] + 1 > $stock) {
                        \Filament\Notifications\Notification::make()->title('Недостаточно остатка')->body('Доступно: '.$stock.' шт.')->danger()->send();
                        return;
                    }
                    $row['qty'] = (int) ($row['qty'] ?? 0) + 1;
                    $this->barcode = ''; $this->results = []; $this->sortCart(); return;
                }
            }
            unset($row);

            $sizeName  = $sizeId  ? \App\Models\Size::find($sizeId)?->name   : null;
            $colorName = $colorId ? \App\Models\Color::find($colorId)?->name : null;

            $variantId = $this->resolveVariantId($productId, $sizeId, $colorId);
            $variant   = $variantId ? \App\Models\Variant::find($variantId) : null;

            $variantImg = ( $variant && !empty($variant->image) && is_string($variant->image) )
                ? $this->fileUrl($variant->image) : null;

            $variantSku = $variant?->sku ?? ($product->sku ?? null);

            $item = $this->productToCartItem($product, $sizeId, $sizeName, $variantSku, $colorId, $colorName, $variantImg);
            $item['variant_id'] = $variantId;

            if ($stock !== null && ($item['qty'] ?? 1) > $stock) {
                \Filament\Notifications\Notification::make()->title('Недостаточно остатка')->body('Доступно: '.$stock.' шт.')->danger()->send();
                return;
            }

            $this->cart[] = $item;
            $this->barcode = ''; $this->results = []; $this->sortCart();

        } catch (\Throwable $e) {
            report($e);
            \Filament\Notifications\Notification::make()->title('Ошибка добавления в чек')->body($e->getMessage())->danger()->send();
        }
    }

    protected function getColors(int $productId): array
    {
        $names = \App\Models\Variant::where('product_id', $productId)
            ->pluck('attrs')
            ->map(fn($raw) => is_string($raw) ? json_decode($raw, true) : (array) $raw)
            ->map(fn($a) => (string) ($a['Color'] ?? ''))
            ->filter()->unique()->values();

        if ($names->isEmpty()) return [];

        $map = \DB::table('colors')->whereIn('name', $names)->pluck('id','name');

        return $names->map(fn($name) => [
            'id' => isset($map[$name]) ? (int)$map[$name] : null,
            'name' => $name,
        ])->toArray();
    }

    protected function getSizes(int $productId): array
    {
        $names = \App\Models\Variant::where('product_id', $productId)
            ->pluck('attrs')
            ->map(fn($raw) => is_string($raw) ? json_decode($raw, true) : (array) $raw)
            ->map(fn($a) => (string) ($a['Size'] ?? ''))
            ->filter()->unique()->values();

        if ($names->isEmpty()) return [];

        $map = \DB::table('sizes')->whereIn('name', $names)->pluck('id','name');

        return $names->map(fn($name) => [
            'id' => isset($map[$name]) ? (int)$map[$name] : null,
            'name' => $name,
        ])->toArray();
    }

    protected function getVariantMatrix(int $productId): array
    {
        $rows = \App\Models\Variant::where('product_id', $productId)
            ->get(['id','attrs','sku','barcode','stock']);

        return $rows->map(function ($v) {
            $a = is_string($v->attrs) ? json_decode($v->attrs, true) : (array)$v->attrs;
            return [
                'variant_id' => (int)$v->id,
                'color' => (string) ($a['Color'] ?? '—'),
                'size'  => (string) ($a['Size']  ?? '—'),
                'sku' => (string) ($v->sku ?? ''),
                'barcode' => (string) ($v->barcode ?? ''),
                'stock' => (int) ($v->stock ?? 0),
            ];
        })->groupBy('color')->map(function ($g, $color) {
            return [
                'color' => $color,
                'sizes' => $g->sortBy('size')->values()->map(fn($r) => [
                    'name' => $r['size'],
                    'variant_id' => $r['variant_id'],
                    'sku' => $r['sku'],
                    'barcode' => $r['barcode'],
                    'stock' => $r['stock'],
                ])->all(),
                'qty_total' => $g->sum('stock'),
            ];
        })->values()->toArray();
    }
}