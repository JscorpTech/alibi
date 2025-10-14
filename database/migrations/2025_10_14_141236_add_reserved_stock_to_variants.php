<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('variants', function (Blueprint $table) {
            // Резервирование товара для APP
            $table->integer('reserved_stock')->default(0)->after('stock');
            
            // Индекс для быстрого поиска доступных товаров
            $table->index(['stock', 'reserved_stock']);
        });
    }

    public function down(): void
    {
        Schema::table('variants', function (Blueprint $table) {
            $table->dropIndex(['stock', 'reserved_stock']);
            $table->dropColumn('reserved_stock');
        });
    }
};