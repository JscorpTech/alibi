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
        Schema::table('order_groups', function (Blueprint $t) {
            // тип чека: обычная продажа (sale), возврат (return) или обмен (exchange)
            if (!Schema::hasColumn('order_groups', 'type')) {
                $t->string('type', 20)->default('sale')->index(); // sale|return|exchange
            }
            // ссылка на исходный чек при возврате/обмене (необязательно)
            if (!Schema::hasColumn('order_groups', 'original_group_id')) {
                $t->unsignedBigInteger('original_group_id')->nullable()->index();
            }
        });

        Schema::table('orders', function (Blueprint $t) {
            // привязка к строке исходного заказа (для частичных возвратов)
            if (!Schema::hasColumn('orders', 'original_order_id')) {
                $t->unsignedBigInteger('original_order_id')->nullable()->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_groups', function (Blueprint $t) {
            if (Schema::hasColumn('order_groups', 'type'))
                $t->dropColumn('type');
            if (Schema::hasColumn('order_groups', 'original_group_id'))
                $t->dropColumn('original_group_id');
        });
        Schema::table('orders', function (Blueprint $t) {
            if (Schema::hasColumn('orders', 'original_order_id'))
                $t->dropColumn('original_order_id');
        });
    }
};
