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
            $table->date('dob')->nullable()->after('blood_group');
            $table->string('email')->nullable()->after('dob');
            $table->text('medical_condition')->nullable()->after('email');
            $table->string('assistance_type')->nullable()->after('medical_condition');
            $table->text('justification')->nullable()->after('assistance_type');
            $table->json('document_paths')->nullable()->after('justification');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_registrations', function (Blueprint $table) {
            //
        });
    }
};
