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

class Product extends Model
{
    use BaseQuery, HasFactory, BaseModel, \Illuminate\Database\Eloquent\SoftDeletes;

    protected $casts = [
        'offers' => 'json',
        'is_active' => 'bool',
    ];

    protected $attributes = [
        'cost_price' => 0,
    ];

    protected $fillable = [
        'is_active',
        'channel',
        'stock_location_id',
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