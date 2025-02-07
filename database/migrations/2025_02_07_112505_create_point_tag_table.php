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
        Schema::create('point_tag', function (Blueprint $table) {
            $table->foreignUuid('point_uuid')->constrained('points', 'uuid');
            $table->foreignUuid('tag_uuid')->constrained('tags', 'uuid');

            $table->unique(['point_uuid', 'tag_uuid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_tag');
    }
};
