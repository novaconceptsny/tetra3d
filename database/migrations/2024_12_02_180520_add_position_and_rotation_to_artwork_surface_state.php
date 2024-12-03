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
        Schema::table('artwork_surface_state', function (Blueprint $table) {
            $table->float('position_x')->nullable();
            $table->float('position_y')->nullable();
            $table->float('position_z')->nullable();
            $table->float('rotation_x')->nullable();
            $table->float('rotation_y')->nullable();
            $table->float('rotation_z')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artwork_surface_state', function (Blueprint $table) {
            $table->dropColumn(['position_x', 'position_y', 'position_z', 'rotation_x', 'rotation_y', 'rotation_z']);
        });
    }
};
