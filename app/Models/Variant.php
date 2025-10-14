<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Variant extends Model
{
    use SoftDeletes;

    protected $table = 'variants';

    protected $fillable = [
        'product_id',
        'sku',
        'barcode',
        'stock',
        'reserved_stock',      // ⭐ ДОБАВИТЬ (если нет в БД - пропусти, добавим через миграцию)
        'price',
        'attrs',
        'available',
        'image',
    ];

    protected $casts = [
        'attrs' => 'array',        // {"Size":"41","Color":"Black"}
        'available' => 'boolean',
        'price' => 'integer',
        'stock' => 'integer',
        'reserved_stock' => 'integer',  // ⭐ НОВОЕ
    ];

    protected $appends = [
        'available_stock',         // ⭐ НОВОЕ
        'cover_url',
    ];

    // ========================================
    // ⭐ НОВЫЕ ACCESSORS
    // ========================================

    /**
     * Доступный остаток (stock - reserved_stock)
     */
    public function getAvailableStockAttribute(): int
    {
        return max(0, $this->stock - ($this->reserved_stock ?? 0));
    }

    /**
     * Есть ли товар в наличии?
     */
    public function getIsInStockAttribute(): bool
    {
        return $this->available_stock > 0;
    }

    /**
     * Товар закончился?
     */
    public function getIsOutOfStockAttribute(): bool
    {
        return $this->available_stock <= 0;
    }

    /**
     * Низкий остаток? (меньше 5 шт)
     */
    public function getIsLowStockAttribute(): bool
    {
        return $this->available_stock > 0 && $this->available_stock < 5;
    }

    // ========================================
    // ТВОИ СУЩЕСТВУЮЩИЕ МЕТОДЫ (оставляем)
    // ========================================

    public function getSize(): ?string
    {
        return $this->attrs['Size'] ?? null;
    }

    public function getColor(): ?string
    {
        return $this->attrs['Color'] ?? null;
    }

    public function coverUrl(): ?string
    {
        // 1) Фото у варианта
        $path = $this->image['path'] ?? null;
        if ($path) {
            return Str::startsWith($path, ['http://', 'https://'])
                ? $path
                : Storage::url($path);
        }

        // 2) Фото по цвету из продукта
        $product = $this->relationLoaded('product')
            ? $this->product
            : $this->product()->first();

        if ($product) {
            $color = $this->getColor();
            $cover = $product->coverFor($color) ?? ($product->gallery[0] ?? $product->image);
            if ($cover) {
                return Str::startsWith($cover, ['http://', 'https://'])
                    ? $cover
                    : Storage::url($cover);
            }
        }

        return null;
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->coverUrl();
    }

    public function getImageAttribute($value)
    {
        if (empty($value))
            return null;
        if (is_array($value))
            return $value;
        return ['path' => $value];
    }

    public function attr(string $key, $default = null)
    {
        return $this->attrs[$key] ?? $default;
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // ⭐ НОВОЕ
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'variant_id');
    }

    // ========================================
    // SCOPES (твои существующие)
    // ========================================

    public function scopeInStock(Builder $q): Builder
    {
        return $q->where('stock', '>', 0);
    }

    public function scopeWithAttrs(Builder $q, array $attrs): Builder
    {
        return $q->whereJsonContains('attrs', $attrs);
    }

    // ⭐ НОВЫЕ SCOPES
    public function scopeAvailable(Builder $q): Builder
    {
        return $q->whereRaw('(stock - COALESCE(reserved_stock, 0)) > 0');
    }

    public function scopeLowStock(Builder $q, int $threshold = 5): Builder
    {
        return $q->whereRaw('(stock - COALESCE(reserved_stock, 0)) > 0')
            ->whereRaw('(stock - COALESCE(reserved_stock, 0)) < ?', [$threshold]);
    }

    // ========================================
    // BOOT (твоя существующая логика)
    // ========================================

    protected static function booted(): void
    {
        // 1) Генерим штрихкод только если пустой
        static::creating(function (self $v) {
            if (empty($v->barcode)) {
                do {
                    $code = (string) random_int(1000000000000, 9999999999999);
                } while (
                    \DB::table('variants')->where('barcode', $code)->exists() ||
                    \DB::table('products')->where('barcode', $code)->exists()
                );
                $v->barcode = $code;
            }
        });

        // 2) Копируем image на другие варианты того же цвета
        static::created(function (self $v) {
            $color = $v->attr('Color');
            if ($color && !empty($v->image)) {
                self::where('product_id', $v->product_id)
                    ->where('id', '!=', $v->id)
                    ->whereNull('image')
                    ->whereRaw("attrs ->> 'Color' = ?", [$color])
                    ->update(['image' => $v->image]);
            }
        });

        // 3) available синхронизируем со stock
        static::saving(function (self $v) {
            if ($v->isDirty('stock') && !$v->isDirty('available')) {
                // ⭐ ОБНОВЛЕНО: учитываем reserved_stock
                $availableStock = max(0, $v->stock - ($v->reserved_stock ?? 0));
                $v->available = $availableStock > 0;
            }
        });
    }

    // ========================================
    // ⭐ НОВЫЕ МЕТОДЫ
    // ========================================

    /**
     * Можно ли выполнить заказ на указанное количество?
     */
    public function canFulfill(int $quantity): bool
    {
        return $this->available_stock >= $quantity;
    }

    /**
     * Полное название варианта (Product + Size + Color)
     */
    public function getFullNameAttribute(): string
    {
        $name = $this->product->name ?? '';

        $attrs = collect($this->attrs)
            ->filter()
            ->implode(', ');

        return $attrs ? "{$name} ({$attrs})" : $name;
    }
}