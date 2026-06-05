<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            if (! Schema::hasColumn('programs', 'program_type')) {
                $table->string('program_type', 40)->default('financial_assistance')->after('status');
            }
            if (! Schema::hasColumn('programs', 'sponsor_name')) {
                $table->string('sponsor_name')->nullable()->after('program_type');
            }
            if (! Schema::hasColumn('programs', 'sponsor_logo')) {
                $table->string('sponsor_logo')->nullable()->after('sponsor_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            if (Schema::hasColumn('programs', 'sponsor_logo')) {
                $table->dropColumn('sponsor_logo');
            }
            if (Schema::hasColumn('programs', 'sponsor_name')) {
                $table->dropColumn('sponsor_name');
            }
            if (Schema::hasColumn('programs', 'program_type')) {
                $table->dropColumn('program_type');
            }
        });
    }
};
