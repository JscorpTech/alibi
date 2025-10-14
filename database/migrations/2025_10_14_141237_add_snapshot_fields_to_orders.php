<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Снимки данных на момент заказа (для истории)
            $table->string('product_name')->nullable()->after('product_id');
            $table->string('variant_sku')->nullable()->after('variant_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['product_name', 'variant_sku']);
        });
    }
};