<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasColumn('applications', 'program_id')) {
                $table->dropForeign(['program_id']);
                $table->foreign('program_id')
                    ->references('id')
                    ->on('programs')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasColumn('applications', 'program_id')) {
                $table->dropForeign(['program_id']);
                $table->foreign('program_id')
                    ->references('id')
                    ->on('sponsorship_programs')
                    ->nullOnDelete();
            }
        });
    }
};
