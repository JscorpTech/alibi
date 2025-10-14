<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Basket extends Model
{
    use HasFactory;
    use BaseModel;

    // теперь работаем через variant_id
    protected $fillable = [
        'user_id',
        'product_id',
        'variant_id',
        'count',
    ];

    /* ===== Связи ===== */

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(Variant::class, 'variant_id');
    }

    // (Опционально) Оставляем пустые-заглушки, чтобы старый код не падал, если вдруг где-то вызывается:
    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class)->whereRaw('1=0'); // не используется
    }
    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class)->whereRaw('1=0'); // не используется
    }

    /* ===== Цены/итоги (логика через variant) ===== */

    /**
     * Приоритет цены:
     *  unit_price = variant.price > 0 ? variant.price : product.price
     */
    public function unitPrice(): int
    {
        $p = (int) ($this->product->price ?? 0);
        $v = (int) ($this->variant->price ?? 0);
        return $v > 0 ? $v : $p;
    }

    /**
     * Итого по строке с учётом count
     * original   = product.price * count
     * discounted = unitPrice()   * count
     */
    public function getLineTotals(): array
    {
        $count = (int) $this->count;
        $pPrice = (int) ($this->product->price ?? 0);
        $applied = $this->unitPrice();

        return [
            'original' => $pPrice * $count,
            'discounted' => $applied * $count,
        ];
    }

    /** Совместимость со старым кодом: возвращаем сумму к оплате по строке */
    public function getTotalPrice(): int
    {
        return $this->getLineTotals()['discounted'];
    }

    /**
     * Совместимость:
     * раньше возвращалась "сумма со скидкой", теперь это просто discounted.
     * Если хочешь вернуть "сумму скидки", верни: original - discounted.
     */
    public function getProductDiscountPrice(): int
    {
        $t = $this->getLineTotals();
        return $t['discounted']; // или ($t['original'] - $t['discounted']) — если нужна разница
    }
}