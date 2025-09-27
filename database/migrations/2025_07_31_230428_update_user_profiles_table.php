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
        Schema::table('user_profiles', function (Blueprint $table) {
            // Additional personal info fields
            $table->string('first_name')->nullable()->after('id');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('username')->unique()->nullable()->after('last_name');
            $table->string('alternate_email')->nullable()->after('username');

            // Location breakdown
            $table->string('country')->nullable()->after('location');
            $table->string('city')->nullable()->after('country');
            $table->string('state')->nullable()->after('city');

            // Social media links
            $table->string('facebook')->nullable()->after('state');
            $table->string('twitter')->nullable()->after('facebook');
            $table->string('instagram')->nullable()->after('twitter');

            // Notification preferences
            $table->boolean('email_notification')->default(true)->after('instagram');
            $table->boolean('sms_notification')->default(true)->after('email_notification');
            $table->boolean('notify_on_new_notifications')->default(true)->after('sms_notification');
            $table->boolean('notify_on_direct_message')->default(true)->after('notify_on_new_notifications');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'username',
                'alternate_email',
                'country',
                'city',
                'state',
                'facebook',
                'twitter',
                'instagram',
                'email_notification',
                'sms_notification',
                'notify_on_new_notifications',
                'notify_on_direct_message',
            ]);
        });
    }
};
