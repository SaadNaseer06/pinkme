<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_registrations', function (Blueprint $table) {
            if (!Schema::hasColumn('program_registrations', 'payment_links')) {
                $table->text('payment_links')->nullable()->after('billing_details');
            }
        });
    }

    public function down(): void
    {
        Schema::table('program_registrations', function (Blueprint $table) {
            if (Schema::hasColumn('program_registrations', 'payment_links')) {
                $table->dropColumn('payment_links');
            }
        });
    }
};
