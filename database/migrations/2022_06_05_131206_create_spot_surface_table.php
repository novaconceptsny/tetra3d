<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('spot_surface', function (Blueprint $table) {
            $table->foreignId('spot_id');
            $table->foreignId('surface_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('spot_surface');
    }
};
