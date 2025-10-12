<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Variant;

class VariantGenerator
{

    public function generateForProduct(Product $p): int
    {
        $p->loadMissing(['variantOptions.values', 'colors.image', 'images']);

        // карта цветов -> путь картинки (из ProductColors или общее фото)
        $colorImage = [];
        foreach ($p->colors as $pc) {
            $name = trim((string) ($pc->color?->name ?? ''));
            if ($name === '')
                continue;
            $path = $pc->image?->path ?? null;
            if ($path)
                $colorImage[$name] = $path;
        }
        $fallbackImage = $p->image ?? null;

        // соберём опции
        $opts = $p->variantOptions()->with('values')->get()
            ->mapWithKeys(fn($o) => [$o->name => $o->values->pluck('name')->filter()->values()->all()])
            ->toArray();

        $sizes = $opts['Size'] ?? [];
        $colors = $opts['Color'] ?? [];

        // уже существующие attrs (как нормализованная строка, чтобы не плодить дубли)
        $existing = $p->variants()->get()
            ->map(fn($v) => $this->normalizeAttrs($v->attrs))
            ->flip(); // string => variant_id

        $created = 0;

        $combos = $this->cross($sizes, $colors);
        if (empty($combos)) {
            // если ни Size ни Color — создадим один «бесатрибутный» вариант
            $attrStr = $this->normalizeAttrs([]);
            if (!$existing->has($attrStr)) {
                Variant::create([
                    'product_id' => $p->id,
                    'sku' => $this->makeSku($p, null, null),
                    'barcode' => $this->makeBarcode(),
                    'stock' => 0,
                    'price' => 0,
                    'attrs' => [],
                    'image' => $fallbackImage,
                    'available' => true,
                ]);
                $created++;
            }
            return $created;
        }

        foreach ($combos as [$size, $color]) {
            $attrs = [];
            if ($size !== null)
                $attrs['Size'] = (string) $size;
            if ($color !== null)
                $attrs['Color'] = (string) $color;

            $attrStr = $this->normalizeAttrs($attrs);
            if ($existing->has($attrStr)) {
                // уже есть — по желанию можно доперезаполнить картинку
                $vId = $existing[$attrStr];
                $v = $p->variants()->find($vId);
                if ($v && empty($v->image)) {
                    $img = $attrs['Color'] ?? null;
                    $v->image = $colorImage[$img] ?? $fallbackImage;
                    $v->save();
                }
                continue;
            }

            Variant::create([
                'product_id' => $p->id,
                'sku' => $this->makeSku($p, $color, $size),
                'barcode' => $this->makeBarcode(),
                'stock' => 0,
                'price' => 0,
                'attrs' => $attrs,
                'image' => ($color && isset($colorImage[$color])) ? $colorImage[$color] : $fallbackImage,
                'available' => true,
            ]);
            $created++;
        }

        return $created;
    }

    private function cross(array $sizes, array $colors): array
    {
        $out = [];
        if ($sizes && $colors) {
            foreach ($colors as $c)
                foreach ($sizes as $s)
                    $out[] = [$s, $c];
        } elseif ($sizes) {
            foreach ($sizes as $s)
                $out[] = [$s, null];
        } elseif ($colors) {
            foreach ($colors as $c)
                $out[] = [null, $c];
        }
        return $out;
    }

    public function previewFromState(array $optionsState): array
    {
        // $optionsState: [ ['name'=>'Size','values'=>[['name'=>'41'],...]], ['name'=>'Color', ...] ]
        $axes = [];
        foreach ($optionsState as $opt) {
            $name = trim((string) ($opt['name'] ?? ''));
            if (!$name)
                continue;
            $vals = collect($opt['values'] ?? [])->pluck('name')->filter()->unique()->values()->all();
            if ($vals)
                $axes[$name] = $vals;
        }
        // декартово произведение
        $combos = [[]];
        foreach ($axes as $axis => $vals) {
            $new = [];
            foreach ($combos as $base) {
                foreach ($vals as $v) {
                    $tmp = $base;
                    $tmp[$axis] = (string) $v;
                    $new[] = $tmp;
                }
            }
            $combos = $new;
        }
        return $combos;
    }

    public function generateFromState(\App\Models\Product $product, array $optionsState): int
    {
        $combos = $this->previewFromState($optionsState);
        return $this->generateCombos($product, $combos); // твой существующий метод, который создаёт недостающие варианты
    }

    private function normalizeAttrs($attrs): string
    {
        if (is_string($attrs)) {
            $decoded = json_decode($attrs, true);
            $attrs = is_array($decoded) ? $decoded : [];
        } elseif (!is_array($attrs)) {
            $attrs = [];
        }
        ksort($attrs);
        return json_encode($attrs, JSON_UNESCAPED_UNICODE);
    }

    private function makeSku(Product $p, ?string $color, ?string $size): string
    {
        $base = $p->sku ? strtoupper(preg_replace('/\s+/', '-', $p->sku)) : 'P' . $p->id;
        $parts = [$base];
        if ($color)
            $parts[] = strtoupper(preg_replace('/\s+/', '-', $color));
        if ($size)
            $parts[] = strtoupper(preg_replace('/\s+/', '-', $size));
        return implode('-', $parts);
    }

    private function makeBarcode(): string
    {
        // простая заглушка EAN13-подобная
        $n = '';
        for ($i = 0; $i < 13; $i++)
            $n .= random_int(0, 9);
        return $n;
    }
}