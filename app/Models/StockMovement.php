<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $table = 'stock_movements';

    protected $fillable = [
        'product_id',
        'size_id',
        'stock_location_id',
        'delta',
        'reason',
        'order_id',
        'meta',
    ];

    protected $casts = [
        'delta' => 'integer',
        'meta'  => 'array',
    ];

    /** Товар */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** Размер (если используется учёт по размерам) */
    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }

    /** Склад / точка продаж */
    public function location(): BelongsTo
    {
        return $this->belongsTo(StockLocation::class, 'stock_location_id');
    }

    /** Заказ, с которым связано движение (если продажа или возврат) */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /** 
     * Возврат true, если это приход
     */
    public function isInbound(): bool
    {
        return $this->delta > 0;
    }

    /** 
     * Возврат true, если это расход (продажа)
     */
    public function isOutbound(): bool
    {
        return $this->delta < 0;
    }

    /** 
     * Человеческое описание движения (для отчётов)
     */
    public function description(): string
    {
        return match ($this->reason) {
            'sale'       => 'Продажа',
            'return'     => 'Возврат',
            'receive'    => 'Поступление',
            'transfer'   => 'Перемещение',
            'adjustment' => 'Корректировка',
            default      => ucfirst($this->reason),
        };
    }
}