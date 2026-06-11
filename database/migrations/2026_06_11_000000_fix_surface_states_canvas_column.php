<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The original create_surface_states_table migration was edited in place
 * (June 2022): `canvas_data` was renamed to `canvas`. Databases migrated
 * before that edit (production, and local copies imported from it) still
 * have `canvas_data`, which crashes the current code on surface save.
 *
 * Guarded so it is a no-op on databases that are already correct.
 */
return new class extends Migration {
    public function up(): void
    {
        $hasCanvas = Schema::hasColumn('surface_states', 'canvas');
        $hasCanvasData = Schema::hasColumn('surface_states', 'canvas_data');

        if (! $hasCanvas && $hasCanvasData) {
            // Rename to preserve existing saved states.
            Schema::table('surface_states', function (Blueprint $table) {
                $table->renameColumn('canvas_data', 'canvas');
            });

            return;
        }

        if (! $hasCanvas) {
            Schema::table('surface_states', function (Blueprint $table) {
                $table->json('canvas')->nullable()->after('name');
            });
        }
    }

    public function down(): void
    {
        // Intentionally left empty: reversing could destroy saved states.
    }
};
