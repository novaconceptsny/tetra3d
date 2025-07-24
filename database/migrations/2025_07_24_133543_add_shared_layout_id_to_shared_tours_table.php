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
        Schema::table('shared_tours', callback: function (Blueprint $table) {
            $table->foreignId('shared_layout_id')->nullable()->after('layout_id')->constrained('shared_layouts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shared_tours', function (Blueprint $table) {
            $table->dropForeign(['shared_layout_id']);
            $table->dropColumn('shared_layout_id');
        });
    }
};
