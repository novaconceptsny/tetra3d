<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Collector Systems API 2.0 identifies an account by a GUID
     * (app.collectorsystems.com/Items/Index/{AccountGUID}?subscriptionId=...).
     * collector_subscription_id is kept for reference.
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('collector_account_guid', 36)->nullable()->after('collector_subscription_id');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('collector_account_guid');
        });
    }
};
