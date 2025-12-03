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
        Schema::table('tutorial_videos', function (Blueprint $table) {
            if (Schema::hasColumn('tutorial_videos', 'thumbnail_path')) {
                $table->dropColumn('thumbnail_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tutorial_videos', function (Blueprint $table) {
            if (!Schema::hasColumn('tutorial_videos', 'thumbnail_path')) {
                $table->string('thumbnail_path')->nullable();
            }
        });
    }
};

