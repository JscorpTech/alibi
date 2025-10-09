<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('product_sizes', function (Blueprint $t) {
            if (!Schema::hasColumn('product_sizes', 'barcode')) {
                // оставляем 13 символов под EAN-13; если хочешь QR/длинные — сделай string(64)
                $t->char('barcode', 13)->nullable()->unique()->after('count');
            }
            // (опционально) SKU для варианта
            if (!Schema::hasColumn('product_sizes', 'sku')) {
                $t->string('sku', 64)->nullable()->unique()->after('barcode');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_sizes', function (Blueprint $t) {
            if (Schema::hasColumn('product_sizes', 'sku')) {
                $t->dropUnique('product_sizes_sku_unique');
                $t->dropColumn('sku');
            }
            if (Schema::hasColumn('product_sizes', 'barcode')) {
                $t->dropUnique('product_sizes_barcode_unique');
                $t->dropColumn('barcode');
            }
        });
    }
};