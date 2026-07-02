<?php

namespace App\Support;

use App\Models\ProgramRegistration;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FinancialAssistanceApplicationPeriod
{
    public const OPTION1 = 'option1';

    public const OPTION2 = 'option2';

    public static function quarterLabel(string $quarter): string
    {
        return match ($quarter) {
            self::OPTION1 => 'May–June',
            self::OPTION2 => 'Nov–Dec',
            default => $quarter,
        };
    }

    public static function periodKey(string $quarter, int $year): string
    {
        return $quarter.'_'.$year;
    }

    /**
     * @return array{quarter: string, year: int}|null
     */
    public static function parsePeriodKey(?string $key): ?array
    {
        if ($key === null || $key === '' || $key === 'all') {
            return null;
        }

        if (! preg_match('/^(option1|option2)_(\d{4})$/', $key, $matches)) {
            return null;
        }

        return [
            'quarter' => $matches[1],
            'year' => (int) $matches[2],
        ];
    }

    public static function label(?string $periodKey): string
    {
        $parsed = self::parsePeriodKey($periodKey);
        if (! $parsed) {
            return 'All periods';
        }

        return self::quarterLabel($parsed['quarter']).' '.$parsed['year'];
    }

    public static function archiveFilename(string $periodKey): string
    {
        $parsed = self::parsePeriodKey($periodKey);
        if (! $parsed) {
            return 'ARCHIVE_Financial_Assistance_'.now()->format('Ymd').'.csv';
        }

        $name = str_replace(['–', ' '], ['-', '_'], self::quarterLabel($parsed['quarter']));

        return 'ARCHIVE_'.$name.'_'.$parsed['year'].'.csv';
    }

    public static function isClosed(string $quarter, int $year): bool
    {
        $end = $quarter === self::OPTION1
            ? Carbon::create($year, 6, 30)->endOfDay()
            : Carbon::create($year, 12, 31)->endOfDay();

        return now()->gt($end);
    }

    public static function applyToQuery(Builder $query, ?string $periodKey): Builder
    {
        $parsed = self::parsePeriodKey($periodKey);
        if (! $parsed) {
            return $query;
        }

        return $query
            ->where('quarter_applied', $parsed['quarter'])
            ->whereYear('created_at', $parsed['year']);
    }

    protected static function createdAtYearExpression(): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "CAST(strftime('%Y', created_at) AS INTEGER)",
            default => 'YEAR(created_at)',
        };
    }

    /**
     * @return Collection<int, array{key: string, label: string, quarter: string, year: int, count: int, closed: bool}>
     */
    public static function periodsWithCounts(): Collection
    {
        $yearExpression = self::createdAtYearExpression();

        $rows = ProgramRegistration::query()
            ->forApplicationType(ProgramType::FINANCIAL_ASSISTANCE)
            ->whereNotNull('quarter_applied')
            ->whereIn('quarter_applied', [self::OPTION1, self::OPTION2])
            ->select('quarter_applied')
            ->selectRaw("{$yearExpression} as period_year")
            ->selectRaw('COUNT(*) as total')
            ->groupBy('quarter_applied', DB::raw($yearExpression))
            ->orderByDesc(DB::raw($yearExpression))
            ->orderBy('quarter_applied')
            ->get();

        return $rows->map(function ($row) {
            $quarter = (string) $row->quarter_applied;
            $year = (int) $row->period_year;
            $key = self::periodKey($quarter, $year);

            return [
                'key' => $key,
                'label' => self::label($key),
                'quarter' => $quarter,
                'year' => $year,
                'count' => (int) $row->total,
                'closed' => self::isClosed($quarter, $year),
            ];
        })->values();
    }

    /**
     * @return Collection<int, array{key: string, label: string, quarter: string, year: int, count: int, closed: bool}>
     */
    public static function closedPeriods(): Collection
    {
        return self::periodsWithCounts()->filter(fn (array $period) => $period['closed'])->values();
    }
}
