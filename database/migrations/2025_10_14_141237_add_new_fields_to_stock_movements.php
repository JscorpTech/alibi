<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            // Новые поля для детального учёта
            $table->foreignId('variant_id')->nullable()->after('product_id')
                ->constrained('variants')->nullOnDelete();
            
            $table->string('type')->nullable()->after('reason');
            // type: 'sale_pos', 'sale_app', 'return_pos', 'return_app', 'cancel_pos', 'cancel_app', 
            // 'reserve_app', 'release_reserve', 'purchase', 'adjustment', 'damage', 'inventory'
            
            $table->integer('quantity')->default(0)->after('type');
            $table->integer('quantity_before')->default(0)->after('quantity');
            $table->integer('quantity_after')->default(0)->after('quantity_before');
            
            $table->foreignId('order_group_id')->nullable()->after('order_id')
                ->constrained('order_groups')->nullOnDelete();
            
            $table->foreignId('user_id')->nullable()->after('order_group_id')
                ->constrained('users')->nullOnDelete();
            
            $table->text('note')->nullable()->after('meta');
            
            // Индексы для быстрого поиска
            $table->index(['variant_id', 'type', 'created_at']);
            $table->index('order_group_id');
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['variant_id']);
            $table->dropForeign(['order_group_id']);
            $table->dropForeign(['user_id']);
            
            $table->dropIndex(['variant_id', 'type', 'created_at']);
            $table->dropIndex(['order_group_id']);
            
            $table->dropColumn([
                'variant_id',
                'type',
                'quantity',
                'quantity_before',
                'quantity_after',
                'order_group_id',
                'user_id',
                'note',
            ]);
        });
    }
};