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
        Schema::create('artwork_models', function (Blueprint $table) {
            $table->id(); // Creates an auto-incrementing primary key (id)
            $table->integer('layout_id');
            $table->integer('artwork_id');
            $table->float('position_x');
            $table->float('position_y');
            $table->float('position_z');
            $table->float('rotation_x');
            $table->float('rotation_y');
            $table->float('rotation_z');
            $table->timestamps(); // Creates created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artwork_models');
    }
};
