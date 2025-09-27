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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('program_id')->nullable()->constrained('sponsorship_programs')->nullOnDelete();
            $table->string('title');
            $table->string('age')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('assistance_type')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Under Review'])->default('Pending');
            $table->timestamp('submission_date');
            $table->timestamp('decision_date')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
