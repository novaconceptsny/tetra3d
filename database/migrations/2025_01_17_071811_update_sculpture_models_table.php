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
        Schema::table('sculpture_models', function (Blueprint $table) {
            $table->foreignId('company_id')->change();
            $table->foreignId('artwork_collection_id')->change();
            $table->unsignedBigInteger('collector_object_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sculpture_models', function (Blueprint $table) {
            $table->dropColumn('collector_object_id');
            $table->unsignedInteger('company_id')->change();
            $table->unsignedInteger('artwork_collection_id')->change();
        });
    }
};
