<?php

namespace App\Support;

use App\Mail\ProgramRegistrationAdminNotice;
use App\Models\ProgramRegistration;
use App\Models\User;
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
                Mail::to($email)->send(new ProgramRegistrationAdminNotice($subject, $registration, $bodyLine));
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }
}
