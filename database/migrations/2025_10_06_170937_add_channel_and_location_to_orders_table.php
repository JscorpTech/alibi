<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_channel_and_location_to_orders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $t) {
            // Канал продажи: online или pos
            if (!Schema::hasColumn('orders', 'channel')) {
                $t->string('channel', 16)
                  ->default('online')
                  ->after('id')
                  ->comment('Канал продажи: online | pos');
            }

            // Где оформлен заказ — склад или торговая точка
            if (!Schema::hasColumn('orders', 'stock_location_id')) {
                $t->foreignId('stock_location_id')
                  ->nullable()
                  ->after('channel')
                  ->constrained('stock_locations')
                  ->nullOnDelete();
            }

            // Кто оформил продажу в POS
            if (!Schema::hasColumn('orders', 'cashier_id')) {
                $t->foreignId('cashier_id')
                  ->nullable()
                  ->after('stock_location_id')
                  ->constrained('users')
                  ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $t) {
            if (Schema::hasColumn('orders', 'cashier_id')) {
                $t->dropConstrainedForeignId('cashier_id');
            }
            if (Schema::hasColumn('orders', 'stock_location_id')) {
                $t->dropConstrainedForeignId('stock_location_id');
            }
            if (Schema::hasColumn('orders', 'channel')) {
                $t->dropColumn('channel');
            }
        });
    }
};