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
        Schema::create('trip_point', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignUuid('trip_uuid')->constrained('trips', 'uuid');
            $table->foreignUuid('point_uuid')->constrained('points', 'uuid');
            $table->unsignedBigInteger('day')->index();
            $table->time('time')->nullable()->index();
            $table->unsignedBigInteger('order')->default(0)->index();
            $table->text('note')->nullable()->comment('Примечание');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_points');
    }
};
