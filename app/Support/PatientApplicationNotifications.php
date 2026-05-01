<?php

namespace App\Support;

use App\Models\Application;
use App\Models\Patient;
use App\Models\Program;
use App\Models\ProgramRegistration;
use App\Models\RegistrationInvoice;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Support\Str;

class PatientApplicationNotifications
{
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
    }

    public static function programRegistrationSubmitted(ProgramRegistration $registration): void
    {
        if (! $registration->user_id) {
            return;
        }
        $registration->loadMissing('program');
        $programTitle = $registration->program?->title ?? 'the program';
        self::notifyProgramRegistrationPatient(
            $registration,
            'Application received',
            'We received your financial assistance application for "'.$programTitle.'". It is pending review — we will notify you here as the status changes.',
            UserNotification::PRIORITY_NORMAL
        );
    }

    public static function programRegistrationCaseManagerAssigned(ProgramRegistration $registration): void
    {
        $registration->loadMissing('program');
        $programTitle = $registration->program?->title ?? 'your application';
        self::notifyProgramRegistrationPatient(
            $registration,
            'Case manager assigned',
            'A case manager is now reviewing your application for "'.$programTitle.'".',
        );
    }

    public static function programRegistrationBudgetAllocated(ProgramRegistration $registration, RegistrationInvoice $invoice): void
    {
        $registration->loadMissing('program');
        $programTitle = $registration->program?->title ?? 'your program application';
        self::notifyProgramRegistrationPatient(
            $registration,
            'Budget allocated',
            'Budget has been allocated for "'.$programTitle.'". Invoice '.$invoice->invoice_number.' has been generated.',
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

    public static function programCreatedForPatients(Program $program): void
    {
        $programTitle = trim((string) ($program->title ?? 'New Program'));
        $message = 'A new support program "'.$programTitle.'" is now available. You can review details and apply now.';
        $link = route('patient.programs.show', ['id' => $program->id]);

        $now = now();
        self::patientUserIds()->chunk(300)->each(function ($ids) use ($message, $link, $now): void {
            $rows = collect($ids)->map(fn ($userId): array => [
                'user_id' => (int) $userId,
                'title' => 'New program available',
                'message' => $message,
                'priority' => UserNotification::PRIORITY_IMPORTANT,
                'link_url' => $link,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all();

            try {
                UserNotification::query()->insert($rows);
            } catch (\Throwable $e) {
                report($e);
            }
        });
    }
}
