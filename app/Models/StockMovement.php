<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $table = 'stock_movements';

    protected $fillable = [
        // Твои существующие поля
        'product_id',
        'size_id',
        'stock_location_id',
        'delta',
        'reason',
        'order_id',
        'meta',
        
        // ⭐ НОВЫЕ ПОЛЯ (если нет в БД - пропусти, добавим через миграцию)
        'variant_id',           // для работы с вариантами
        'type',                 // детальный тип операции
        'quantity',             // + или -
        'quantity_before',      // было
        'quantity_after',       // стало
        'order_group_id',       // ссылка на группу заказа
        'user_id',              // кто сделал операцию
        'note',                 // примечание
    ];

    protected $casts = [
        'delta' => 'integer',
        'quantity' => 'integer',
        'quantity_before' => 'integer',
        'quantity_after' => 'integer',
        'meta' => 'array',
    ];

    // ========================================
    // RELATIONSHIPS (твои существующие)
    // ========================================

    /** Товар */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** Размер (если используется учёт по размерам) */
    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }

    /** Склад / точка продаж */
    public function location(): BelongsTo
    {
        return $this->belongsTo(StockLocation::class, 'stock_location_id');
    }

    /** Заказ, с которым связано движение (если продажа или возврат) */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // ⭐ НОВЫЕ RELATIONSHIPS

    /** Вариант товара */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(Variant::class, 'variant_id');
    }

    /** Группа заказа */
    public function orderGroup(): BelongsTo
    {
        return $this->belongsTo(OrderGroup::class, 'order_group_id');
    }

    /** Пользователь который сделал операцию */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ========================================
    // ТВОИ СУЩЕСТВУЮЩИЕ МЕТОДЫ
    // ========================================

    /**
     * Возврат true, если это приход
     */
    public function isInbound(): bool
    {
        // Работает и для delta и для quantity
        return ($this->delta ?? $this->quantity ?? 0) > 0;
    }

    /**
     * Возврат true, если это расход (продажа)
     */
    public function isOutbound(): bool
    {
        return ($this->delta ?? $this->quantity ?? 0) < 0;
    }

    /**
     * Человеческое описание движения (для отчётов)
     */
    public function description(): string
    {
        // Если есть type - используем его
        if ($this->type) {
            return match ($this->type) {
                'sale_pos' => 'Продажа (POS)',
                'sale_app' => 'Продажа (APP)',
                'return_pos' => 'Возврат (POS)',
                'return_app' => 'Возврат (APP)',
                'cancel_pos' => 'Отмена (POS)',
                'cancel_app' => 'Отмена (APP)',
                'reserve_app' => 'Резервирование',
                'release_reserve' => 'Снятие резерва',
                'purchase' => 'Закупка',
                'adjustment' => 'Корректировка',
                'damage' => 'Списание',
                'inventory' => 'Инвентаризация',
                default => ucfirst($this->type),
            };
        }

        // Fallback на старый reason
        return match ($this->reason) {
            'sale' => 'Продажа',
            'return' => 'Возврат',
            'receive' => 'Поступление',
            'transfer' => 'Перемещение',
            'adjustment' => 'Корректировка',
            default => ucfirst($this->reason ?? 'Неизвестно'),
        };
    }

    // ========================================
    // ⭐ НОВЫЕ МЕТОДЫ
    // ========================================

    /**
     * Scopes для фильтрации
     */
    public function scopeSales($query)
    {
        return $query->whereIn('type', ['sale_pos', 'sale_app'])
            ->orWhere('reason', 'sale');
    }

    public function scopeReturns($query)
    {
        return $query->whereIn('type', ['return_pos', 'return_app'])
            ->orWhere('reason', 'return');
    }

    public function scopeCancels($query)
    {
        return $query->whereIn('type', ['cancel_pos', 'cancel_app']);
    }

    public function scopeReservations($query)
    {
        return $query->where('type', 'reserve_app');
    }

    /**
     * Получить количество (работает и со старым delta и с новым quantity)
     */
    public function getAmountAttribute(): int
    {
        return $this->quantity ?? $this->delta ?? 0;
    }
}