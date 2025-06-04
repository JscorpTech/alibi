<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    use HasFactory;
    use BaseModel;

    public $fillable = [
        'price',
        'product_id',
        'count',
        'color_id',
        'size_id',
        'discount',
        'order_group_id',
    ];

    /**
     * @return float|int
     *
     * Get product total price
     */
    public function getTotalPrice(): float|int
    {
        return $this->price * $this->count;
    }

    public function OrderGroup(): BelongsTo
    {
        return $this->belongsTo(OrderGroup::class);
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class, 'size_id');
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
