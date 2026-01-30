<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_sponsorships', function (Blueprint $table) {
            $table->string('stripe_checkout_session_id')->nullable()->after('confirmed_at');
            $table->string('payment_status')->default('pending')->after('stripe_checkout_session_id'); // pending, paid, failed, refunded
        });
    }

    public function down(): void
    {
        Schema::table('event_sponsorships', function (Blueprint $table) {
            $table->dropColumn(['stripe_checkout_session_id', 'payment_status']);
        });
    }
};
