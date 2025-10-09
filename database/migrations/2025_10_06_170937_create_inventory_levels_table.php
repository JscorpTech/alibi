<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_inventory_levels_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventory_levels', function (Blueprint $t) {
            $t->id();

            $t->foreignId('product_id')
              ->constrained('products')
              ->cascadeOnDelete();

            // Если есть таблица sizes — храним разрез по размеру
            if (Schema::hasTable('sizes')) {
                $t->foreignId('size_id')
                  ->nullable()
                  ->constrained('sizes')
                  ->nullOnDelete();
            } else {
                // На случай отсутствия sizes оставим просто nullable FK по имени
                $t->unsignedBigInteger('size_id')->nullable()->index();
            }

            $t->foreignId('stock_location_id')
              ->constrained('stock_locations')
              ->cascadeOnDelete();

            // Текущий остаток и резерв
            $t->unsignedInteger('qty_on_hand')->default(0);
            $t->unsignedInteger('qty_reserved')->default(0);

            // Уникальность записи на комбинацию товар+размер+склад
            $t->unique(['product_id', 'size_id', 'stock_location_id']);

            // Полезные индексы
            $t->index(['product_id']);
            $t->index(['stock_location_id']);

            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_levels');
    }
};