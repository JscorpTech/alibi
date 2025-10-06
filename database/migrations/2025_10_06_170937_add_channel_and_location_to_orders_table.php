<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $t) {
    if (!Schema::hasColumn('orders','channel')) {
        $t->string('channel')->default('online'); // online|pos
    }
    if (!Schema::hasColumn('orders','stock_location_id')) {
        $t->foreignId('stock_location_id')->nullable()->constrained('stock_locations');
    }
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};
