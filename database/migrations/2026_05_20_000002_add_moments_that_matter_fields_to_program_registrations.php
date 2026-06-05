<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_registrations', function (Blueprint $table) {
            if (! Schema::hasColumn('program_registrations', 'apartment_suite')) {
                $table->string('apartment_suite', 120)->nullable()->after('postal_code');
            }
            if (! Schema::hasColumn('program_registrations', 'shipping_usa')) {
                $table->boolean('shipping_usa')->nullable()->after('apartment_suite');
            }
            if (! Schema::hasColumn('program_registrations', 'mtm_package')) {
                $table->string('mtm_package', 60)->nullable()->after('shipping_usa');
            }
            if (! Schema::hasColumn('program_registrations', 'applying_for')) {
                $table->string('applying_for', 40)->nullable()->after('mtm_package');
            }
            if (! Schema::hasColumn('program_registrations', 'patient_loved_one_name')) {
                $table->string('patient_loved_one_name', 255)->nullable()->after('applying_for');
            }
            if (! Schema::hasColumn('program_registrations', 'mtm_treatment_status')) {
                $table->string('mtm_treatment_status', 60)->nullable()->after('patient_loved_one_name');
            }
            if (! Schema::hasColumn('program_registrations', 'mtm_treatment_status_other')) {
                $table->string('mtm_treatment_status_other', 255)->nullable()->after('mtm_treatment_status');
            }
            if (! Schema::hasColumn('program_registrations', 'mtm_diagnosis_type')) {
                $table->string('mtm_diagnosis_type', 255)->nullable()->after('mtm_treatment_status_other');
            }
            if (! Schema::hasColumn('program_registrations', 'mtm_diagnosis_date')) {
                $table->date('mtm_diagnosis_date')->nullable()->after('mtm_diagnosis_type');
            }
            if (! Schema::hasColumn('program_registrations', 'mtm_story_permission')) {
                $table->string('mtm_story_permission', 40)->nullable()->after('mtm_diagnosis_date');
            }
            if (! Schema::hasColumn('program_registrations', 'mtm_acknowledgments')) {
                $table->json('mtm_acknowledgments')->nullable()->after('mtm_story_permission');
            }
            if (! Schema::hasColumn('program_registrations', 'signature_date')) {
                $table->date('signature_date')->nullable()->after('signature');
            }
        });
    }

    public function down(): void
    {
        Schema::table('program_registrations', function (Blueprint $table) {
            $columns = [
                'signature_date',
                'mtm_acknowledgments',
                'mtm_story_permission',
                'mtm_diagnosis_date',
                'mtm_diagnosis_type',
                'mtm_treatment_status_other',
                'mtm_treatment_status',
                'patient_loved_one_name',
                'applying_for',
                'mtm_package',
                'shipping_usa',
                'apartment_suite',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('program_registrations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
