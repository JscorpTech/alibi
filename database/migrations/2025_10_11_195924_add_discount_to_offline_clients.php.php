<?php

// database/migrations/2025_10_11_200100_add_discount_to_offline_clients.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('offline_clients', function (Blueprint $table) {
            $table->unsignedInteger('discount_percent')->nullable()->after('phone'); // 0..100
            $table->string('discount_note')->nullable()->after('discount_percent');
        });
    }
    public function down(): void
    {
        Schema::table('offline_clients', function (Blueprint $table) {
            $table->dropColumn(['discount_percent', 'discount_note']);
        });
    }
};