<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order_groups', function (Blueprint $table) {
            $table->foreignId('offline_client_id')
                ->nullable()
                ->after('user_id')
                ->constrained('offline_clients')
                ->nullOnDelete();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('offline_client_id')
                ->nullable()
                ->after('user_id')
                ->constrained('offline_clients')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('offline_client_id');
        });

        Schema::table('order_groups', function (Blueprint $table) {
            $table->dropConstrainedForeignId('offline_client_id');
        });
    }
};