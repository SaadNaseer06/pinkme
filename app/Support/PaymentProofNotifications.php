<?php

namespace App\Support;

use App\Mail\PaymentProofUploadedMail;
use App\Models\ProgramRegistration;
use App\Models\RegistrationInvoice;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentProofNotifications
{
    /**
     * After finance uploads payment proof: notify patient, admins, and assigned case manager (email + in-app where applicable).
     */
    public static function notifyPatientAdminAndCaseManager(ProgramRegistration $registration, RegistrationInvoice $invoice): void
    {
        $registration->loadMissing('program', 'user.profile', 'assignedCaseManager.profile');

        // Payment receipts / proof are not sent to patients; staff and admin are notified below.

        // Admins
        $admins = User::query()
            ->whereHas('role', fn ($q) => $q->where('name', 'admin'))
            ->with('profile')
            ->get();

        foreach ($admins as $admin) {
            try {
                UserNotification::create([
                    'user_id' => $admin->id,
                    'title' => 'Payment proof uploaded',
                    'message' => 'Finance uploaded payment proof for '.($registration->full_name ?? 'applicant').' — '.($registration->program?->title ?? 'Program').'. Invoice '.$invoice->invoice_number.'.',
                    'priority' => UserNotification::PRIORITY_NORMAL,
                    'link_url' => route('admin.program_registrations.show', $registration),
                ]);
                if (filled($admin->email)) {
                    Mail::to($admin->email)->send(new PaymentProofUploadedMail($registration, $invoice, 'admin'));
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        // Case manager
        $cm = $registration->assignedCaseManager;
        if ($cm) {
            try {
                UserNotification::create([
                    'user_id' => $cm->id,
                    'title' => 'Payment proof uploaded',
                    'message' => 'Finance confirmed payment with a receipt for '.($registration->full_name ?? 'your patient').' ('.($registration->program?->title ?? 'Program').'). Invoice '.$invoice->invoice_number.'.',
                    'priority' => UserNotification::PRIORITY_IMPORTANT,
                    'link_url' => route('case_manager.program_registrations.show', $registration),
                ]);
                if (filled($cm->email)) {
                    Mail::to($cm->email)->send(new PaymentProofUploadedMail($registration, $invoice, 'case_manager'));
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }
}
