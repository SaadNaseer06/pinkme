<?php

namespace App\Support;

use App\Mail\ProgramRegistrationAdminNotice;
use App\Models\ProgramRegistration;
use App\Models\Role;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Mail;

class ProgramRegistrationNotifiers
{
    public static function notifyAdmins(string $subject, string $bodyLine, ProgramRegistration $registration): void
    {
        $emails = User::query()
            ->whereHas('role', fn ($q) => $q->where('name', 'admin'))
            ->whereNotNull('email')
            ->pluck('email')
            ->filter()
            ->unique();

        foreach ($emails as $email) {
            try {
                Mail::to($email)->queue(new ProgramRegistrationAdminNotice($subject, $registration, $bodyLine));
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

    /**
     * In-app notifications for all active case managers when a new application is submitted.
     */
    public static function notifyCaseManagersInbox(string $title, string $message, ProgramRegistration $registration): void
    {
        $roleId = Role::where('name', 'casemanager')->value('id');
        if (! $roleId) {
            return;
        }

        $userIds = User::query()
            ->where('role_id', $roleId)
            ->pluck('id');

        $now = now();
        $linkUrl = route('case_manager.program_registrations.show', $registration);
        $rows = $userIds->map(fn ($userId): array => [
            'user_id' => (int) $userId,
            'title' => $title,
            'message' => $message,
            'priority' => UserNotification::PRIORITY_IMPORTANT,
            'link_url' => $linkUrl,
            'created_at' => $now,
            'updated_at' => $now,
        ])->all();

        foreach (array_chunk($rows, 300) as $chunk) {
            try {
                UserNotification::query()->insert($chunk);
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }
}
