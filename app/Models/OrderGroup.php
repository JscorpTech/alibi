<?php

namespace App\Models;

use App\Enums\OrderStatusEnum;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class OrderGroup extends Model
{
    use HasFactory;
    use BaseModel;

    public $fillable = [
        'user_id',
        'status',
        'address_id',
        'payment_type',
        'cashback',
        'delivery_date',
        'giver_cashback',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getTotalPrice()
    {
        $orders = $this->orders()->get();
        $total = 0;
        foreach ($orders as $order) {
            $total += $order->getTotalPrice();
        }

        return $total;
    }

    /**
     * Order address relationship
     *
     * @return BelongsTo
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id');
    }
}
