<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VariantOptionValue extends Model
{
    protected $table = 'variant_option_values';
    protected $fillable = ['variant_option_id', 'name']; // поля из вашей схемы

    public function option(): BelongsTo
    {
        return $this->belongsTo(VariantOption::class, 'variant_option_id');
    }
}