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
        Schema::table('sponsorship_programs', function (Blueprint $table) {
            if (Schema::hasColumn('sponsorship_programs', 'program_id')) {
                $table->dropForeign(['program_id']);
                $table->dropColumn('program_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sponsorship_programs', function (Blueprint $table) {
            if (!Schema::hasColumn('sponsorship_programs', 'program_id')) {
                $table->foreignId('program_id')
                    ->unique()
                    ->nullable()
                    ->after('id')
                    ->constrained('programs')
                    ->nullOnDelete();
            }
        });
    }
};
