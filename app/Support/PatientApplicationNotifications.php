<?php

namespace App\Support;

use App\Mail\ProgramRegistrationStatus;
use App\Models\Application;
use App\Models\Patient;
use App\Models\Program;
use App\Models\ProgramRegistration;
use App\Models\RegistrationInvoice;
use App\Models\User;
use App\Models\UserNotification;
use App\Support\TransactionalMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PatientApplicationNotifications
{
    /**
     * Titles for which we skip the generic UserNotification email — a dedicated mailable is sent instead.
     *
     * @var list<string>
     */
    public const SKIP_GENERIC_EMAIL_TITLES = [
        'Bills paid',
        'Budget allocated',
    ];

    /**
     * Generic UserNotification email is skipped — branded ProgramRegistrationStatus is sent instead.
     */
    public static function shouldSkipGenericNotificationEmail(UserNotification $notification): bool
    {
        if (in_array((string) ($notification->title ?? ''), self::SKIP_GENERIC_EMAIL_TITLES, true)) {
            return true;
        }

        $link = (string) ($notification->link_url ?? '');

        return $link !== '' && str_contains($link, 'program-registrations');
    }

    /**
     * Branded transactional email for program applications (all registrants with a valid email).
     */
    private static function sendProgramRegistrationStatusEmail(
        ProgramRegistration $registration,
        string $mailStatusLabel,
        ?string $noteForEmail = null
    ): void {
        $email = self::applicantEmailForProgramRegistration($registration);
        if (! $email) {
            return;
        }
        try {
            TransactionalMail::send($email, new ProgramRegistrationStatus($registration, $mailStatusLabel, $noteForEmail));
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * @return \Illuminate\Support\Collection<int, int>
     */
    private static function patientUserIds(): \Illuminate\Support\Collection
    {
        $roleBased = User::query()
            ->whereHas('role', fn ($q) => $q->where('name', 'patient'))
            ->pluck('id');

        $patientTableBased = Patient::query()
            ->whereNotNull('user_id')
            ->pluck('user_id');

        return $roleBased
            ->merge($patientTableBased)
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();
    }

    /**
     * Resolve the patient user id for in-app notifications (linked account or patient role with same email).
     */
    public static function patientUserIdForProgramRegistration(ProgramRegistration $registration): ?int
    {
        if ($registration->user_id) {
            return (int) $registration->user_id;
        }
        if (! filled($registration->email)) {
            return null;
        }

        $id = User::query()
            ->where('email', $registration->email)
            ->whereHas('role', fn ($q) => $q->where('name', 'patient'))
            ->value('id');

        return $id ? (int) $id : null;
    }

    /**
     * Applicant email for transactional mail: application form address first, then portal account.
     * Matches finance-facing helpers — avoids silent drops when user.email is "" (PHP ?? does not fall back).
     */
    public static function applicantEmailForProgramRegistration(ProgramRegistration $registration): ?string
    {
        $registration->loadMissing('user');

        foreach ([$registration->email, $registration->user?->email] as $candidate) {
            $e = strtolower(trim((string) $candidate));
            if ($e !== '' && filter_var($e, FILTER_VALIDATE_EMAIL)) {
                return $e;
            }
        }

        return null;
    }

    /**
     * In-app notification when the patient has a portal account; otherwise a ProgramRegistrationStatus email.
     */
    public static function notifyProgramRegistrationApplicant(
        ProgramRegistration $registration,
        string $mailStatusLabel,
        string $title,
        string $message,
        ?string $noteForEmail = null,
        string $priority = UserNotification::PRIORITY_IMPORTANT
    ): void {
        $userId = self::patientUserIdForProgramRegistration($registration);
        if ($userId) {
            try {
                UserNotification::create([
                    'user_id' => $userId,
                    'title' => $title,
                    'message' => $message,
                    'priority' => $priority,
                    'link_url' => route('patient.programRegistrations.show', $registration),
                ]);
            } catch (\Throwable $e) {
                report($e);
            }

            // Logged-in patients still receive the same branded ProgramRegistrationStatus email as guests.
            self::sendProgramRegistrationStatusEmail($registration, $mailStatusLabel, $noteForEmail);

            return;
        }

        self::sendProgramRegistrationStatusEmail($registration, $mailStatusLabel, $noteForEmail);
    }

    public static function notifyProgramRegistrationPatient(
        ProgramRegistration $registration,
        string $title,
        string $message,
        string $priority = UserNotification::PRIORITY_IMPORTANT
    ): void {
        $userId = self::patientUserIdForProgramRegistration($registration);
        if (! $userId) {
            return;
        }
        try {
            UserNotification::create([
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'priority' => $priority,
                'link_url' => route('patient.programRegistrations.show', $registration),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }

        // Invoice PDF uses BudgetAllocatedToPatient; generic notification email is skipped via SKIP_GENERIC_EMAIL_TITLES.
        if ($title !== 'Bills paid' && $title !== 'Budget allocated') {
            self::sendProgramRegistrationStatusEmail($registration, $title, null);
        }
    }

    public static function programRegistrationSubmitted(ProgramRegistration $registration): void
    {
        $registration->loadMissing('program');

        if ($registration->isMomentsThatMatterApplication()) {
            self::notifyProgramRegistrationApplicant(
                $registration,
                'Received',
                \App\Support\MomentsThatMatterNotice::TITLE,
                \App\Support\MomentsThatMatterNotice::notificationMessage(),
                null,
                UserNotification::PRIORITY_NORMAL
            );

            return;
        }

        $programTitle = $registration->program?->title ?? 'the program';
        self::notifyProgramRegistrationApplicant(
            $registration,
            'Received',
            'Application received',
            'Thank you for submitting your application for "'.$programTitle.'". We will contact you through the Patient Portal and chat support as your application is processed.',
            null,
            UserNotification::PRIORITY_NORMAL
        );
    }

    public static function programRegistrationCaseManagerAssigned(ProgramRegistration $registration): void
    {
        $registration->loadMissing('program');
        $programTitle = $registration->program?->title ?? 'your application';
        self::notifyProgramRegistrationApplicant(
            $registration,
            'Patient Support Coordinator assigned',
            'Patient Support Coordinator assigned',
            'A Patient Support Coordinator is now reviewing your application for "'.$programTitle.'".',
            null,
            UserNotification::PRIORITY_IMPORTANT
        );
    }

    public static function programRegistrationBudgetAllocated(ProgramRegistration $registration, RegistrationInvoice $invoice): void
    {
        $registration->loadMissing('program');
        $programTitle = $registration->program?->title ?? 'your program application';
        self::notifyProgramRegistrationPatient(
            $registration,
            'Bills paid',
            'Your bill has been paid through the financial assistance program for "'.$programTitle.'". Please allow a few days for the payment to reflect on your account. Message us in the patient portal with questions.',
        );
    }

    public static function applicationSubmitted(Application $application): void
    {
        $user = optional($application->patient)->user;
        if (! $user) {
            return;
        }
        $code = $application->code ?: ('APP-'.str_pad((string) $application->id, 6, '0', STR_PAD_LEFT));
        try {
            UserNotification::create([
                'user_id' => $user->id,
                'title' => 'Application received',
                'message' => "We received your application {$code}. It is pending review — we will notify you here as the status changes.",
                'priority' => UserNotification::PRIORITY_NORMAL,
                'link_url' => route('patient.viewApplication', $application->id),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public static function applicationReviewerAssigned(Application $application, string $reviewerDisplayName): void
    {
        $user = optional($application->patient)->user;
        if (! $user) {
            return;
        }
        $code = $application->code ?: ('APP-'.str_pad((string) $application->id, 6, '0', STR_PAD_LEFT));
        try {
            UserNotification::create([
                'user_id' => $user->id,
                'title' => 'Case manager assigned',
                'message' => "Your application {$code} has been assigned to {$reviewerDisplayName} for review.",
                'priority' => UserNotification::PRIORITY_IMPORTANT,
                'link_url' => route('patient.viewApplication', $application->id),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public static function applicationMissingDocumentsRequested(Application $application, string $message): void
    {
        $user = optional($application->patient)->user;
        if (! $user) {
            return;
        }
        $code = $application->code ?: ('APP-'.str_pad((string) $application->id, 6, '0', STR_PAD_LEFT));
        $snippet = Str::limit(trim($message), 200);
        try {
            UserNotification::create([
                'user_id' => $user->id,
                'title' => 'Action needed: more information',
                'message' => "Your case manager needs something for {$code}: {$snippet}",
                'priority' => UserNotification::PRIORITY_IMPORTANT,
                'link_url' => route('patient.viewApplication', $application->id),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public static function legacyApplicationSentToFinance(Application $application, string $financeDisplayName): void
    {
        $application->loadMissing(['patient.user', 'program']);
        $user = optional($application->patient)->user;
        if (! $user) {
            return;
        }
        $code = $application->code ?: ('APP-'.str_pad((string) $application->id, 6, '0', STR_PAD_LEFT));
        $programTitle = $application->program?->title ?? 'your application';
        try {
            UserNotification::create([
                'user_id' => $user->id,
                'title' => 'Sent to finance',
                'message' => "Your application {$code} for {$programTitle} has been sent to finance ({$financeDisplayName}) for payment processing.",
                'priority' => UserNotification::PRIORITY_IMPORTANT,
                'link_url' => route('patient.viewApplication', $application->id),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public static function programCreatedForPatients(Program $program): void
    {
        $programTitle = trim((string) ($program->title ?? 'New Program'));
        $message = 'A new support program "'.$programTitle.'" is now available. You can review details and apply now.';
        $link = route('patient.programs.show', ['id' => $program->id]);

        self::patientUserIds()->chunk(150)->each(function ($ids) use ($message, $link): void {
            foreach ($ids as $userId) {
                try {
                    UserNotification::create([
                        'user_id' => (int) $userId,
                        'title' => 'New program available',
                        'message' => $message,
                        'priority' => UserNotification::PRIORITY_IMPORTANT,
                        'link_url' => $link,
                    ]);
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        });
    }
}
