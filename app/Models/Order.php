<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'discount',          // скидка за 1 шт
        'count',
        'channel',           // ← оставь только если колонка есть
        'stock_location_id', // ← оставь только если колонка есть
        'cashier_id',        // ← оставь только если колонка есть
        'original_order_id',
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

    public function getVariantImageUrlAttribute(): ?string
    {
        return $this->variant?->coverUrl();
    }
    // -------- Relations --------
    public function group()
    {
        return $this->belongsTo(\App\Models\OrderGroup::class, 'order_group_id');
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

    // -------- Helpers --------

    public function getTotalPrice(): int
    {
        $unit = max(0, (int) $this->price - (int) $this->discount);
        return $unit * (int) $this->count;
    }
}