<?php

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
        Schema::create('points', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignId('user_id')->constrained();
            $table->string('name')->comment('Название точки');
            $table->string('address')->nullable()->comment('Адрес');
            $table->geography('location', 'Point', 4326)->comment('Локация')->index();
            $table->timestamps();

            $table->unique(['user_id', 'name']);
        });

        \Illuminate\Support\Facades\DB::statement('ALTER TABLE points ADD SPATIAL INDEX(location);');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('points');
    }
};
