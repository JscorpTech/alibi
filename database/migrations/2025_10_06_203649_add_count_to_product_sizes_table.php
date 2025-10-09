<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_count_to_product_sizes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ⚠️ Это временный вариант: поле count полезно,
        // если ты ещё не вынес остатки в inventory_levels.
        // Позже можно будет удалить, когда учёт пойдёт через склады.
        Schema::table('product_sizes', function (Blueprint $t) {
            if (!Schema::hasColumn('product_sizes', 'count')) {
                $t->integer('count')
                  ->default(0)
                  ->after('size_id')
                  ->comment('Количество товара по размеру (временно, до внедрения складского учёта)');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_sizes', function (Blueprint $t) {
            if (Schema::hasColumn('product_sizes', 'count')) {
                $t->dropColumn('count');
            }
        });
    }
};