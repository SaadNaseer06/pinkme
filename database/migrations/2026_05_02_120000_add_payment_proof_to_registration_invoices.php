<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registration_invoices', function (Blueprint $table) {
            $table->string('payment_proof_path')->nullable()->after('file_path');
            $table->string('payment_proof_original_name')->nullable()->after('payment_proof_path');
            $table->timestamp('payment_proof_uploaded_at')->nullable()->after('payment_proof_original_name');
        });
    }

    public function down(): void
    {
        Schema::table('registration_invoices', function (Blueprint $table) {
            $table->dropColumn(['payment_proof_path', 'payment_proof_original_name', 'payment_proof_uploaded_at']);
        });
    }
};
