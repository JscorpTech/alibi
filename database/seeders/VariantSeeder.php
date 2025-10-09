<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VariantSeeder extends Seeder
{
    public function run(): void
    {
        // Пример данных — поправь product_id / пути к картинкам под свою БД
        DB::table('variants')->insert([
            [
                'product_id' => 501,
                'sku' => 'N501-40-BLK',
                'barcode' => '1234565001',
                'stock' => 3,
                'price' => 899000,
                'attrs' => json_encode(['Size' => '40', 'Color' => 'Black']),
                'image' => 'https://cdn/app/p/501_40_blk.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 501,
                'sku' => 'N501-41-BLK',
                'barcode' => '1234565002',
                'stock' => 2,
                'price' => 899000,
                'attrs' => json_encode(['Size' => '41', 'Color' => 'Black']),
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}