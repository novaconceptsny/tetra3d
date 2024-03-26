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
        Schema::create('sculptures', function (Blueprint $table) {
            $table->id();
            $table->integer('layout_id');
            $table->integer('sculpture_id');
            $table->integer('model_id');
            $table->float('position_x');
            $table->float('position_y');
            $table->float('position_z');
            $table->float('rotation_x');
            $table->float('rotation_y');
            $table->float('rotation_z');
            $table->float('scale');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sculptures');
    }
};
