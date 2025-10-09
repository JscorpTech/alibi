<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('order_groups', 'type')) {
            Schema::table('order_groups', function (Blueprint $table) {
                $table->string('type')->default('sale');
            });
        }

        if (!Schema::hasColumn('order_groups', 'original_group_id')) {
            Schema::table('order_groups', function (Blueprint $table) {
                $table->unsignedBigInteger('original_group_id')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        // безопасный откат
        if (Schema::hasColumn('order_groups', 'type')) {
            Schema::table('order_groups', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }

        if (Schema::hasColumn('order_groups', 'original_group_id')) {
            Schema::table('order_groups', function (Blueprint $table) {
                $table->dropColumn('original_group_id');
            });
        }
    }
};