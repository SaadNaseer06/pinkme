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
        Schema::table('sponsorships', function (Blueprint $table) {
            if (Schema::hasColumn('sponsorships', 'sponsorship_program_id')) {
                $table->dropForeign(['sponsorship_program_id']);
            }
        });

        if (Schema::hasColumn('sponsorships', 'sponsorship_program_id')) {
            Schema::table('sponsorships', function (Blueprint $table) {
                $table->dropColumn('sponsorship_program_id');
            });
        }

        if (!Schema::hasColumn('sponsorships', 'program_id')) {
            Schema::table('sponsorships', function (Blueprint $table) {
                $table->foreignId('program_id')->after('sponsor_id');
            });
        }

        Schema::table('sponsorships', function (Blueprint $table) {
            $table->foreign('program_id')
                ->references('id')
                ->on('programs')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('sponsorships', 'program_id')) {
            Schema::table('sponsorships', function (Blueprint $table) {
                $table->dropForeign(['program_id']);
                $table->dropColumn('program_id');
            });
        }

        Schema::table('sponsorships', function (Blueprint $table) {
            if (!Schema::hasColumn('sponsorships', 'sponsorship_program_id')) {
                $table->foreignId('sponsorship_program_id')
                    ->after('sponsor_id')
                    ->constrained('sponsorship_programs')
                    ->cascadeOnDelete();
            }
        });
    }
};
