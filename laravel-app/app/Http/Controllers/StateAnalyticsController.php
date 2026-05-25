<?php

namespace App\Http\Controllers;

use App\Services\StateAnalyticsService;
use Illuminate\Http\Request;

class StateAnalyticsController extends Controller
{
    public function __construct(protected StateAnalyticsService $service)
    {
    }

    public function index(Request $request)
    {
        $fiscalYearsConfig = (array) config('analytics.fiscal_years', []);
        $quartersConfig = (array) config('analytics.quarters', []);

        $defaultFiscalYear = array_key_first($fiscalYearsConfig) ?? 'FY2324';
        $defaultQuarter = array_key_first($quartersConfig) ?? 'ALL';

        $fiscalYear = (string) $request->get('fiscal_year', $defaultFiscalYear);
        $quarter = (string) $request->get('quarter', $defaultQuarter);

        if (! array_key_exists($fiscalYear, $fiscalYearsConfig)) {
            $fiscalYear = $defaultFiscalYear;
        }

        if (! array_key_exists($quarter, $quartersConfig)) {
            $quarter = $defaultQuarter;
        }

        $range = $this->service->buildDateRange($fiscalYear, $quarter);

        $fyKeys = array_keys($fiscalYearsConfig);
        $currentIdx = array_search($fiscalYear, $fyKeys, true);
        $currentIdx = $currentIdx === false ? 0 : $currentIdx;
        $prevFy = $fyKeys[$currentIdx + 1] ?? $fyKeys[$currentIdx] ?? $fiscalYear;

        $prevRange = $this->service->buildDateRange($prevFy, $quarter);

        $statesData = $this->service->getMergedStateData($range['start'], $range['end']);
        $prevData = $this->service->getMergedStateData($prevRange['start'], $prevRange['end']);
        $summary = $this->service->getSummary($statesData, $prevData);

        $fiscalYears = collect($fiscalYearsConfig)
            ->map(fn ($v, $k) => ['value' => $k, 'label' => $v['label']])
            ->values();

        $quarters = collect($quartersConfig)
            ->map(fn ($v, $k) => ['value' => $k, 'label' => $v['label']])
            ->values();

        $appliedFilters = [
            'fiscal_year' => $fiscalYear,
            'fiscal_year_label' => $fiscalYearsConfig[$fiscalYear]['label'],
            'quarter' => $quarter,
            'quarter_label' => $quartersConfig[$quarter]['label'],
            'date_range' => $range['start']->format('d M Y') . ' - ' . $range['end']->format('d M Y'),
        ];

        $breadcrumbs = [
            ['label' => 'Home', 'url' => route('dashboard')],
            ['label' => 'State Analytics', 'url' => route('state-analytics.index')],
        ];

        $title = 'State Analytics';
        $pageTitle = 'State Analytics';

        return view('state-analytics.index', compact(
            'statesData',
            'summary',
            'fiscalYears',
            'quarters',
            'appliedFilters',
            'breadcrumbs',
            'title',
            'pageTitle',
            'defaultFiscalYear',
            'defaultQuarter'
        ));
    }

    public function export(Request $request)
    {
        $fiscalYearsConfig = (array) config('analytics.fiscal_years', []);
        $quartersConfig = (array) config('analytics.quarters', []);

        $defaultFiscalYear = array_key_first($fiscalYearsConfig) ?? 'FY2324';
        $defaultQuarter = array_key_first($quartersConfig) ?? 'ALL';

        $fiscalYear = (string) $request->get('fiscal_year', $defaultFiscalYear);
        $quarter = (string) $request->get('quarter', $defaultQuarter);

        if (! array_key_exists($fiscalYear, $fiscalYearsConfig)) {
            $fiscalYear = $defaultFiscalYear;
        }

        if (! array_key_exists($quarter, $quartersConfig)) {
            $quarter = $defaultQuarter;
        }

        $range = $this->service->buildDateRange($fiscalYear, $quarter);
        $statesData = $this->service->getMergedStateData($range['start'], $range['end']);

        $filename = 'state_analytics_' . $fiscalYear . '_' . $quarter . '_' . now()->format('Ymd') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $columns = [
            'State', 'Code', 'Region', 'Tier',
            'Total Startups', 'Active', 'High Growth',
            'Women-led', 'DPIIT', 'AI-enabled',
            'Avg Growth %', 'Employees', 'Jobs',
            'Funding (Cr)', 'Deals',
            'Avg Deal (USD)', 'Investors',
            'Dominant Sector', 'Sector Clusters',
        ];

        $callback = static function () use ($statesData, $columns): void {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($statesData as $s) {
                fputcsv($file, [
                    $s->state_name,
                    $s->state_code,
                    $s->region,
                    $s->tier,
                    $s->total_startups,
                    $s->active_startups,
                    $s->high_growth_startups,
                    $s->women_led_startups,
                    $s->dpiit_recognized,
                    $s->ai_enabled_startups,
                    $s->avg_growth_pct,
                    $s->total_employees,
                    $s->total_jobs,
                    $s->funding_inr_cr,
                    $s->total_deals,
                    $s->avg_deal_size_usd,
                    $s->unique_investors,
                    $s->dominant_sector,
                    $s->sector_cluster_count,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
