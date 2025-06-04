<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Basket extends Model
{
    use HasFactory;
    use BaseModel;

    public $fillable = [
        'product_id',
        'user_id',
        'size_id',
        'color_id',
        'count',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }

    public function getTotalPrice(): float|int
    {
        try {
            return (int) $this->product->price * $this->count;
        } catch (\Throwable $e) {
            return 0;
        }
    }

    public function getProductDiscountPrice(): float|int
    {
        try {
            return $this->product->getDiscountNumber() * $this->count;
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
