<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->foreignId('layout_id')->nullable()->after('project_id');
        });

        Schema::table('surface_states', function (Blueprint $table) {
            $table->foreignId('layout_id')->nullable()->after('project_id');
        });
    }

    public function down(): void
    {
        Schema::table('', function (Blueprint $table) {
            //
        });
    }
};
