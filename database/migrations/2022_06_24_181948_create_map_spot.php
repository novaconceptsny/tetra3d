<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('map_spot', function (Blueprint $table) {
            $table->foreignId('map_id');
            $table->foreignId('spot_id');
            $table->string('x')->nullable();
            $table->string('y')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('map_spot');
    }
};
