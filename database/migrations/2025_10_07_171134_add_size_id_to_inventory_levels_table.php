<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('inventory_levels', function (Blueprint $t) {
            if (!Schema::hasColumn('inventory_levels', 'size_id')) {
                // если таблица sizes есть — делаем FK, иначе просто nullable bigInteger
                if (Schema::hasTable('sizes')) {
                    $t->foreignId('size_id')->nullable()
                        ->constrained('sizes')->nullOnDelete();
                } else {
                    $t->unsignedBigInteger('size_id')->nullable()->index();
                }
            }

            // уникальность по товару+размеру+складу
            if (!Schema::hasTable('inv_levels_product_size_location_unique')) {
                $t->unique(
                    ['product_id', 'size_id', 'stock_location_id'],
                    'inv_levels_product_size_location_unique'
                );
            }
        });
    }

    public function down(): void {
        Schema::table('inventory_levels', function (Blueprint $t) {
            if (Schema::hasColumn('inventory_levels', 'size_id')) {
                $t->dropUnique('inv_levels_product_size_location_unique');
                if (Schema::hasTable('sizes')) {
                    $t->dropConstrainedForeignId('size_id');
                } else {
                    $t->dropColumn('size_id');
                }
            }
        });
    }
};