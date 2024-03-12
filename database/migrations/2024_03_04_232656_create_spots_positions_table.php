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
        Schema::create('spots_positions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('spot_id');
            $table->unsignedInteger('tour_id');
            $table->double('x');
            $table->double('y');
            $table->double('z');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spots_positions');
    }
};
