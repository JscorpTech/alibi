<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('variants', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('product_id')
                ->constrained()               // references products(id)
                ->cascadeOnDelete();

            $table->string('sku')->nullable();
            $table->string('barcode', 64)->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedBigInteger('price')->default(0);
            $table->jsonb('attrs')->nullable();          // {"Size":"M","Color":"Black"}
            $table->string('image')->nullable();         // фото варианта (обычно для Color)
            $table->boolean('available')->default(true);
            $table->timestamps();

            // Индексы/уникальности
            $table->unique(['barcode']);                 // в Postgres несколько NULL допустимы — ок
            $table->unique(['product_id', 'sku'], 'variants_product_sku_unique');
            $table->index('product_id');
        });

        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            // Универсальный GIN индекс по jsonb (без jsonb_path_ops для совместимости)
            DB::statement('CREATE INDEX variants_attrs_gin ON variants USING gin (attrs)');

            // (Опционально) уникальность attrs в пределах продукта через выражение
            DB::statement("
                CREATE UNIQUE INDEX variants_product_attrs_unique
                ON variants (product_id, md5(attrs::text))
            ");
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS variants_product_attrs_unique');
            DB::statement('DROP INDEX IF EXISTS variants_attrs_gin');
        }
        Schema::dropIfExists('variants');
    }
};