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
        Schema::create('surface_infos', function (Blueprint $table) {
            $table->id();
            $table->string('surface_id');
            $table->json('normalvector'); // JSON for complex data like vectors
            $table->json('start_pos');    // JSON for position data
            $table->float('width');
            $table->float('height');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surface_infos');
    }
};
