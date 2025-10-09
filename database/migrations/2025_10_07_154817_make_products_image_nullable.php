<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Разрешаем NULL в колонке image
        DB::statement('ALTER TABLE products ALTER COLUMN image DROP NOT NULL');
    }

    public function down(): void
    {
        // Если нужно откатить обратно (будьте осторожны, если уже есть NULL)
        DB::statement('ALTER TABLE products ALTER COLUMN image SET NOT NULL');
    }
};