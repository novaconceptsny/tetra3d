<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('artwork_surface_state', function (Blueprint $table) {
            $table->foreignId('artwork_id');
            $table->foreignId('surface_state_id');
            $table->string('top_position')->nullable();
            $table->string('left_position')->nullable();
            $table->text('crop_data')->nullable();
            $table->boolean('override_scale')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('artwork_surface_state');
    }
};
