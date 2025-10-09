<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_stock_locations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_locations', function (Blueprint $t) {
            $t->id();

            // Код для ссылок и API (уникален, короткий, латиница/подчёркивание)
            $t->string('code', 64)->unique()->comment('Напр: warehouse, store_1, online');

            // Человекочитаемое имя
            $t->string('name', 191);

            // Тип локации: склад, магазин, онлайн-пул и т.д.
            $t->string('type', 16)->default('warehouse')->index()
              ->comment('warehouse|store|online|other');

            // Активность (можно временно выключить точку)
            $t->boolean('is_active')->default(true)->index();

            // Необязательные атрибуты для удобства
            $t->string('address')->nullable();
            $t->string('phone', 32)->nullable();

            // Координаты (если захочешь доставку/карты)
            $t->decimal('lat', 10, 7)->nullable();
            $t->decimal('lng', 10, 7)->nullable();

            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_locations');
    }
};