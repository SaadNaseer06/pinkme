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
        Schema::table('programs', function (Blueprint $table) {
            if (!Schema::hasColumn('programs', 'application_start_date')) {
                $table->date('application_start_date')->nullable()->after('event_time');
            }
            if (!Schema::hasColumn('programs', 'application_end_date')) {
                $table->date('application_end_date')->nullable()->after('application_start_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            if (Schema::hasColumn('programs', 'application_end_date')) {
                $table->dropColumn('application_end_date');
            }
            if (Schema::hasColumn('programs', 'application_start_date')) {
                $table->dropColumn('application_start_date');
            }
        });
    }
};
