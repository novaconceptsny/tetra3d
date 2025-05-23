<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('artworks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id');
            $table->foreignId('artwork_collection_id')->nullable();
            $table->unsignedBigInteger('collector_object_id')->nullable();
            $table->string('name');
            $table->string('artist')->nullable();
            $table->string('type')->nullable();
            $table->string('image_url')->nullable();
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('artworks');
    }
};
