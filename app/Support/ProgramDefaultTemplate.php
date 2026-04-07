<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Default custom_fields payload when `programs` is empty — aligned with allowed builder names
 * and typical rows from production (see programs.sql) without legacy keys (payment_type in JSON).
 */
final class ProgramDefaultTemplate
{
    /**
     * @return list<array{id: string, name: string, label: string, type: string, value: string, required: bool}>
     */
    public static function customFields(): array
    {
        $applicationOpens = Carbon::now()->addWeek()->startOfDay();
        $applicationCloses = (clone $applicationOpens)->copy()->addDays(14);
        $eventDate = (clone $applicationOpens)->copy()->addDays(7);

        return [
            self::field('title', 'Title', 'short_text', 'PinkCare Treatment Support – Program'),
            self::field('description', 'Description', 'long_text', 'Financial support for eligible patients undergoing treatment. Applications are reviewed on a rolling basis.'),
            self::field('event_date', 'Program date', 'date', $eventDate->toDateString()),
            self::field('application_start_date', 'Application Start Date', 'date', $applicationOpens->toDateString()),
            self::field('application_end_date', 'Application End Date', 'date', $applicationCloses->toDateString()),
            self::field('event_time', 'Time', 'time', '09:00'),
            self::field('status', 'Status', 'short_text', 'upcoming'),
            self::field('max_applications', 'Maximum Applications', 'number', ''),
        ];
    }

    /**
     * @return array{id: string, name: string, label: string, type: string, value: string, required: bool}
     */
    private static function field(string $name, string $label, string $type, string $value): array
    {
        return [
            'id' => (string) Str::uuid(),
            'name' => $name,
            'label' => $label,
            'type' => $type,
            'value' => $value,
            'required' => false,
        ];
    }
}
