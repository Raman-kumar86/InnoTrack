<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index(Request $request): View
    {
        $currentYear = Carbon::now()->year;
        $availableYears = range($currentYear - 4, $currentYear);
        $selectedYear = max(min($request->integer('year', $currentYear), $currentYear), $currentYear - 4);
        $selectedMonth = max(1, min(12, $request->integer('month', Carbon::now()->month)));

        $startDateStr = $request->input('start_date');
        $endDateStr = $request->input('end_date');

        if (!empty($startDateStr) && !empty($endDateStr)) {
            $startDate = Carbon::parse($startDateStr)->startOfMonth();
            $endDate = Carbon::parse($endDateStr)->endOfMonth();
            $monthsCount = max(1, $startDate->diffInMonths($endDate) + 1);
            [$months, $monthKeys] = $this->buildMonthSeries($monthsCount, $endDate);
            $selectedStartDate = $startDate->toDateString();
            $selectedEndDate = $endDate->toDateString();
        } else {
            $filterApplied = $request->filled('month') || $request->filled('year');

            if ($filterApplied) {
                [$months, $monthKeys] = $this->buildMonthSeries($selectedMonth, Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth());
            } else {
                [$months, $monthKeys] = $this->buildMonthSeries(6);
            }

            $selectedStartDate = null;
            $selectedEndDate = null;
        }

        $monthStart = Carbon::createFromFormat('Y-m', $monthKeys[0])->startOfMonth();
        $monthEnd = Carbon::createFromFormat('Y-m', $monthKeys[count($monthKeys) - 1])->endOfMonth();

        $fundingByMonth = DB::table('startups')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, COALESCE(SUM(total_funding_usd), 0) as total")
            ->whereNotNull('created_at')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->groupBy('month_key')
            ->pluck('total', 'month_key')
            ->all();

        $registrationsByMonth = DB::table('startups')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, COUNT(*) as total")
            ->whereNotNull('created_at')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->groupBy('month_key')
            ->pluck('total', 'month_key')
            ->all();

        $recognizedByMonth = DB::table('startups')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, COUNT(*) as total")
            ->where('dpiit_recognized', 'Yes')
            ->whereNotNull('created_at')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->groupBy('month_key')
            ->pluck('total', 'month_key')
            ->all();

        $fundingSeries = $this->cumulativeSeriesFromMonthMap($monthKeys, $fundingByMonth, 'float');

        $registrationsSeries = $this->seriesFromMonthMap($monthKeys, $registrationsByMonth, 'int');
        $recognizedSeries = $this->seriesFromMonthMap($monthKeys, $recognizedByMonth, 'int');

        // Build table rows for month-by-month detail
        $tableRows = [];
        foreach ($monthKeys as $idx => $monthKey) {
            $monthLabel = $months[$idx] ?? $monthKey;
            $monthlyFunding = (float) ($fundingByMonth[$monthKey] ?? 0);
            $cumulative = (float) ($fundingSeries[$idx] ?? 0);
            $registrations = (int) ($registrationsSeries[$idx] ?? 0);
            $recognized = (int) ($recognizedSeries[$idx] ?? 0);
            $approval = $registrations > 0 ? round((($recognized / $registrations) * 100), 1) : 0.0;

            $tableRows[] = [
                'label' => $monthLabel,
                'month_key' => $monthKey,
                'funding' => $monthlyFunding,
                'cumulative' => $cumulative,
                'registrations' => $registrations,
                'recognized' => $recognized,
                'approval' => $approval,
            ];
        }

        $approvalSeries = [];
        foreach ($registrationsSeries as $idx => $reg) {
            $rec = $recognizedSeries[$idx] ?? 0;
            $approvalSeries[] = $reg > 0 ? round((($rec / $reg) * 100), 1) : 0;
        }

        $growthDatasets = [[
            'label' => 'Funding growth',
            'data' => $fundingSeries,
            'borderColor' => '#4f46e5',
            'backgroundColor' => 'rgba(79, 70, 229, 0.12)',
            'tension' => 0.42,
            'fill' => true,
        ]];

        $approvalDatasets = [[
            'label' => 'Approval rate',
            'data' => $approvalSeries,
            'backgroundColor' => '#0ea5e9',
        ]];

        $fundingTotalUsd = array_sum(array_values($fundingByMonth));
        $fundingTotalCr = $fundingTotalUsd > 0 ? round($fundingTotalUsd / 10000000, 2) : 0;

        $approvedGrants = array_sum($recognizedSeries);
        $printableReports = (int) DB::table('notifications')->where('notification_type', 'export_report')->count();

        $selectedRangeLabel = Carbon::createFromFormat('Y-m', $monthKeys[0])->format('M Y') . ' - ' . Carbon::createFromFormat('Y-m', $monthKeys[count($monthKeys) - 1])->format('M Y');

        return view('reports.index', [
            'title' => 'Reports',
            'pageTitle' => 'Reports',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('dashboard')],
                ['label' => 'Reports', 'url' => route('reports.index')],
            ],
            'months' => $months,
            'growthDatasets' => $growthDatasets,
            'approvalDatasets' => $approvalDatasets,
            'summary' => [
                ['label' => 'Total funding', 'value' => 'Rs ' . number_format($fundingTotalCr) . ' Cr'],
                ['label' => 'YoY growth', 'value' => $this->trendFromSeries($fundingSeries)],
                ['label' => 'Approved grants', 'value' => number_format($approvedGrants)],
                ['label' => 'Printable reports', 'value' => number_format($printableReports)],
            ],
            'monthOptions' => [
                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June',
                7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
            ],
            'availableYears' => $availableYears,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'selectedRangeLabel' => $selectedRangeLabel,
            'selectedStartDate' => $selectedStartDate ?? null,
            'selectedEndDate' => $selectedEndDate ?? null,
            'tableRows' => $tableRows,
        ]);
    }

    /**
     * @return array{0: array<int, string>, 1: array<int, string>}
     */
    private function buildMonthSeries(int $months, ?Carbon $endDate = null): array
    {
        $labels = [];
        $keys = [];
        $cursor = ($endDate ?? Carbon::now()->endOfMonth())->copy()->startOfMonth()->subMonths(max(0, $months - 1));

        for ($index = 0; $index < $months; $index++) {
            $labels[] = $cursor->format('M');
            $keys[] = $cursor->format('Y-m');
            $cursor->addMonth();
        }

        return [$labels, $keys];
    }

    /**
     * @param array<int, string> $monthKeys
     * @param array<string, mixed> $monthMap
     * @return array<int, int|float>
     */
    private function seriesFromMonthMap(array $monthKeys, array $monthMap, string $cast = 'int'): array
    {
        return array_map(static function (string $monthKey) use ($monthMap, $cast): int|float {
            $value = $monthMap[$monthKey] ?? 0;

            return $cast === 'float' ? (float) $value : (int) $value;
        }, $monthKeys);
    }

    /**
     * @param array<int, string> $monthKeys
     * @param array<string, mixed> $monthMap
     * @return array<int, int|float>
     */
    private function cumulativeSeriesFromMonthMap(array $monthKeys, array $monthMap, string $cast = 'int'): array
    {
        $runningTotal = 0.0;

        return array_map(static function (string $monthKey) use ($monthMap, $cast, &$runningTotal): int|float {
            $value = $monthMap[$monthKey] ?? 0;
            $runningTotal += (float) $value;

            return $cast === 'float' ? $runningTotal : (int) $runningTotal;
        }, $monthKeys);
    }

    /**
     * @param array<int, int|float> $series
     */
    private function trendFromSeries(array $series): string
    {
        $count = count($series);

        if ($count < 2) {
            return '+0.0%';
        }

        $previous = (float) $series[$count - 2];
        $current = (float) $series[$count - 1];

        if ($previous <= 0) {
            return '+0.0%';
        }

        $delta = (($current - $previous) / $previous) * 100;

        return sprintf('%s%.1f%%', $delta >= 0 ? '+' : '', $delta);
    }
}
