<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_registrations', function (Blueprint $table) {
            if (! Schema::hasColumn('program_registrations', 'breast_cancer_stage')) {
                $table->string('breast_cancer_stage', 32)->nullable()->after('medical_condition');
            }
            if (! Schema::hasColumn('program_registrations', 'ethnicity')) {
                $table->string('ethnicity', 160)->nullable()->after('breast_cancer_stage');
            }
            if (! Schema::hasColumn('program_registrations', 'patient_bill_line_items')) {
                $table->json('patient_bill_line_items')->nullable()->after('billing_details');
            }
            if (! Schema::hasColumn('program_registrations', 'story_media_paths')) {
                $table->json('story_media_paths')->nullable()->after('story');
            }
            if (! Schema::hasColumn('program_registrations', 'story_notes')) {
                $table->text('story_notes')->nullable()->after('story_media_paths');
            }
        });
    }

    public function down(): void
    {
        Schema::table('program_registrations', function (Blueprint $table) {
            foreach (['breast_cancer_stage', 'ethnicity', 'patient_bill_line_items', 'story_media_paths', 'story_notes'] as $col) {
                if (Schema::hasColumn('program_registrations', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
