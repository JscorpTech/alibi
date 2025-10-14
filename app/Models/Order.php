<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes, HasFactory, BaseModel;

    protected $fillable = [
        'order_group_id',
        'user_id',
        'product_id',
        'variant_id',
        'size_id',
        'color_id',
        'price',
        'discount',
        'count',
        'channel',              // если используешь
        'stock_location_id',    // если используешь
        'cashier_id',           // если используешь
        'original_order_id',    // ⭐ УЖЕ ЕСТЬ
        
        // ⭐ ДОБАВИТЬ ЭТИ (если нет в БД - пропусти, добавим через миграцию)
        'product_name',         // снимок названия товара
        'variant_sku',          // снимок SKU
    ];

    protected $casts = [
        'order_group_id' => 'integer',
        'user_id' => 'integer',
        'product_id' => 'integer',
        'variant_id' => 'integer',
        'size_id' => 'integer',
        'color_id' => 'integer',
        'price' => 'integer',
        'discount' => 'integer',
        'count' => 'integer',
        'stock_location_id' => 'integer',
        'cashier_id' => 'integer',
        'original_order_id' => 'integer',
    ];

    protected $appends = ['variant_image_url'];

    // ========================================
    // ACCESSORS (твои существующие)
    // ========================================

    public function getVariantImageUrlAttribute(): ?string
    {
        return $this->variant?->coverUrl();
    }

    // ========================================
    // RELATIONSHIPS (твои существующие)
    // ========================================

    public function group(): BelongsTo
    {
        return $this->belongsTo(OrderGroup::class, 'order_group_id');
    }

    public function orderGroup(): BelongsTo
    {
        return $this->belongsTo(OrderGroup::class, 'order_group_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(Variant::class, 'variant_id');
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class, 'size_id');
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    // ⭐ НОВЫЕ RELATIONSHIPS

    /**
     * Оригинальная позиция заказа (для возврата)
     */
    public function originalOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'original_order_id');
    }

    /**
     * Возвраты по этой позиции
     */
    public function returnOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'original_order_id');
    }

    // ========================================
    // HELPERS (твои существующие)
    // ========================================

    public function getTotalPrice(): int
    {
        $unit = max(0, (int) $this->price - (int) $this->discount);
        return $unit * (int) $this->count;
    }

    // ========================================
    // ⭐ НОВЫЕ МЕТОДЫ
    // ========================================

    /**
     * Сколько осталось можно вернуть по этой позиции
     */
    public function getAvailableForReturnAttribute(): int
    {
        $returned = $this->returnOrders()->sum('count');
        return max(0, $this->count - $returned);
    }

    /**
     * Можно ли вернуть эту позицию?
     */
    public function canBeReturned(): bool
    {
        return $this->available_for_return > 0;
    }

    /**
     * Сколько уже возвращено по этой позиции
     */
    public function getReturnedQuantityAttribute(): int
    {
        return (int) $this->returnOrders()->sum('count');
    }
}