<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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
        'image',
        'available',
    ];

    protected $casts = [
        'attrs' => 'array',   // {"Size":"41","Color":"Black"}
        'available' => 'boolean',
        'price' => 'integer',
        'stock' => 'integer',
    ];

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
        static::saving(function (self $v) {
            // если available явно не передали — выставим по stock
            if ($v->isDirty('stock') && !$v->isDirty('available')) {
                $v->available = (int) $v->stock > 0;
            }
        });
    }

    /* Хелперы */
    public function attr(string $key, $default = null)
    {
        return $this->attrs[$key] ?? $default;
    } // $variant->attr('Size')
}