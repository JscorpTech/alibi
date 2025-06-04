<?php

use App\Enums\OrderStatusEnum;
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
        // Schema::table('order_groups', function (Blueprint $table) {
        //     $table->enum('status', OrderStatusEnum::toArray())
        //     ->default(OrderStatusEnum::PENDING)
        //     ->change();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
    }
};
