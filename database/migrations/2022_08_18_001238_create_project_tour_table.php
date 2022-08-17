<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('project_tour', function (Blueprint $table) {
            $table->foreignId('project_id');
            $table->foreignId('tour_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_tour');
    }
};
