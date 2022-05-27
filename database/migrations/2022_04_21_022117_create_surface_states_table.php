<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('surface_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surface_id');
            $table->string('name');
            $table->json('canvas_data')->nullable();
            $table->string('hotspot_url')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->boolean('active')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('surface_states');
    }
};
