<?php

namespace App\Services;

use App\Models\ProgramRegistration;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Log;

class FinanceNotificationService
{
    /**
     * Notify a finance user that a registration has been assigned to them for budget allocation.
     */
    public static function notifyRegistrationAssigned(User $financeUser, ProgramRegistration $registration): bool
    {
        try {
            UserNotification::create([
                'user_id' => $financeUser->id,
                'title' => 'New Lead Assigned',
                'message' => 'A registration has been sent to you for budget allocation. Applicant: ' . ($registration->full_name ?? 'N/A') . ', Program: ' . ($registration->program?->title ?? 'N/A'),
                'priority' => UserNotification::PRIORITY_IMPORTANT,
                'link_url' => route('finance.registrations.show', $registration),
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::warning('Finance notification failed', [
                'finance_user_id' => $financeUser->id,
                'registration_id' => $registration->id,
                'error' => $e->getMessage(),
            ]);
            report($e);

            return false;
        }
    }

    /**
     * Notify a finance user that an application has been assigned to them for budget allocation.
     */
    public static function notifyApplicationAssigned(User $financeUser, $application): bool
    {
        try {
            $applicantName = optional($application->patient)->user?->profile?->full_name
                ?? optional($application->patient)->user?->email
                ?? 'N/A';
            $programTitle = $application->program?->title ?? 'N/A';

            UserNotification::create([
                'user_id' => $financeUser->id,
                'title' => 'New Application Assigned',
                'message' => 'An application has been sent to you for budget allocation. Applicant: ' . $applicantName . ', Program: ' . $programTitle,
                'priority' => UserNotification::PRIORITY_IMPORTANT,
                'link_url' => route('admin.viewApplication', $application->id),
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::warning('Finance notification failed', [
                'finance_user_id' => $financeUser->id,
                'application_id' => $application->id ?? null,
                'error' => $e->getMessage(),
            ]);
            report($e);

            return false;
        }
    }
}
