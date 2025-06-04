<?php

use App\Enums\GenderEnum;
use App\Enums\ProductStatusEnum;
use App\Services\LocaleService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            LocaleService::getMigration($table, 'string', 'name');
            LocaleService::getMigration($table, 'text', 'desc');
            $table->enum('gender', GenderEnum::toArray());
            $table->unsignedBigInteger('price');
            $table->decimal('discount', 100, 1)->nullable();
            $table->string('image');
            $table->json('count')->default(json_encode(['sm' => 100]));
            $table->enum('status', ProductStatusEnum::toArray())->default(ProductStatusEnum::AVAILABLE);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
