<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // ✅ добавили импорт

class StockLocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('stock_locations')->updateOrInsert(
            ['code' => 'warehouse'],
            ['name' => 'Склад', 'type' => 'warehouse']
        );

        DB::table('stock_locations')->updateOrInsert(
            ['code' => 'store_1'],
            ['name' => 'Магазин ТЦ', 'type' => 'store']
        );
    }
}