<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            if (! Schema::hasColumn('programs', 'application_form_schema')) {
                $table->json('application_form_schema')->nullable()->after('custom_fields');
            }
        });

        Schema::table('program_registrations', function (Blueprint $table) {
            if (! Schema::hasColumn('program_registrations', 'application_responses')) {
                $table->json('application_responses')->nullable()->after('document_paths');
            }
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            if (Schema::hasColumn('programs', 'application_form_schema')) {
                $table->dropColumn('application_form_schema');
            }
        });

        Schema::table('program_registrations', function (Blueprint $table) {
            if (Schema::hasColumn('program_registrations', 'application_responses')) {
                $table->dropColumn('application_responses');
            }
        });
    }
};
