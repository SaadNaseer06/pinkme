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
        Schema::table('site_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('site_settings', 'privacy_last_updated')) {
                $table->date('privacy_last_updated')->nullable()->after('privacy_policy_content');
            }

            if (!Schema::hasColumn('site_settings', 'terms_last_updated')) {
                $table->date('terms_last_updated')->nullable()->after('terms_conditions_content');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            if (Schema::hasColumn('site_settings', 'privacy_last_updated')) {
                $table->dropColumn('privacy_last_updated');
            }

            if (Schema::hasColumn('site_settings', 'terms_last_updated')) {
                $table->dropColumn('terms_last_updated');
            }
        });
    }
};
