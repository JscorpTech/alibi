<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_groups', function (Blueprint $table) {
            // ✅ Добавляем поля для POS, но не ломаем логику приложения
            $table->string('source')->default('app'); // 'app' | 'pos'
            $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('payment_method')->nullable(); // 'cash', 'card', 'mixed'
            $table->timestamp('paid_at')->nullable();
            $table->string('order_number')->nullable()->unique();
            $table->integer('total')->nullable();
            $table->string('comment')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('order_groups', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cashier_id');
            $table->dropColumn([
                'source',
                'payment_method',
                'paid_at',
                'order_number',
                'total',
                'comment',
            ]);
        });
    }
};