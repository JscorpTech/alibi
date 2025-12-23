<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Для PostgreSQL (у тебя он, судя по ошибке)
        DB::statement('ALTER TABLE videos ALTER COLUMN status TYPE VARCHAR(255)');
    }
    
    public function down()
    {
        // Если нужно откатить обратно
        DB::statement('ALTER TABLE videos ALTER COLUMN status TYPE BOOLEAN USING (status::boolean)');
    }
};
