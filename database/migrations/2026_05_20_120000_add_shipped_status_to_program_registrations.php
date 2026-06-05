<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_registrations', function (Blueprint $table) {
            $table->timestamp('shipped_at')->nullable()->after('reviewed_at');
            $table->foreignId('shipped_by')->nullable()->after('shipped_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('program_registrations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shipped_by');
            $table->dropColumn('shipped_at');
        });
    }
};
