<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1️⃣ Дефолтное значение = 0
        DB::statement('ALTER TABLE products ALTER COLUMN cost_price SET DEFAULT 0');

        // 2️⃣ Заменяем все NULL на 0
        DB::statement('UPDATE products SET cost_price = 0 WHERE cost_price IS NULL');

        // 3️⃣ Разрешаем NULL (если не хочешь — можно пропустить)
        DB::statement('ALTER TABLE products ALTER COLUMN cost_price DROP NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE products ALTER COLUMN cost_price SET NOT NULL');
        DB::statement('ALTER TABLE products ALTER COLUMN cost_price DROP DEFAULT');
    }
};