<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.artwork_photo_state
     */
    public function up(): void
    {
        Schema::create('artwork_photo_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artwork_id')->constrained()->onDelete('cascade');
            $table->foreignId('photo_state_id')->constrained()->onDelete('cascade');
            $table->foreignId('curate2d_surface_id')->constrained()->onDelete('cascade');
            $table->foreignId('layout_id')->constrained()->onDelete('cascade');
            $table->json('pos');
            $table->float('scale');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artwork_photo_states');
    }
};
