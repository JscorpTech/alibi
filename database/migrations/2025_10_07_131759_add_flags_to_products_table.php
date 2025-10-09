<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $t) {
            if (!Schema::hasColumn('products', 'is_active')) {
                $t->boolean('is_active')->default(false);
            }
            if (!Schema::hasColumn('products', 'channel')) {
                $t->string('channel')->default('warehouse'); // warehouse|online
            }
            if (!Schema::hasColumn('products', 'cost_price')) {
                $t->decimal('cost_price', 12, 2)->default(0); // себестоимость
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
