<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_registrations', function (Blueprint $table) {
            $table->text('internal_note_for_finance')->nullable()->after('review_note');
            $table->text('internal_note_for_admin')->nullable()->after('internal_note_for_finance');
        });
    }

    public function down(): void
    {
        Schema::table('program_registrations', function (Blueprint $table) {
            $table->dropColumn(['internal_note_for_finance', 'internal_note_for_admin']);
        });
    }
};
