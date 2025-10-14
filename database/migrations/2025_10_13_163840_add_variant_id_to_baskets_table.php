<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('baskets', function (Blueprint $table) {
            if (!Schema::hasColumn('baskets', 'variant_id')) {
                $table->foreignId('variant_id')
                    ->nullable()
                    ->after('product_id')
                    ->constrained('variants')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('baskets', function (Blueprint $table) {
            if (Schema::hasColumn('baskets', 'variant_id')) {
                $table->dropConstrainedForeignId('variant_id');
            }
        });
    }
};