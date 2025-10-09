<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryLevel extends Model
{
    protected $table = 'inventory_levels';

    protected $fillable = [
        'product_id',
        'size_id',
        'stock_location_id',
        'qty_on_hand',
        'qty_reserved',
    ];

    protected $casts = [
        'qty_on_hand'   => 'integer',
        'qty_reserved'  => 'integer',
    ];

    /** Связь с товаром */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** Связь с размером (если размеры используются) */
    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }

    /** Склад / торговая точка */
    public function location(): BelongsTo
    {
        return $this->belongsTo(StockLocation::class, 'stock_location_id');
    }

    /** 
     * Сколько доступно к продаже (без резерва)
     */
    public function available(): int
    {
        return max(0, $this->qty_on_hand - $this->qty_reserved);
    }

    /**
     * Проверка, есть ли остаток на складе
     */
    public function inStock(): bool
    {
        return $this->available() > 0;
    }
}