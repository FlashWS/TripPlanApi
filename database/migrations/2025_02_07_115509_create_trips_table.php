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
        Schema::create('trips', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignId('user_id')->constrained();
            $table->string('name')->comment('Название путешествия')->index();
            $table->timestamp('date_start')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('Дата старта');
            $table->timestamp('date_end')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('Дата окончания');
            $table->unsignedBigInteger('days')->virtualAs("DATEDIFF(DATE(date_end), DATE(date_start)) + 1");
            $table->text('note')->nullable()->comment('Примечание');
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
        Schema::dropIfExists('trips');
    }
};
