<?php

use App\Enums\OrderStatusEnum;
use App\Enums\PaymentTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('product_id')->constrained('products');

            $table->enum('status', OrderStatusEnum::toArray())->default(OrderStatusEnum::PENDING);
            $table->unsignedBigInteger('price')->nullable();
            $table->enum('payment_type', PaymentTypeEnum::toArray())->default(PaymentTypeEnum::CASH);

            $table->integer('count')->default(1);
            $table->foreignId('address_id')->constrained('addresses');

            $table->foreignId('color_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('size_id')->nullable()->constrained()->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
