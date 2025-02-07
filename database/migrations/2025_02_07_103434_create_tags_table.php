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
        Schema::create('tags', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignId('user_id')->constrained();
            $table->string('name')->comment('Название точки')->index();
            $table->string('icon')->default('tag')->comment('Иконка');
            $table->string('color')->nullable()->comment('Цвет');
            $table->timestamps();

            $table->unique(['user_id', 'name']);
            $table->index(['created_at']);
            $table->index(['updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
