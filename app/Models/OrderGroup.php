<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\OrderStatusEnum;

class OrderGroup extends Model
{
    use HasFactory, BaseModel, SoftDeletes;

    protected $fillable = [
        // Существующие поля
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
        'type',                    // ⭐ УЖЕ ЕСТЬ
        'original_group_id',       // ⭐ УЖЕ ЕСТЬ

        // ⭐ ДОБАВИТЬ ЭТИ (если их нет в БД - пропусти, добавим через миграцию)
        'cashback',                // применённый кэшбэк
        'given_cashback',          // начисленный кэшбэк
        'payment_type',
        'delivery_address',
        'phone',
        'delivery_cost',
        'delivered_at',
        'canceled_at',
        'cancel_reason',
    ];

    protected $casts = [
        'status' => OrderStatusEnum::class,
        'paid_at' => 'datetime',
        'delivered_at' => 'datetime',
        'canceled_at' => 'datetime',
        'total' => 'integer',
        'cashback' => 'integer',
        'given_cashback' => 'integer',
        'delivery_cost' => 'integer',
    ];

    // ========================================
    // RELATIONSHIPS (твои существующие + новые)
    // ========================================

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
        return $this->hasMany(Order::class, 'order_group_id');
    }

    /** Адрес доставки */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    /** Локация (склад/точка продаж) */
    public function location(): BelongsTo
    {
        return $this->belongsTo(StockLocation::class, 'location_id');
    }

    // ⭐ НОВЫЕ RELATIONSHIPS

    /**
     * Оригинальная группа заказа (для возвратов)
     */
    public function originalGroup(): BelongsTo
    {
        return $this->belongsTo(OrderGroup::class, 'original_group_id');
    }

    /**
     * Возвраты по этому заказу
     */
    public function returns(): HasMany
    {
        return $this->hasMany(OrderGroup::class, 'original_group_id')
            ->where('type', 'return');
    }

    /**
     * Движения склада связанные с этим заказом
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'order_group_id');
    }

    // ========================================
    // УТИЛИТЫ (твои существующие)
    // ========================================

    /** Итог по позициям (если не используешь кешированное поле total) */
    public function getTotalPrice(): int
    {
        if ($this->total) {
            return (int) $this->total;
        }

        $total = 0;
        foreach ($this->orders as $order) {
            $total += (int) $order->getTotalPrice();
        }

        return (int) $total;
    }

    // ========================================
    // ⭐ НОВЫЕ МЕТОДЫ
    // ========================================

    /**
     * Это продажа?
     */
    public function isSale(): bool
    {
        return $this->type === 'sale';
    }

    /**
     * Это возврат?
     */
    public function isReturn(): bool
    {
        return $this->type === 'return';
    }

    /**
     * Можно ли отменить заказ?
     */
    public function canBeCanceled(): bool
    {
        return in_array($this->status, [
            OrderStatusEnum::PENDING,
            OrderStatusEnum::SUCCESS,
        ]);
    }

    /**
     * Заказ из мобильного приложения?
     */
    public function isFromApp(): bool
    {
        return $this->source === 'app';
    }

    /**
     * Заказ из POS?
     */
    public function isFromPOS(): bool
    {
        return $this->source === 'pos';
    }

    /**
     * Заказ отменён?
     */
    public function isCanceled(): bool
    {
        return $this->status === OrderStatusEnum::CANCELED;
    }

    /**
     * Заказ завершён?
     */
    public function isCompleted(): bool
    {
        return $this->status === OrderStatusEnum::SUCCESS;
    }

    /**
     * Есть ли возвраты по этому заказу?
     */
    public function hasReturns(): bool
    {
        return $this->returns()->exists();
    }

    /**
     * Получить сумму всех возвратов
     */
    public function getReturnedAmount(): int
    {
        return (int) $this->returns()->sum('total');
    }
}