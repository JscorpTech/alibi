<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('cashback')->default(0)->nullable();
            $table->date('delivery_date')->nullable();
            $table->unsignedBigInteger('given_cashback')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_groups', function (Blueprint $table) {
            $table->dropColumn('cashback');
            $table->dropColumn('delivery_date');
            $table->dropColumn('given_cashback');
        });
    }
};
