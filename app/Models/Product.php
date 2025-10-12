<?php

namespace App\Models;

use App\Enums\DiscountEnum;
use App\Enums\GenderEnum;
use App\Enums\RoleEnum;
use App\Enums\ProductStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany, MorphMany};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Arr;


class Product extends Model
{
    use BaseQuery, HasFactory, BaseModel, \Illuminate\Database\Eloquent\SoftDeletes;

    private static function normalizeUploadPath($val): ?string
    {
        if ($val instanceof TemporaryUploadedFile) {
            // сохраняем на 'public', путь вернётся как 'products/....'
            return $val->store('products', 'public');
        }
        if (is_array($val)) {
            // может быть ['path' => 'products/a.jpg'] или ['products/a.jpg']
            return $val['path'] ?? (reset($val) ?: null);
        }
        return is_string($val) ? ($val !== '' ? $val : null) : null;
    }

    public function setGalleryAttribute($value): void
    {
        $paths = [];

        // допускаем: строка, список строк, список файлов, null
        $raw = is_null($value) ? [] : (is_array($value) ? $value : [$value]);

        foreach ($raw as $item) {
            $p = self::normalizeUploadPath($item);
            if ($p)
                $paths[] = $p;
        }

        // всегда JSON в базе
        $this->attributes['gallery'] = json_encode(array_values(array_unique($paths)), JSON_UNESCAPED_UNICODE);
    }
    public function getGalleryAttribute($value): array
    {
        if (is_array($value))
            return $value;        // уже кастом вернул массив
        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return array_values(array_filter($decoded, fn($x) => is_string($x) && $x !== ''));
            }
        }
        return []; // по умолчанию
    }

    public function setColorImagesAttribute($value): void
    {
        // Если $value уже нормальный ['Black' => ['path1', ...]], просто проставляем:
        if (is_array($value)) {
            $this->attributes['color_images'] = json_encode($value, JSON_UNESCAPED_UNICODE);
            return;
        }

        // Если пришла строка JSON — попробуем распарсить
        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $this->attributes['color_images'] = json_encode($decoded, JSON_UNESCAPED_UNICODE);
                return;
            }
        }

        // Иначе пустой массив
        $this->attributes['color_images'] = json_encode([], JSON_UNESCAPED_UNICODE);
    }

    public function getColorImagesAttribute($value): array
    {
        if (is_array($value))
            return $value;
        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                // подчистим мусор, приведём к строкам
                $clean = [];
                foreach ($decoded as $color => $list) {
                    $arr = array_values(array_filter((array) $list, fn($x) => is_string($x) && $x !== ''));
                    if ($arr)
                        $clean[(string) $color] = $arr;
                }
                return $clean;
            }
        }
        return [];
    }

    public function variantOptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(VariantOption::class, 'product_id');
    }
    protected $casts = [
        'offers' => 'json',
        'is_active' => 'bool',
        'gallery' => 'array',
        'options' => 'array',
        'color_images' => 'array',
    ];

    // Product.php (фрагменты)
    public function upsertVariantOptions(array $map): void
    {
        // $map = ['Size' => ['41','42'], 'Color' => ['Black','White']]
        $this->loadMissing('variantOptions.values');
        foreach ($map as $name => $values) {
            $opt = $this->variantOptions()->firstOrCreate(['name' => $name]);
            foreach ($values as $val) {
                $opt->values()->firstOrCreate(['name' => (string) $val]);
            }
        }
    }

    // App/Models/Product.php

    public function matrixToVariants(array $rows): int
    {
        \Log::info('matrixToVariants called', [
            'rows_count' => count($rows),
            'rows_stocks' => array_map(fn($r) => [
                'id' => $r['id'] ?? null,
                'stock' => $r['stock'] ?? null,
                'sku' => $r['sku'] ?? null
            ], $rows),
        ]);

        $created = 0;
        foreach ($rows as $r) {
            $attrs = (array) ($r['attrs'] ?? []);
            ksort($attrs); // нормализуем порядок ключей

            // Postgres jsonb равенство
            $v = $this->variants()
                ->whereRaw('attrs::jsonb = ?::jsonb', [json_encode($attrs, JSON_UNESCAPED_UNICODE)])
                ->first();

            if (!$v) {
                $v = $this->variants()->make();
            }

            $oldStock = $v->stock ?? 0;

            $v->price = (int) ($r['price'] ?? 0);
            $v->stock = (int) ($r['stock'] ?? 0);
            $v->attrs = $attrs;
            $v->image = $r['image'] ?? null;
            $v->sku = $r['sku'] ?? null;

            \Log::info('matrixToVariants: saving variant', [
                'variant_id' => $v->id ?? 'new',
                'sku' => $v->sku,
                'old_stock' => $oldStock,
                'new_stock' => $v->stock,
                'attrs' => $attrs,
            ]);

            $v->save(); // barcode и available выставятся в хуках Variant
            $created++;
        }

        \Log::info('matrixToVariants: complete', ['created' => $created]);

        return $created;
    }

    public function findVariantIdBy(?int $sizeId, ?int $colorId): ?int
    {
        $size = $sizeId ? \App\Models\Size::find($sizeId)?->name : null;
        $color = $colorId ? \DB::table('colors')->where('id', $colorId)->value('name') : null;

        $q = $this->variants();
        if ($size)
            $q->where('attrs->Size', $size);
        if ($color)
            $q->where('attrs->Color', $color);

        $id = $q->value('id');
        return $id ? (int) $id : null;
    }

    public function imageForColor(?string $colorName): ?string
    {
        if (!$colorName)
            return $this->thumbnail;
        // если используешь ProductColors с path — маппинг здесь
        $mediaPath = \DB::table('product_colors')
            ->join('colors', 'colors.id', '=', 'product_colors.color_id')
            ->where('product_colors.product_id', $this->id)
            ->where('colors.name', $colorName)
            ->value('path'); // если нет path — вернём основное фото
        return $mediaPath ? url($mediaPath) : $this->thumbnail;
    }

    protected $attributes = [
        'cost_price' => 0,
    ];

    protected $fillable = [
        'is_active',
        'name_ru',          // 👈 добавь это
        'desc_ru',
        'channel',
        'stock_location_id',
        'gallery',
        'options',
        'color_images',
        'image',
        'gender',
        'price',
        'discount',
        'count',
        'status',
        'key',
        'sku',
        'barcode',
        'offers',
        'label',
        'views',
        'cost_price',
    ];

    /* ===================== Relations ===================== */

    // App\Models\Product.php
    public function buildVariantState(): array
    {
        // 1) Осі (Size/Color) → значения
        $options = [];
        // если есть вариант-опции в БД — читаем их
        if (Schema::hasTable('variant_options') && Schema::hasTable('variant_option_values')) {
            $opts = \App\Models\VariantOption::with('values')
                ->where('product_id', $this->id)->get();
            foreach ($opts as $opt) {
                $values = $opt->values->pluck('name')->filter()->unique()->values()->all();
                if ($values) {
                    $options[] = [
                        'name' => $opt->name, // "Size" / "Color"
                        'values' => $values, // ["41","42"] / ["Black","White"]
                    ];
                }
            }
        }

        // fallback: если таблиц/данных опций нет — строим из variants.attrs
        if (empty($options)) {
            $sizes = [];
            $colors = [];
            foreach ($this->variants()->get(['attrs']) as $v) {
                $a = (array) ($v->attrs ?? []);
                if (isset($a['Size']))
                    $sizes[] = (string) $a['Size'];
                if (isset($a['Color']))
                    $colors[] = (string) $a['Color'];
            }
            $sizes = array_values(array_unique(array_filter($sizes)));
            $colors = array_values(array_unique(array_filter($colors)));
            if ($sizes)
                $options[] = ['name' => 'Size', 'values' => $sizes];
            if ($colors)
                $options[] = ['name' => 'Color', 'values' => $colors];
        }

        // 2) Строки редактора вариантов + карта stocks
        $rows = [];
        $stocks = [];  // 👈 ДОБАВЛЕНО

        foreach ($this->variants()->get(['id', 'sku', 'price', 'stock', 'image', 'attrs', 'barcode']) as $v) {
            $attrs = (array) $v->attrs;
            $title = $attrs
                ? implode(' / ', array_map(
                    fn($k, $val) => "{$k}: {$val}",
                    array_keys($attrs),
                    array_values($attrs)
                ))
                : ($v->sku ?: 'Вариант');

            $row = [
                'id' => $v->id,
                'title' => $title,
                'attrs' => $attrs,
                'price' => (int) $v->price,
                'stock' => (int) $v->stock,
                'available' => (int) $v->stock > 0,
                'image' => $v->image,
                'sku' => $v->sku,
                'barcode' => (string) ($v->barcode ?? ''),
            ];

            $rows[] = $row;

            // ✅ ДОБАВЛЕНО: Формируем карту stocks по ключу варианта
            $rowKey = 'id:' . $v->id;
            $stocks[$rowKey] = (int) $v->stock;
        }

        return [
            'variant_options' => $options,
            'variants_draft' => $rows,
            'variants_editor' => $rows,
            'stocks' => $stocks,  // 👈 ДОБАВЛЕНО
        ];
    }

    public function inventoryLevels(): HasMany
    {
        return $this->hasMany(InventoryLevel::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function stockLocation(): BelongsTo
    {
        return $this->belongsTo(StockLocation::class, 'stock_location_id');
    }

    public function sizeImage(): BelongsTo
    {
        return $this->belongsTo(SizeInfo::class, 'size_infos_id');
    }

    // Старый pivot «цвета товара» (оставляем ради совместимости для автогенерации вариантов/картинок)
    public function colors(): HasMany
    {
        return $this->hasMany(ProductColors::class, 'product_id');
    }

    // Старый pivot «размеры товара» (учёт остатков по размеру)
    public function productSizes(): HasMany
    {
        return $this->hasMany(ProductSize::class, 'product_id');
    }

    public function sizes(): BelongsToMany
    {
        return $this->belongsToMany(Size::class, 'product_sizes', 'product_id', 'size_id')
            ->withPivot('count', 'barcode', 'sku')
            ->withTimestamps();
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Media::class, 'taggable');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function subcategories(): BelongsToMany
    {
        return $this->belongsToMany(SubCategory::class, 'product_subcategories', 'product_id', 'sub_category_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tags', 'product_id', 'tag_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    // Новый вариант: оси (options) и их значения (option_items)
    public function options(): HasMany
    {
        return $this->hasMany(Option::class, 'product_id');
    }

    // BC alias, если где-то используется старое имя
    public function product_options(): HasMany
    {
        return $this->options();
    }

    public function variants(): HasMany
    {
        return $this->hasMany(Variant::class, 'product_id');
    }

    /* ===================== Scopes & Accessors ===================== */

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function scopeWithCatalogData(Builder $q): Builder
    {
        return $q->select([
            'id',
            \DB::raw("COALESCE(name_ru, '') as name"),
            'price',
            \DB::raw("NULLIF(image,'') as image"),
            \DB::raw("COALESCE(is_active, TRUE) as is_active"),
        ]);
    }

    public function getThumbnailAttribute(): ?string
    {
        return $this->image ? url($this->image) : null;
    }

    /* ===================== Helpers ===================== */

    /** Карта осей: ['Size' => ['41','42'], 'Color' => ['Black',...]] с фолбэком на legacy-таблицы */
    public function getOptionsMap(): array
    {
        $map = [];

        // Новый источник: options + option_items
        $this->loadMissing(['options.items']);
        foreach ($this->options as $opt) {
            $vals = $opt->items->pluck('name')->filter()->unique()->values()->all();
            if ($vals) {
                $map[$opt->name] = $vals;
            }
        }

        // Фолбэк для «размеров»
        if (empty($map['Size']) && Schema::hasTable('product_sizes')) {
            $sizes = $this->sizes->pluck('name')->filter()->unique()->values()->all();
            if ($sizes) {
                $map['Size'] = $sizes;
            }
        }

        // Фолбэк для «цветов»
        if (empty($map['Color']) && Schema::hasTable('product_colors')) {
            $colorNames = ProductColors::query()
                ->where('product_id', $this->id)
                ->join('colors', 'colors.id', '=', 'product_colors.color_id')
                ->pluck('colors.name')
                ->filter()->unique()->values()->all();
            if ($colorNames) {
                $map['Color'] = $colorNames;
            }
        }

        return $map;
    }

    /** Черновой предпросмотр комбинаций (без записи в БД) */
    public function getVariantCombosPreview(): array
    {
        $map = $this->getOptionsMap();
        $sizes = $map['Size'] ?? [];
        $colors = $map['Color'] ?? [];

        $out = [];
        if ($sizes && $colors) {
            foreach ($colors as $c) {
                foreach ($sizes as $s) {
                    $out[] = ['Size' => (string) $s, 'Color' => (string) $c];
                }
            }
        } elseif ($sizes) {
            foreach ($sizes as $s) {
                $out[] = ['Size' => (string) $s];
            }
        } elseif ($colors) {
            foreach ($colors as $c) {
                $out[] = ['Color' => (string) $c];
            }
        }
        return $out;
    }

    /** Суммарный остаток по размерам (legacy-pivot). Возвращает null, если данных нет. */
    public function totalStock(): ?int
    {
        $this->loadMissing('sizes');
        if ($this->sizes->isEmpty())
            return null;
        return (int) $this->sizes->sum(fn($s) => (int) ($s->pivot->count ?? 0));
    }

    public function stockBySize(int $sizeId): int
    {
        $this->loadMissing('sizes');
        $size = $this->sizes->firstWhere('id', $sizeId);
        return (int) ($size?->pivot?->count ?? 0);
    }

    /** Есть скидка? */
    public function isDiscount(): bool
    {
        $discount = (int) $this->discount;
        return $discount !== 0 && $discount != $this->price;
    }

    public function getProductPrice(): string
    {
        return number_format($this->price) . ' ' . __('currency');
    }

    public function getPrice(): string
    {
        return number_format(($this->discount ?: $this->price)) . ' ' . __('currency');
    }

    public function getPriceNumber(): int
    {
        if (Auth::check() && Auth::user()->is_first_order) {
            return (int) round($this->price - ($this->price / 100 * DiscountEnum::FIRST_ORDER));
        }
        return (int) round($this->discount ?: $this->price);
    }

    public function getDiscountNumber()
    {
        if (request()->bearerToken() && $user = Auth::guard('sanctum')->user()) {
            Auth::setUser($user);
        }
        if (Auth::check() && Auth::user()->is_first_order) {
            return (int) round($this->price - ($this->price / 100 * DiscountEnum::FIRST_ORDER));
        }
        return (int) round($this->discount ?: 0) ?: null;
    }

    public function categoryNames(): string
    {
        return implode(' & ', array_column($this->categories->toArray(), 'name_ru'));
    }

    public function subCategoryNames(): string
    {
        return implode(' & ', array_column($this->subcategories->toArray(), 'name_ru'));
    }

    public function stockInLocation(int $stockLocationId): int
    {
        return (int) $this->inventoryLevels()
            ->where('stock_location_id', $stockLocationId)
            ->sum('qty_on_hand');
    }

    /* ===================== Boot ===================== */

    public function coverFor(?string $color): ?string
    {
        if ($color && isset($this->color_images[$color])) {
            $ci = $this->color_images[$color];
            if (is_array($ci) && !empty($ci))
                return $ci[0];
            if (is_string($ci) && $ci !== '')
                return $ci;
        }
        return $this->gallery[0] ?? $this->image;
    }

    public function galleryFor(?string $color): array
    {
        if ($color && isset($this->color_images[$color])) {
            $ci = $this->color_images[$color];
            if (is_array($ci))
                return $ci;
            if (is_string($ci) && $ci !== '')
                return [$ci];
        }
        return $this->gallery ?? [];
    }

    protected static function booted(): void
    {
        // Публичный глобальный скоуп
        static::addGlobalScope('active_gender', function (Builder $q) {
            if (request()->is('admin/*') || request()->routeIs('filament.*'))
                return;

            if (auth()->user()?->role !== RoleEnum::ADMIN) {
                $gender = request()->header('gender')
                    ?? session('gender', GenderEnum::MALE);
                $q->where('gender', $gender)->where('is_active', true);
            }
        });

        // Автозаполнение статусов/цен
        static::saving(function (Product $p) {
            $p->is_active = !empty($p->image);
            $p->channel = $p->is_active ? 'online' : 'warehouse';

            if (empty($p->price) && $p->cost_price > 0) {
                $p->price = $p->cost_price * 1.3;
            }
        });

        // Глубокая зачистка при forceDelete
        static::forceDeleted(function (Product $p) {
            try {
                if (!empty($p->image))
                    \Storage::delete($p->image);
                foreach ($p->images as $img) {
                    if (!empty($img->path))
                        \Storage::delete($img->path);
                    $img->delete();
                }
            } catch (\Throwable) {
            }

            $p->sizes()->detach();
            $p->categories()->detach();
            $p->subcategories()->detach();
            $p->tags()->detach();

            $p->colors()->delete();
            $p->productSizes()->delete();
            $p->options()->delete();
            $p->inventoryLevels()->delete();
            $p->stockMovements()->delete();

            Like::where('product_id', $p->id)->delete();
        });
    }
}