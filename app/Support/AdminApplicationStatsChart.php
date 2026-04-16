<?php

namespace App\Support;

use App\Models\Application;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class AdminApplicationStatsChart
{
    public const PERIOD_LABELS = [
        'day' => 'Day',
        'week' => 'Week',
        'month' => 'Month',
        'all' => 'All Time',
    ];

    public static function normalizePeriod(?string $period): string
    {
        $p = strtolower((string) $period);
        $allowed = array_keys(self::PERIOD_LABELS);

        return in_array($p, $allowed, true) ? $p : 'week';
    }

    /**
     * @return array<string, array{apps: int, approved: int, rejected: int}>
     */
    public static function series(?Builder $query = null, ?string $period = null): array
    {
        $period = self::normalizePeriod($period);

        $chartData = [];

        switch ($period) {
            case 'day':
                for ($i = 5; $i >= 0; $i--) {
                    $day = Carbon::today()->subDays($i);
                    $label = $day->format('M d');
                    self::pushBucketCounts($chartData, $label, function (Builder $q) use ($day) {
                        $q->whereDate('created_at', $day->toDateString());
                    });
                }
                break;

            case 'week':
                $weekdayToLabel = [1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat', 7 => 'Sun'];
                for ($i = 6; $i >= 0; $i--) {
                    $day = Carbon::today()->subDays($i);
                    $label = $weekdayToLabel[(int) $day->isoWeekday()];
                    self::pushBucketCounts($chartData, $label, function (Builder $q) use ($day) {
                        $q->whereDate('created_at', $day->toDateString());
                    });
                }
                break;

            case 'month':
                for ($i = 3; $i >= 0; $i--) {
                    $monthPoint = Carbon::now()->subMonths($i);
                    $monthStart = $monthPoint->copy()->startOfMonth();
                    $monthEnd = $monthPoint->copy()->endOfMonth();
                    $label = $monthPoint->format('M');
                    self::pushBucketCounts($chartData, $label, function (Builder $q) use ($monthStart, $monthEnd) {
                        $q->whereBetween('created_at', [$monthStart, $monthEnd]);
                    });
                }
                break;

            case 'all':
                for ($i = 5; $i >= 0; $i--) {
                    $month = Carbon::now()->subMonths($i);
                    $label = $month->format('M');
                    self::pushBucketCounts($chartData, $label, function (Builder $q) use ($month) {
                        $q->whereYear('created_at', $month->year)
                            ->whereMonth('created_at', $month->month);
                    });
                }
                break;
        }

        return $chartData;
    }

    /**
     * @param  array<string, array{apps: int, approved: int, rejected: int}>  $chartData
     * @param  callable(Builder): void  $scope
     */
    private static function pushBucketCounts(array &$chartData, string $label, callable $scope): void
    {
        $totalQ = Application::query();
        $scope($totalQ);
        $total = $totalQ->count();

        $approvedQ = Application::query();
        $scope($approvedQ);
        $approved = $approvedQ->where('status', 'Approved')->count();

        $rejectedQ = Application::query();
        $scope($rejectedQ);
        $rejected = $rejectedQ->where('status', 'Rejected')->count();
        $remain = max(0, $total - $approved - $rejected);

        if ($total > 0) {
            $appsPct = (int) round(($remain / $total) * 100);
            $approvedPct = (int) round(($approved / $total) * 100);
            $rejectedPct = max(0, 100 - $appsPct - $approvedPct);
        } else {
            $appsPct = $approvedPct = $rejectedPct = 0;
        }

        $chartData[$label] = [
            'apps' => $appsPct,
            'approved' => $approvedPct,
            'rejected' => $rejectedPct,
        ];
    }
}
