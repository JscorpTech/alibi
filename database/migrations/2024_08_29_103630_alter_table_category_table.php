<?php

use App\Enums\SortbyEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table("categories",function(Blueprint $table){
            $table->enum("sortby",SortbyEnum::toArray())->default(SortbyEnum::CREATED_AT);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("categories", function(Blueprint $table){
            $table->dropColumn("sortby");
        });
    }
};
