<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_registration_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number')->unique();
            $table->date('issue_date');
            $table->string('payment_purpose');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method');
            $table->enum('status', ['Pending', 'Paid', 'Cancelled'])->default('Paid');
            $table->string('file_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_invoices');
    }
};
