<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Support\Barcode;

class ProductSize extends Model
{
    protected $table = 'product_sizes';

    protected $fillable = [
        'product_id',
        'size_id',
        'count',
        'sku',
        'barcode',
    ];

    // ✅ Автоматическая генерация при создании записи
    protected static function booted(): void
    {
        static::creating(function ($ps) {
            // ---- Генерация BARCODE ----
            if (empty($ps->barcode)) {
                do {
                    $code = Barcode::makeEan13();
                    $exists = self::where('barcode', $code)->exists();
                } while ($exists);

                $ps->barcode = $code;
            }

            // ---- Генерация SKU ----
            if (empty($ps->sku)) {
                $product = $ps->product;
                $size = $ps->size;

                if ($product && $size) {
                    $base = trim($product->sku ?? '', '- ');
                    $sizeName = strtolower($size->name ?? '');
                    $variantSku = $base ? ($base . '-' . $sizeName) : null;

                    if ($variantSku) {
                        $i = 1;
                        while (self::where('sku', $variantSku)->exists()) {
                            $variantSku = $base . '-' . $sizeName . '-' . $i++;
                        }
                        $ps->sku = $variantSku;
                    }
                }
            }
        });
    }

    // 🔗 Связи
    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}