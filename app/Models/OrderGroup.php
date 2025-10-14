<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderGroup extends Model
{
    use HasFactory;
    use BaseModel;

    protected $fillable = [
        // клиент из приложения
        'user_id',
        'status',
        'address_id',
        'source',
        'cashier_id',
        'payment_method',
        'paid_at',
        'order_number',
        'total',
        'comment',
        'location_id',
        'type',
        'original_group_id',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'total'   => 'integer',
        'type'    => 'string',
    ];

    // --- Связи ---

    /** Покупатель (клиент из приложения) */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    

    /** Кассир (кто провёл продажу в POS) */
    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    /** Позиции заказа */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /** Адрес доставки */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    // --- Утилиты ---

    /** Итог по позициям (если не используешь кешированное поле total) */
    public function getTotalPrice(): int
    {
        $total = 0;
        foreach ($this->orders as $order) {
            $total += (int) $order->getTotalPrice();
        }
        return (int) $total;
    }
}