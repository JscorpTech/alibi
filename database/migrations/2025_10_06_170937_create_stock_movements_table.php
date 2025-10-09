<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_stock_movements_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $t) {
            $t->id();

            // Основная ссылка на товар
            $t->foreignId('product_id')
              ->constrained('products')
              ->cascadeOnDelete();

            // Если учёт по размерам — добавим позже (size_id)
            if (Schema::hasTable('sizes')) {
                $t->foreignId('size_id')
                  ->nullable()
                  ->constrained('sizes')
                  ->nullOnDelete();
            }

            // Откуда или куда — склад/точка продаж
            $t->foreignId('stock_location_id')
              ->constrained('stock_locations')
              ->cascadeOnDelete();

            // Движение: положительное = приход, отрицательное = расход
            $t->integer('delta')->comment('Положительное = приход, отрицательное = расход');

            // Причина: sale|return|receive|transfer|adjustment|reservation
            $t->string('reason', 32)->index();

            // Привязка к заказу (если движение связано с продажей)
            $t->foreignId('order_id')
              ->nullable()
              ->constrained('orders')
              ->nullOnDelete();

            // JSON-мета для гибкости: cashier_id, barcode, комментарии, дата документа и т.п.
            $t->json('meta')->nullable();

            $t->timestamps();

            // Индексы для быстрого поиска
            $t->index(['product_id', 'stock_location_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};