<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('sponsorships') && Schema::hasColumn('sponsorships', 'program_id')) {
            Schema::table('sponsorships', function (Blueprint $table) {
                $table->dropForeign(['program_id']);
            });

            DB::statement('ALTER TABLE sponsorships MODIFY program_id bigint unsigned NULL');

            Schema::table('sponsorships', function (Blueprint $table) {
                $table->foreign('program_id')
                    ->references('id')
                    ->on('programs')
                    ->cascadeOnDelete();
            });
        }

        Schema::table('sponsorships', function (Blueprint $table) {
            if (!Schema::hasColumn('sponsorships', 'sponsorship_program_id')) {
                $table->foreignId('sponsorship_program_id')
                    ->nullable()
                    ->after('program_id')
                    ->constrained('sponsorship_programs')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sponsorships', function (Blueprint $table) {
            if (Schema::hasColumn('sponsorships', 'sponsorship_program_id')) {
                $table->dropForeign(['sponsorship_program_id']);
                $table->dropColumn('sponsorship_program_id');
            }
        });

        if (Schema::hasTable('sponsorships') && Schema::hasColumn('sponsorships', 'program_id')) {
            Schema::table('sponsorships', function (Blueprint $table) {
                $table->dropForeign(['program_id']);
            });

            DB::statement('ALTER TABLE sponsorships MODIFY program_id bigint unsigned NOT NULL');

            Schema::table('sponsorships', function (Blueprint $table) {
                $table->foreign('program_id')
                    ->references('id')
                    ->on('programs')
                    ->cascadeOnDelete();
            });
        }
    }
};

