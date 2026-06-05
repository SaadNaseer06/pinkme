<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_registrations', function (Blueprint $table) {
            if (! Schema::hasColumn('program_registrations', 'finance_pre_payment_proof_paths')) {
                $table->json('finance_pre_payment_proof_paths')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('program_registrations', function (Blueprint $table) {
            $table->dropColumn('finance_pre_payment_proof_paths');
        });
    }
};
