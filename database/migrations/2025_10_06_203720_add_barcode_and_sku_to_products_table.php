<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_barcode_and_sku_to_products_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $t) {
            // barcode: до 64 символов, часто длиннее чем 32 (EAN-13, QR, UUID и т.д.)
            if (!Schema::hasColumn('products', 'barcode')) {
                $t->string('barcode', 64)->nullable()->unique()->after('id');
            }

            // sku: внутренний артикул, 64 символов обычно хватает
            if (!Schema::hasColumn('products', 'sku')) {
                $t->string('sku', 64)->nullable()->unique()->after('barcode');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $t) {
            // имена индексов по конвенции: {table}_{column}_unique
            if (Schema::hasColumn('products', 'sku')) {
                $t->dropUnique('products_sku_unique');
            }
            if (Schema::hasColumn('products', 'barcode')) {
                $t->dropUnique('products_barcode_unique');
            }

            $drop = [];
            if (Schema::hasColumn('products', 'sku'))
                $drop[] = 'sku';
            if (Schema::hasColumn('products', 'barcode'))
                $drop[] = 'barcode';
            if ($drop)
                $t->dropColumn($drop);
        });
    }
};