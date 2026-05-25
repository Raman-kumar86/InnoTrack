<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class ReportsService
{
    public function fiscalRange(string $fy): array
    {
        $cfg = (array) config("reports.fiscal_years.{$fy}", []);

        return [
            'start' => Carbon::parse($cfg['start'] ?? now()->startOfYear()),
            'end' => Carbon::parse($cfg['end'] ?? now()->endOfYear()),
            'label' => $cfg['label'] ?? $fy,
        ];
    }

    public function customRange(?string $from, ?string $to): array
    {
        $start = $from ? Carbon::parse($from)->startOfDay() : Carbon::now()->subMonths(6)->startOfDay();
        $end = $to ? Carbon::parse($to)->endOfDay() : Carbon::now()->endOfDay();

        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    public function getKpiSummary(Carbon $start, Carbon $end, Carbon $prevStart, Carbon $prevEnd): array
    {
        $usdToInr = (float) config('reports.usd_to_inr', 83.5);

        $fundingUsd = (float) DB::table('funding_rounds')
            ->whereBetween('funding_date', [$start, $end])
            ->sum('amount_raised_usd');

        $prevFundingUsd = (float) DB::table('funding_rounds')
            ->whereBetween('funding_date', [$prevStart, $prevEnd])
            ->sum('amount_raised_usd');

        $fundingCr = round($fundingUsd * $usdToInr / 10000000, 2);
        $yoyGrowth = $prevFundingUsd > 0 ? round((($fundingUsd - $prevFundingUsd) / $prevFundingUsd) * 100, 1) : 0;

        $approvedGrants = DB::table('startups')
            ->where('dpiit_recognized', 'Yes')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $printableReports = DB::table('startup_updates')
            ->where('is_published', true)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        return [
            'total_funding_cr' => $fundingCr,
            'total_funding_usd' => $fundingUsd,
            'yoy_growth' => $yoyGrowth,
            'yoy_direction' => $yoyGrowth >= 0 ? 'up' : 'down',
            'approved_grants' => $approvedGrants,
            'printable_reports' => $printableReports,
        ];
    }

    public function getYoyComparison(string $fy1, string $fy2): array
    {
        $r1 = $this->fiscalRange($fy1);
        $r2 = $this->fiscalRange($fy2);

        $calc = function (Carbon $start, Carbon $end): array {
            $usdToInr = (float) config('reports.usd_to_inr', 83.5);

            $funding = (float) DB::table('funding_rounds')
                ->whereBetween('funding_date', [$start, $end])
                ->sum('amount_raised_usd');

            return [
                'total_funding_cr' => round($funding * $usdToInr / 10000000, 2),
                'active_startups' => DB::table('startups')->where('status', 'Active')->whereBetween('created_at', [$start, $end])->count(),
                'registrations' => DB::table('startups')->whereBetween('created_at', [$start, $end])->count(),
                'dpiit_recognized' => DB::table('startups')->where('dpiit_recognized', 'Yes')->whereBetween('created_at', [$start, $end])->count(),
                'jobs_created' => DB::table('startups')->whereBetween('created_at', [$start, $end])->sum('jobs_created'),
                'women_led' => DB::table('startups')->where('women_led', 'Yes')->whereBetween('created_at', [$start, $end])->count(),
            ];
        };

        $data1 = $calc($r1['start'], $r1['end']);
        $data2 = $calc($r2['start'], $r2['end']);

        $comparison = [];
        foreach ($data1 as $metric => $value1) {
            $value2 = $data2[$metric] ?? 0;
            $change = $value2 > 0 ? round((($value1 - $value2) / $value2) * 100, 1) : 0;

            $comparison[] = [
                'metric' => $metric,
                'fy1_label' => $r1['label'],
                'fy2_label' => $r2['label'],
                'fy1_value' => $value1,
                'fy2_value' => $value2,
                'change' => $change,
                'direction' => $change >= 0 ? 'up' : 'down',
            ];
        }

        return [
            'fy1' => $fy1,
            'fy2' => $fy2,
            'fy1_label' => $r1['label'],
            'fy2_label' => $r2['label'],
            'comparison' => $comparison,
        ];
    }

    public function getFundingGrowth(Carbon $start, Carbon $end): array
    {
        $rows = DB::table('funding_rounds')
            ->whereBetween('funding_date', [$start, $end])
            ->select([
                DB::raw("DATE_FORMAT(funding_date, '%Y-%m') as month_key"),
                DB::raw('SUM(amount_raised_usd) as total_usd'),
                DB::raw('COUNT(*) as deal_count'),
            ])
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->get()
            ->keyBy('month_key');

        $period = CarbonPeriod::create($start->copy()->startOfMonth(), '1 month', $end->copy()->endOfMonth());
        $usdToInr = (float) config('reports.usd_to_inr', 83.5);

        $months = [];
        $cumulative = 0;

        foreach ($period as $month) {
            $key = $month->format('Y-m');
            $usd = (float) ($rows->get($key)?->total_usd ?? 0);
            $cr = round($usd * $usdToInr / 10000000, 2);
            $cumulative += $cr;

            $months[] = [
                'month_key' => $key,
                'month_label' => $month->format('M y'),
                'funding_cr' => $cr,
                'funding_usd' => $usd,
                'cumulative_cr' => round($cumulative, 2),
                'deal_count' => (int) ($rows->get($key)?->deal_count ?? 0),
            ];
        }

        return [
            'months' => $months,
            'date_range' => $start->format('M Y') . ' - ' . $end->format('M Y'),
            'total_cr' => round($cumulative, 2),
        ];
    }

    public function getGrantApprovalRate(Carbon $start, Carbon $end): array
    {
        $period = CarbonPeriod::create($start->copy()->startOfMonth(), '1 month', $end->copy()->endOfMonth());

        $regs = DB::table('startups')
            ->whereBetween('created_at', [$start, $end])
            ->select([
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_key"),
                DB::raw('COUNT(*) as total'),
            ])
            ->groupBy('month_key')
            ->get()
            ->keyBy('month_key');

        $recognized = DB::table('startups')
            ->where('dpiit_recognized', 'Yes')
            ->whereBetween('created_at', [$start, $end])
            ->select([
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_key"),
                DB::raw('COUNT(*) as total'),
            ])
            ->groupBy('month_key')
            ->get()
            ->keyBy('month_key');

        $months = [];
        foreach ($period as $month) {
            $key = $month->format('Y-m');
            $total = (int) ($regs->get($key)?->total ?? 0);
            $recog = (int) ($recognized->get($key)?->total ?? 0);
            $rate = $total > 0 ? round(($recog / $total) * 100, 1) : 0;

            $months[] = [
                'month_key' => $key,
                'month_label' => $month->format('M y'),
                'registrations' => $total,
                'recognized' => $recog,
                'approval_rate' => $rate,
            ];
        }

        return [
            'months' => $months,
            'date_range' => $start->format('M Y') . ' - ' . $end->format('M Y'),
            'total_reg' => collect($months)->sum('registrations'),
            'total_recog' => collect($months)->sum('recognized'),
            'overall_rate' => collect($months)->sum('registrations') > 0 ? round((collect($months)->sum('recognized') / collect($months)->sum('registrations')) * 100, 1) : 0,
        ];
    }

    public function getMonthlyDetail(Carbon $start, Carbon $end): array
    {
        $funding = collect($this->getFundingGrowth($start, $end)['months'])->keyBy('month_key');
        $approval = collect($this->getGrantApprovalRate($start, $end)['months'])->keyBy('month_key');

        $period = CarbonPeriod::create($start->copy()->startOfMonth(), '1 month', $end->copy()->endOfMonth());

        $rows = [];
        foreach ($period as $month) {
            $key = $month->format('Y-m');
            $f = $funding->get($key);
            $a = $approval->get($key);

            $rows[] = [
                'month_label' => $month->format('M'),
                'month_key' => $key,
                'funding_usd' => $f['funding_usd'] ?? 0,
                'cumulative_usd' => $f['cumulative_cr'] ?? 0,
                'registrations' => $a['registrations'] ?? 0,
                'recognized' => $a['recognized'] ?? 0,
                'approval_pct' => $a['approval_rate'] ?? 0,
            ];
        }

        return $rows;
    }
}