<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('offline_clients', function (Blueprint $table) {
            if (!Schema::hasColumn('offline_clients', 'deleted_at')) {
                $table->softDeletes(); // добавит nullable timestamp deleted_at
            }
        });
    }

    public function down(): void
    {
        Schema::table('offline_clients', function (Blueprint $table) {
            if (Schema::hasColumn('offline_clients', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};