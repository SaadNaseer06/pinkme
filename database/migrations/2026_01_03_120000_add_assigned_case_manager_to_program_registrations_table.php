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
            $table->foreignId('assigned_case_manager_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->after('reviewed_by');
            $table->timestamp('assigned_at')->nullable()->after('assigned_case_manager_id');
            $table->index('assigned_case_manager_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_registrations', function (Blueprint $table) {
            $table->dropIndex(['assigned_case_manager_id']);
            $table->dropForeign(['assigned_case_manager_id']);
            $table->dropColumn(['assigned_case_manager_id', 'assigned_at']);
        });
    }
};
