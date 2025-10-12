<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class VariantOption extends Model
{
    protected $table = 'variant_options';
    protected $fillable = ['product_id', 'name']; // position нет в схеме — не указываем

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function values(): HasMany
    {
        return $this->hasMany(VariantOptionValue::class, 'variant_option_id');
    }
}