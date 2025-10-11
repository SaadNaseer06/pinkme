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
        // Enhance events table
        Schema::table('events', function (Blueprint $table) {
            $table->decimal('funding_goal', 12, 2)->nullable()->after('location');
            $table->string('status')->default('upcoming')->after('funding_goal'); // upcoming, ongoing, completed, cancelled
            $table->text('event_highlights')->nullable()->after('status');
            $table->string('image')->nullable()->after('event_highlights');
            $table->integer('max_sponsors')->nullable()->after('image');
            $table->dateTime('registration_deadline')->nullable()->after('max_sponsors');
        });

        // Enhance event_sponsorships table to track registration
        Schema::table('event_sponsorships', function (Blueprint $table) {
            $table->string('registration_status')->default('pending')->after('amount'); // pending, confirmed, cancelled
            $table->text('message')->nullable()->after('registration_status');
            $table->dateTime('registered_at')->nullable()->after('message');
            $table->dateTime('confirmed_at')->nullable()->after('registered_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'funding_goal',
                'status',
                'event_highlights',
                'image',
                'max_sponsors',
                'registration_deadline'
            ]);
        });

        Schema::table('event_sponsorships', function (Blueprint $table) {
            $table->dropColumn([
                'registration_status',
                'message',
                'registered_at',
                'confirmed_at'
            ]);
        });
    }
};
