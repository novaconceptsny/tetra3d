<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('shared_tours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable();
            $table->foreignId('layout_id');
            $table->foreignId('project_id'); //todo:: remove column
            $table->foreignId('user_id');
            $table->foreignId('spot_id')->nullable(); //todo:: remove column
            $table->json('surface_states')->nullable(); //todo:: remove column
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shared_tours');
    }
};
