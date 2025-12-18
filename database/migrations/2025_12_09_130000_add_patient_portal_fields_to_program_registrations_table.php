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
        Schema::table('program_registrations', function (Blueprint $table) {
            $table->string('quarter_applied', 20)->nullable()->after('assistance_type');
            $table->json('programs_applied')->nullable()->after('quarter_applied');
            $table->boolean('active_treatment')->default(false)->after('programs_applied');
            $table->boolean('pregnant')->default(false)->after('active_treatment');
            $table->string('family_history')->nullable()->after('pregnant');
            $table->string('assistance_history')->nullable()->after('family_history');
            $table->string('heard_about')->nullable()->after('assistance_history');
            $table->string('referral_type', 30)->nullable()->after('heard_about');
            $table->string('treatment_facility_name')->nullable()->after('referral_type');
            $table->string('street_address')->nullable()->after('treatment_facility_name');
            $table->string('city')->nullable()->after('street_address');
            $table->string('state')->nullable()->after('city');
            $table->string('postal_code', 20)->nullable()->after('state');
            $table->json('proof_of_income_status')->nullable()->after('postal_code');
            $table->text('story')->nullable()->after('proof_of_income_status');
            $table->boolean('authorization_allow')->default(false)->after('story');
            $table->json('authorization_permissions')->nullable()->after('authorization_allow');
            $table->text('billing_details')->nullable()->after('authorization_permissions');
            $table->string('signature')->nullable()->after('billing_details');

            $table->string('treatment_letter_path')->nullable()->after('document_paths');
            $table->json('bill_statement_paths')->nullable()->after('treatment_letter_path');
            $table->json('income_document_paths')->nullable()->after('bill_statement_paths');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_registrations', function (Blueprint $table) {
            $table->dropColumn([
                'quarter_applied',
                'programs_applied',
                'active_treatment',
                'pregnant',
                'family_history',
                'assistance_history',
                'heard_about',
                'referral_type',
                'treatment_facility_name',
                'street_address',
                'city',
                'state',
                'postal_code',
                'proof_of_income_status',
                'story',
                'authorization_allow',
                'authorization_permissions',
                'billing_details',
                'signature',
                'treatment_letter_path',
                'bill_statement_paths',
                'income_document_paths',
            ]);
        });
    }
};
