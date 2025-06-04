<?php

namespace App\Models;

use App\Enums\DiscountEnum;
use App\Enums\GenderEnum;
use App\Enums\RoleEnum;
use App\Http\Interfaces\LocaleInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Product extends Model implements LocaleInterface
{
    use BaseQuery;
    use HasFactory;
    use BaseModel;
    use SoftDeletes;

    public function jsonFields(): array
    {
        return [
            'count',
        ];
    }

    public function isNullToZero(): array
    {
        return [
            'discount',
        ];
    }

    public function localeFields(): array
    {
        return [
            'name',
            'desc',
        ];
    }
    public $casts = [
        'offers' => 'json',
    ];

    public $fillable = [
        "is_active",
        'image',
        'gender',
        'price',
        'discount',
        'count',
        'status',
        'key',
        'sku',
        'offers',
        'size-image',
        "label",
        "views",
    ];

    public function newQuery(): Builder
    {
        if (Auth::user()?->role != RoleEnum::ADMIN) {
            $request = request();
            $gender = $request->header('gender');

            $gender = $gender ?? Session::get('gender', GenderEnum::MALE);

            return parent::newQuery()->where(['gender' => $gender, "is_active" => true]);
        }
        return parent::newQuery()->where(['is_active' => true]);
    }

    public function categoryNames(): string
    {
        return implode(' & ', array_column($this->categories->toArray(), 'name_ru'));
    }

    public function subCategoryNames(): string
    {
        return implode(' & ', array_column($this->subcategories->toArray(), 'name_ru'));
    }

    public function getPrice(): string
    {
        return number_format(($this->discount != 0 and $this->discount != null) ? $this->discount : $this->price) . ' ' . __('currency');
    }

    public function getPriceNumber(): int
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->is_first_order) {
                return $this->price - ($this->price / 100 * DiscountEnum::FIRST_ORDER);
            }
        }

        return round(($this->discount != 0 and $this->discount != null) ? $this->discount : $this->price);
    }

    public function getDiscountNumber()
    {
        if (request()->bearerToken() && $user = Auth::guard('sanctum')->user()) {
            Auth::setUser($user);
        }

        if (Auth::check()) {
            $user = Auth::user();
            if ($user->is_first_order) {
                return $this->price - ($this->price / 100 * DiscountEnum::FIRST_ORDER);
            }
        }

        return round(($this->discount != 0 and $this->discount != null) ? $this->discount : null);
    }

    public function sizeImage(): BelongsTo
    {
        return $this->belongsTo(SizeInfo::class, 'size_infos_id');
    }

    public function colors(): HasMany
    {
        return $this->hasMany(ProductColors::class, 'product_id');
    }

    public function sizes(): BelongsToMany
    {
        return $this->belongsToMany(Size::class, 'product_sizes', 'product_id');
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Media::class, 'taggable');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function subcategories(): BelongsToMany
    {
        return $this->belongsToMany(SubCategory::class, 'product_subcategories', 'product_id', 'sub_category_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tags', 'product_id', 'tag_id');
    }


    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, "brand_id");
    }

    public function product_options(): HasMany
    {
        return $this->hasMany(Option::class, 'product_id');
    }


    public function options(): HasMany
    {
        return $this->hasMany(ProductOption::class, 'product_id');
    }

    /**
     * Product is discount
     *
     * @return bool
     */
    public function isDiscount(): bool
    {
        $discount = (int) $this->discount;
        if ($discount != 0 and $discount != $this->price) {
            return true;
        }

        return false;
    }

    /**
     * Get product original price
     *
     * @return string
     */
    public function getProductPrice(): string
    {
        return number_format($this->price) . ' ' . __('currency');
    }
}
