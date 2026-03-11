<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->foreignId('finance_user_id')->nullable()->after('reviewer_id')->constrained('users')->nullOnDelete();
            $table->timestamp('sent_to_finance_at')->nullable()->after('finance_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropForeign(['finance_user_id']);
            $table->dropColumn(['finance_user_id', 'sent_to_finance_at']);
        });
    }
};
