<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('surfaces', function (Blueprint $table) {
            $table->string('display_name')->after('name')->nullable();
        });

        // Copy existing name values to display_name
        DB::table('surfaces')->update([
            'display_name' => DB::raw('name')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surfaces', function (Blueprint $table) {
            $table->dropColumn('display_name');
        });
    }
};
