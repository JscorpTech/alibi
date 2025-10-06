<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $t) {
            $t->id();
            // ✅ заменили product_variant_id на product_id
            $t->foreignId('product_id')->constrained('products');
            $t->foreignId('stock_location_id')->constrained();
            $t->integer('delta'); // -1 продажа, +1 возврат, +N приход
            $t->string('reason'); // sale|return|reservation|transfer|adjustment
            $t->foreignId('order_id')->nullable()->constrained();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};