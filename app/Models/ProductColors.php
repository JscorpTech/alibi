<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class ProductColors extends Model
{
    use HasFactory;
    use BaseModel;

    public $fillable = [
        'product_id',
        'color_id',
        'image_id',
    ];

    public function image(): MorphOne
    {
        return $this->morphOne(Media::class, 'taggable');
    }

    /**
     * Get product
     *
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class, 'color_id');
    }
}
