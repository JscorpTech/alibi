<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventory_levels', function (Blueprint $t) {
            $t->id();
            $t->foreignId('product_id')->constrained('products');
            $t->foreignId('stock_location_id')->constrained();
            $t->integer('qty_on_hand')->default(0);
            $t->integer('qty_reserved')->default(0);
            $t->unique(['product_id', 'stock_location_id']);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_levels');
    }
};