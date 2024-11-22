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
        // Check if the 'display_name' column exists
        if (!Schema::hasColumn('spots', 'display_name')) {
            // Add 'display_name' column if it doesn't exist
            Schema::table('spots', function (Blueprint $table) {
                $table->string('display_name')->nullable()->after('name'); // Add after 'name' column
            });
        }

        // Set display_name to the same value as 'name', whether the column was newly added or already existed
        DB::table('spots')->update(['display_name' => DB::raw('name')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spots', function (Blueprint $table) {
            //
        });
    }
};
