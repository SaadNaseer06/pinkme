<?php

namespace App\Support;

use App\Models\Program;
use App\Models\ProgramRegistration;
use Illuminate\Support\Collection;

class ProgramApplicationCapacity
{
    public const CLOSED_MESSAGE = 'Due to the overwhelming response and reaching our maximum capacity for submissions currently, we are no longer accepting applications for the Financial Assistance Programs.

Our Financial Assistance grants are currently closed.

We sincerely thank everyone for their interest, and trust in PINK “ME”® as we continue helping families impacted by breast cancer.';

    public static function registrationCount(Program $program): int
    {
        return $program->registrations()->count();
    }

    public static function hasReachedMax(Program $program): bool
    {
        if (! $program->max_applications) {
            return false;
        }

        return self::registrationCount($program) >= (int) $program->max_applications;
    }

    public static function isAcceptingApplications(Program $program): bool
    {
        return $program->isApplicationOpen() && ! self::hasReachedMax($program);
    }

    /**
     * @param  Collection<int, Program>  $ongoingPrograms
     */
    public static function financialAssistanceClosed(Collection $ongoingPrograms): bool
    {
        $financial = $ongoingPrograms->filter(
            fn (Program $program) => ($program->program_type ?? ProgramType::FINANCIAL_ASSISTANCE) === ProgramType::FINANCIAL_ASSISTANCE
        );

        if ($financial->isEmpty()) {
            return false;
        }

        return $financial->every(
            fn (Program $program) => ! self::isAcceptingApplications($program)
        );
    }

    public static function userHasMomentsApplicationThisYear(int $userId, int $programId): bool
    {
        return ProgramRegistration::query()
            ->where('user_id', $userId)
            ->where('program_id', $programId)
            ->where('created_at', '>=', now()->startOfYear())
            ->exists();
    }

    /**
     * @return array<int, int>
     */
    public static function programIdsAtCapacity(Collection $programs): array
    {
        return $programs
            ->filter(fn (Program $program) => self::hasReachedMax($program))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }
}
