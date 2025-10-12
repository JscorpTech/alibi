<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('offline_clients', 'discount_percent')) {
            Schema::table('offline_clients', function (Blueprint $table) {
                $table->smallInteger('discount_percent')->nullable();
            });
        }

        // если добавлял ещё какую-то колонку в этой же миграции — аналогично:
        // if (!Schema::hasColumn('offline_clients', 'discount_note')) {
        //     Schema::table('offline_clients', function (Blueprint $table) {
        //         $table->string('discount_note')->nullable();
        //     });
        // }
    }

    public function down(): void
    {
        if (Schema::hasColumn('offline_clients', 'discount_percent')) {
            Schema::table('offline_clients', function (Blueprint $table) {
                $table->dropColumn('discount_percent');
            });
        }

        // if (Schema::hasColumn('offline_clients', 'discount_note')) {
        //     Schema::table('offline_clients', function (Blueprint $table) {
        //         $table->dropColumn('discount_note');
        //     });
        // }
    }
};