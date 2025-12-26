<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\RoleEnum;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements HasName
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use BaseModel;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'phone',
        'password',
        'address_id',
        'is_first_order',
        'balance',
        'level',
        'total_spent',
        'card',
        'email',
        'fcm_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'fcm_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'verified_at' => 'datetime',
        'password'    => 'hashed',
    ];

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function OrderGroup(): HasMany
    {
        return $this->hasMany(OrderGroup::class, 'user_id');
    }

    public function baskets(): HasMany
    {
        return $this->hasMany(Basket::class);
    }

    /**
     * return User like products
     *
     * @return BelongsToMany
     */
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'likes', 'user_id', 'product_id');
    }

    public function getFilamentName(): string
    {
        return $this->getAttributeValue('email') ?? __('None');
    }

    /**
     * @param $panel
     * @return bool
     */
    public function canAccessPanel($panel): bool
    {
        $user = Auth::user();

        return $user->role == RoleEnum::ADMIN;
    }
}
