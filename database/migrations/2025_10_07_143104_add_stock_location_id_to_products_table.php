<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'stock_location_id')) {
                $table->foreignId('stock_location_id')
                      ->nullable()
                      ->constrained('stock_locations')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'stock_location_id')) {
                $table->dropForeign(['stock_location_id']);
                $table->dropColumn('stock_location_id');
            }
        });
    }
};