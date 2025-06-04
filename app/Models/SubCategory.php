<?php

namespace App\Models;

use App\Http\Interfaces\LocaleInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCategory extends Model implements LocaleInterface
{
    use HasFactory;
    use BaseModel;
    use SoftDeletes;
    use BaseQuery;

    public function localeFields(): array
    {
        return [
            'name',
        ];
    }

    public $fillable = [
        'category_id',
        'code',
        'position',
        'gender',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_subcategories');
    }
}
