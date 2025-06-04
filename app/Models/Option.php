<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Option extends Model
{
    use HasFactory,BaseModel;

    public $fillable = [
        "name",
        "product_id"
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OptionItem::class, 'option_id');
    }

    
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
}
