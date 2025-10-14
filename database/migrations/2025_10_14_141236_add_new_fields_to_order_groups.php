<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_groups', function (Blueprint $table) {
            // Для отмены заказов
            $table->timestamp('canceled_at')->nullable()->after('paid_at');
            $table->text('cancel_reason')->nullable()->after('canceled_at');
            
            // Для доставки
            $table->timestamp('delivered_at')->nullable()->after('paid_at');
            $table->text('delivery_address')->nullable()->after('address_id');
            $table->string('phone', 20)->nullable()->after('delivery_address');
            $table->integer('delivery_cost')->default(0)->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('order_groups', function (Blueprint $table) {
            $table->dropColumn([
                'canceled_at',
                'cancel_reason',
                'delivered_at',
                'delivery_address',
                'phone',
                'delivery_cost',
            ]);
        });
    }
};