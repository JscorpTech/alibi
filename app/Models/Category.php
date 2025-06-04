<?php

namespace App\Models;

use App\Http\Interfaces\LocaleInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model implements LocaleInterface
{
    use HasFactory;
    use BaseModel;
    use SoftDeletes;
    use BaseQuery;

    public function localeFields(): array
    {
        return ['name'];
    }

    public $fillable = [
        'position',
        'gender',
        'image',
        "sortby",
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_categories');
    }

    public function subcategory(): HasMany
    {
        return $this->hasMany(SubCategory::class, 'category_id');
    }
}
