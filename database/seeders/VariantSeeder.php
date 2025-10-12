<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VariantSeeder extends Seeder
{
    public function run(): void
    {
        Variant::create([
            'product_id' => 501,
            'sku' => 'N501-40-BLK',
            'price' => 899000,
            'stock' => 3,
            'attrs' => ['Size' => '40', 'Color' => 'Black'],
            'image' => 'https://cdn/app/p/501_40_blk.jpg',
            // barcode НЕ указываем — модель сгенерирует
        ]);

        Variant::create([
            'product_id' => 501,
            'sku' => 'N501-41-BLK',
            'price' => 899000,
            'stock' => 2,
            'attrs' => ['Size' => '41', 'Color' => 'Black'],
            // image опционально
            // barcode НЕ указываем
        ]);
    }
}