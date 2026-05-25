<?php

namespace App\Http\Controllers;

use App\Services\ReportsService;
use App\Services\StateAnalyticsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Illuminate\View\View;

class ReportsController extends Controller
{
    public function __construct(
        protected ReportsService $service,
    ) {
    }

    public function index(Request $request): View
    {
        $fiscalYearsConfig = (array) config('reports.fiscal_years', []);
        $defaultFiscalYear = array_key_first($fiscalYearsConfig) ?? 'FY2324';

        $selectedFy = (string) $request->get('fiscal_year', $defaultFiscalYear);
        if (! array_key_exists($selectedFy, $fiscalYearsConfig)) {
            $selectedFy = $defaultFiscalYear;
        }

        $fyRange = $this->service->fiscalRange($selectedFy);
        $prevFy = $this->previousFiscalYearKey($selectedFy);
        $prevRange = $this->service->fiscalRange($prevFy);

        $summary = $this->service->getKpiSummary(
            $fyRange['start'],
            $fyRange['end'],
            $prevRange['start'],
            $prevRange['end'],
        );

        $yoy = $this->service->getYoyComparison($selectedFy, $prevFy);

        $fundingGrowth = $this->service->getFundingGrowth($fyRange['start'], $fyRange['end']);
        $grantApproval = $this->service->getGrantApprovalRate($fyRange['start'], $fyRange['end']);
        $monthlyDetail = $this->service->getMonthlyDetail($fyRange['start'], $fyRange['end']);

        $fiscalYears = collect($fiscalYearsConfig)
            ->map(fn ($value, $key) => ['value' => $key, 'label' => $value['label']])
            ->values();

        $exportFormats = config('reports.export_formats', []);
        $generatedBy = auth()->id();

        return view('reports.index', [
            'title' => 'Reports',
            'pageTitle' => 'Reports',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('dashboard')],
                ['label' => 'Reports', 'url' => route('reports.index')],
            ],
            'summary' => $summary,
            'yoy' => $yoy,
            'fundingGrowth' => $fundingGrowth,
            'grantApproval' => $grantApproval,
            'monthlyDetail' => $monthlyDetail,
            'fiscalYears' => $fiscalYears,
            'selectedFy' => $selectedFy,
            'selectedFyLabel' => $fyRange['label'],
            'exportFormats' => $exportFormats,
            'generatedBy' => $generatedBy,
        ]);
    }

    public function exportExecutive(Request $request)
    {
        $fyKeys = array_keys((array) config('reports.fiscal_years', []));
        $defaultFiscalYear = $fyKeys[0] ?? 'FY2324';
        $fy = (string) $request->get('fiscal_year', $defaultFiscalYear);
        if (! in_array($fy, $fyKeys, true)) {
            $fy = $defaultFiscalYear;
        }

        $range = $this->service->fiscalRange($fy);
        $prevFy = $this->previousFiscalYearKey($fy);
        $prev = $this->service->fiscalRange($prevFy);

        $summary = $this->service->getKpiSummary($range['start'], $range['end'], $prev['start'], $prev['end']);
        $yoy = $this->service->getYoyComparison($fy, $prevFy);
        $monthly = $this->service->getMonthlyDetail($range['start'], $range['end']);

        $pdf = Pdf::loadView('reports.pdf.executive', [
            'fy' => $fy,
            'range' => $range,
            'summary' => $summary,
            'yoy' => $yoy,
            'monthly' => $monthly,
            'generatedBy' => auth()->id(),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('executive-summary-' . $fy . '.pdf');
    }

    public function exportFunding(Request $request): SymfonyResponse
    {
        $fyKeys = array_keys((array) config('reports.fiscal_years', []));
        $defaultFiscalYear = $fyKeys[0] ?? 'FY2324';
        $fy = (string) $request->get('fiscal_year', $defaultFiscalYear);
        if (! in_array($fy, $fyKeys, true)) {
            $fy = $defaultFiscalYear;
        }

        $range = $this->service->fiscalRange($fy);
        $data = $this->service->getFundingGrowth($range['start'], $range['end']);

        $filename = 'funding-tracker-' . $fy . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ];

        $callback = static function () use ($data): void {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Month', 'Funding (USD)', 'Funding (Cr)', 'Cumulative (Cr)', 'Deal Count']);

            foreach ($data['months'] as $month) {
                fputcsv($file, [
                    $month['month_label'],
                    $month['funding_usd'],
                    $month['funding_cr'],
                    $month['cumulative_cr'],
                    $month['deal_count'],
                ]);
            }

            fclose($file);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }

    public function exportStates(Request $request)
    {
        $fyKeys = array_keys((array) config('reports.fiscal_years', []));
        $defaultFiscalYear = $fyKeys[0] ?? 'FY2324';
        $fy = (string) $request->get('fiscal_year', $defaultFiscalYear);
        if (! in_array($fy, $fyKeys, true)) {
            $fy = $defaultFiscalYear;
        }

        $range = $this->service->fiscalRange($fy);
        $stateService = app(StateAnalyticsService::class);
        $states = $stateService->getMergedStateData($range['start'], $range['end']);

        $pdf = Pdf::loadView('reports.pdf.states', [
            'states' => $states,
            'range' => $range,
            'fy' => $fy,
            'generatedBy' => auth()->id(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('state-analytics-' . $fy . '.pdf');
    }

    private function previousFiscalYearKey(string $selectedFy): string
    {
        $fyKeys = array_keys((array) config('reports.fiscal_years', []));
        $currentIndex = array_search($selectedFy, $fyKeys, true);

        if ($currentIndex === false) {
            return $fyKeys[0] ?? $selectedFy;
        }

        return $fyKeys[$currentIndex + 1] ?? $fyKeys[$currentIndex] ?? $selectedFy;
    }
}
