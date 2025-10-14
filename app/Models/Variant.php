<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Variant extends Model
{
    protected $table = 'variants';

    protected $fillable = [
        'product_id',
        'sku',
        'barcode',
        'stock',
        'price',
        'attrs',

        'available',
    ];

    protected $casts = [
        'attrs' => 'array',   // {"Size":"41","Color":"Black"}
        'available' => 'boolean',
        'price' => 'integer',
        'stock' => 'integer',
    ];


    public function getSize(): ?string
    {
        return $this->attrs['Size'] ?? null;
    }
    public function getColor(): ?string
    {
        return $this->attrs['Color'] ?? null;
    }
    /* Связи */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /* Удобные скоупы */
    public function scopeInStock(Builder $q): Builder
    {
        return $q->where('stock', '>', 0);
    }

    public function scopeWithAttrs(Builder $q, array $attrs): Builder
    {
        return $q->whereJsonContains('attrs', $attrs);
    } // пример: ['Size'=>'41','Color'=>'Black']

    /* Синхроним available со stock автоматически */
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

        // 3) available синхронизируем со stock (как было)
        static::saving(function (self $v) {
            if ($v->isDirty('stock') && !$v->isDirty('available')) {
                $v->available = (int) $v->stock > 0;
            }
        });
    }

    public function coverUrl(): ?string
    {
        // 1) Фото у варианта
        $path = $this->image['path'] ?? null; // благодаря getImageAttribute()
        if ($path) {
            return Str::startsWith($path, ['http://', 'https://']) ? $path : Storage::url($path);
        }

        // 2) Фото по цвету из продукта -> coverFor(Color)
        $product = $this->relationLoaded('product') ? $this->product : $this->product()->first();
        if ($product) {
            $color = $this->getColor();
            $cover = $product->coverFor($color) ?? ($product->gallery[0] ?? $product->image);
            if ($cover) {
                return Str::startsWith($cover, ['http://', 'https://']) ? $cover : Storage::url($cover);
            }
        }
        return null;
    }

    /** чтобы можно было обращаться как $variant->cover_url */
    public function getCoverUrlAttribute(): ?string
    {
        return $this->coverUrl();
    }
    // app/Models/Variant.php
    public function getImageAttribute($value)
    {
        if (empty($value))
            return null;
        if (is_array($value))
            return $value;
        return ['path' => $value];
    }

    /* Хелперы */
    public function attr(string $key, $default = null)
    {
        return $this->attrs[$key] ?? $default;
    } // $variant->attr('Size')
}