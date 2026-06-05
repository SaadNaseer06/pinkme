<?php

namespace App\Services;

use App\Mail\PaymentReadyForFinance;
use App\Models\ProgramRegistration;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class FinanceNotificationService
{
    /**
     * Notify a finance user that a registration has been assigned to them for payment processing.
     */
    public static function notifyRegistrationAssigned(User $financeUser, ProgramRegistration $registration): bool
    {
        try {
            UserNotification::create([
                'user_id' => $financeUser->id,
                'title' => 'New Lead Assigned',
                'message' => 'A registration has been sent to you for payment processing. Applicant: '.($registration->full_name ?? 'N/A').', Program: '.($registration->program?->title ?? 'N/A'),
                'priority' => UserNotification::PRIORITY_IMPORTANT,
                'link_url' => route('finance.registrations.show', $registration),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Finance notification failed', [
                'finance_user_id' => $financeUser->id,
                'registration_id' => $registration->id,
                'error' => $e->getMessage(),
            ]);
            report($e);

            return false;
        }

        self::sendPaymentReadyEmailToFinanceUser($financeUser, $registration);

        return true;
    }

    /**
     * Notify a finance user that an application has been assigned to them for payment processing.
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
                'message' => 'An application has been sent to you for payment processing. Applicant: '.$applicantName.', Program: '.$programTitle,
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

    /**
     * Notify every active finance user that a registration is in the shared finance queue.
     */
    public static function notifyFinanceTeamRegistrationQueued(ProgramRegistration $registration): void
    {
        $registration->loadMissing('program');

        $financeUserIds = User::query()
            ->whereHas('role', fn ($q) => $q->where('name', 'finance'))
            ->whereHas('profile', fn ($q) => $q->where('status', 1))
            ->pluck('id');

        foreach ($financeUserIds as $userId) {
            try {
                UserNotification::create([
                    'user_id' => $userId,
                    'title' => 'Application ready for finance',
                    'message' => 'A case manager approved an application. Payment processing is needed for '
                        .($registration->full_name ?? 'an applicant')
                        .' — '.($registration->program?->title ?? 'Program').'.',
                    'priority' => UserNotification::PRIORITY_IMPORTANT,
                    'link_url' => route('finance.registrations.show', $registration),
                ]);
            } catch (\Throwable $e) {
                Log::warning('Finance queue notification failed', [
                    'finance_user_id' => $userId,
                    'registration_id' => $registration->id,
                    'error' => $e->getMessage(),
                ]);
                report($e);
            }
        }

        self::sendPaymentReadyEmailsToFinanceTeam($registration);
    }

    /**
     * Email subject "Payment Ready for Processing" — immediate after application reaches finance queue.
     */
    public static function sendPaymentReadyEmailsToFinanceTeam(ProgramRegistration $registration): void
    {
        $registration->loadMissing('program', 'assignedCaseManager.profile');

        $reference = $registration->public_reference;
        $approvedAmount = $registration->calculated_grant_amount;

        $financeUsers = User::query()
            ->whereHas('role', fn ($q) => $q->where('name', 'finance'))
            ->whereHas('profile', fn ($q) => $q->where('status', 1))
            ->get();

        foreach ($financeUsers as $user) {
            if (! filled($user->email)) {
                continue;
            }
            try {
                Mail::to($user->email)->send(new PaymentReadyForFinance($registration, $reference, $approvedAmount));
            } catch (\Throwable $e) {
                Log::warning('Payment ready email failed', [
                    'finance_user_id' => $user->id,
                    'registration_id' => $registration->id,
                    'error' => $e->getMessage(),
                ]);
                report($e);
            }
        }
    }

    protected static function sendPaymentReadyEmailToFinanceUser(User $financeUser, ProgramRegistration $registration): void
    {
        if (! filled($financeUser->email)) {
            return;
        }
        $registration->loadMissing('program', 'assignedCaseManager.profile');
        try {
            Mail::to($financeUser->email)->send(new PaymentReadyForFinance(
                $registration,
                $registration->public_reference,
                $registration->calculated_grant_amount
            ));
        } catch (\Throwable $e) {
            Log::warning('Payment ready email (assigned) failed', [
                'finance_user_id' => $financeUser->id,
                'registration_id' => $registration->id,
                'error' => $e->getMessage(),
            ]);
            report($e);
        }
    }
}
