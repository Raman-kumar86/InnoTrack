<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
  public function index(): View
  {
    $totalStartups = DB::table('startups')->count();
    $activeStartups = DB::table('startups')->where('status', 'Active')->count();
    $fundingStages = DB::table('startups')->whereNotNull('funding_stage')->distinct()->count('funding_stage');
    $recognizedStartups = DB::table('startups')->where('dpiit_recognized', 'Yes')->count();
    $jobsCreated = (int) DB::table('startups')->sum('jobs_created');

    [$sparkLabels, $monthKeys] = $this->buildMonthSeries(6);
    $monthStart = Carbon::now()->startOfMonth()->subMonths(count($monthKeys) - 1)->startOfMonth();
    $monthEnd = Carbon::now()->endOfMonth();

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

    $fundingByMonth = DB::table('startups')
      ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, COALESCE(SUM(total_funding_usd), 0) as total")
      ->whereNotNull('created_at')
      ->whereBetween('created_at', [$monthStart, $monthEnd])
      ->groupBy('month_key')
      ->pluck('total', 'month_key')
      ->all();

    $jobsByMonth = DB::table('startups')
      ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, COALESCE(SUM(jobs_created), 0) as total")
      ->whereNotNull('created_at')
      ->whereBetween('created_at', [$monthStart, $monthEnd])
      ->groupBy('month_key')
      ->pluck('total', 'month_key')
      ->all();

    $totalSpark = $this->seriesFromMonthMap($monthKeys, $registrationsByMonth, 'int');
    $recognizedSpark = $this->seriesFromMonthMap($monthKeys, $recognizedByMonth, 'int');
    $fundingSpark = $this->seriesFromMonthMap($monthKeys, $fundingByMonth, 'float');
    $jobsSpark = $this->seriesFromMonthMap($monthKeys, $jobsByMonth, 'int');

    $startupSectorRows = DB::table('sectors')
      ->leftJoin('startups', 'sectors.id', '=', 'startups.sector_id')
      ->select(
        'sectors.id',
        'sectors.sector_name',
        DB::raw('COUNT(CASE WHEN startups.status = "Active" THEN startups.id END) as startup_count')
      )
      ->groupBy('sectors.id', 'sectors.sector_name')
      ->orderBy('sectors.sector_name')
      ->get();

    $sectorPalette = [
      '#4f46e5',
      '#0ea5e9',
      '#10b981',
      '#f59e0b',
      '#64748b',
      '#8b5cf6',
      '#14b8a6',
      '#f43f5e',
      '#22c55e',
      '#eab308',
      '#6366f1',
      '#06b6d4',
      '#84cc16',
      '#fb7185',
      '#a855f7',
      '#0f766e',
      '#ca8a04',
      '#1d4ed8',
      '#ef4444',
      '#7c3aed',
    ];
    $sectorTilePalette = [
      'bg-indigo-600',
      'bg-sky-500',
      'bg-emerald-500',
      'bg-amber-500',
      'bg-slate-500',
      'bg-violet-500',
      'bg-teal-500',
      'bg-rose-500',
      'bg-lime-500',
      'bg-yellow-500',
      'bg-indigo-500',
      'bg-cyan-500',
      'bg-green-500',
      'bg-pink-500',
      'bg-fuchsia-500',
      'bg-cyan-700',
      'bg-orange-500',
      'bg-blue-700',
      'bg-red-500',
      'bg-purple-600',
    ];
    $sectorModalRows = $startupSectorRows->values()->map(function ($row, int $index) use ($activeStartups, $sectorPalette, $sectorTilePalette): array {
      $count = (int) $row->startup_count;

      return [
        'name' => $row->sector_name,
        'count' => $count,
        'share' => $activeStartups > 0 ? round(($count / $activeStartups) * 100, 1) : 0,
        'color' => $sectorPalette[$index % count($sectorPalette)],
        'tileClass' => $sectorTilePalette[$index % count($sectorTilePalette)],
      ];
    })->values()->all();

    $sectorLabels = array_column($sectorModalRows, 'name');
    $sectorValues = array_map(static fn(array $row): int => (int) $row['count'], $sectorModalRows);
    $sectorColors = array_column($sectorModalRows, 'color');
    $sectorDatasets = [[
      'data' => $sectorValues,
      'backgroundColor' => $sectorColors,
    ]];

    $sectorBubbleDatasets = [[
      'label' => 'Active startups',
      'data' => array_map(static function (array $row, int $index): array {
        return [
          'x' => $index + 1,
          'y' => (float) $row['share'],
          'r' => max(6, (int) ceil($row['count'] / 8)),
        ];
      }, $sectorModalRows, array_keys($sectorModalRows)),
      'backgroundColor' => $sectorColors,
    ]];

    $sectorCards = array_map(static function (array $row): array {
      return [
        'name' => $row['name'],
        'value' => $row['share'] . '%',
        'subtitle' => $row['count'] . ' active startups',
      ];
    }, $sectorModalRows);

    $sectorPieRows = collect($sectorModalRows)
      ->sortByDesc('count')
      ->values();

    $pieTopRows = $sectorPieRows->take(4)->values();
    $pieOthersCount = max(0, $activeStartups - (int) $pieTopRows->sum('count'));

    $pieSectorRows = $pieTopRows->map(fn(array $row): array => [
      'name' => $row['name'],
      'count' => $row['count'],
      'share' => $row['share'],
      'color' => $row['color'],
    ])->push([
      'name' => 'Others',
      'count' => $pieOthersCount,
      'share' => $activeStartups > 0 ? round(($pieOthersCount / $activeStartups) * 100, 1) : 0,
      'color' => '#94a3b8',
    ])->values()->all();

    $stateRows = DB::table('states')
      ->leftJoin('startups', 'states.id', '=', 'startups.state_id')
      ->select('states.state_name as name', DB::raw('COUNT(startups.id) as startups'))
      ->groupBy('states.id', 'states.state_name')
      ->orderByDesc('startups')
      ->limit(6)
      ->get();

    $stateLabels = $stateRows->pluck('name')->values()->all();
    $stateFunding = $stateRows->pluck('startups')->map(fn($value) => (int) $value)->values()->all();

    $latestStartups = DB::table('startups')
      ->leftJoin('sectors', 'startups.sector_id', '=', 'sectors.id')
      ->leftJoin('states', 'startups.state_id', '=', 'states.id')
      ->select(
        'startups.startup_name as name',
        'sectors.sector_name as sector',
        'states.state_name as state',
        'startups.funding_stage as stage',
        'startups.status',
        'startups.dpiit_recognized as dpiit',
        'startups.created_at as date'
      )
      ->orderByDesc('startups.created_at')
      ->limit(4)
      ->get()
      ->map(function ($startup): array {
        return [
          'name' => $startup->name,
          'sector' => $startup->sector ?? 'Unknown',
          'state' => $startup->state ?? 'Unknown',
          'stage' => $startup->stage ?? 'Unknown',
          'status' => $startup->status ?? 'Unknown',
          'dpiit' => $startup->dpiit ?? 'No',
          'date' => $startup->date ? Carbon::parse($startup->date)->format('d M Y') : '-',
        ];
      })
      ->all();

    $topStates = $stateRows->take(4)->map(function ($state): array {
      return [
        'name' => $state->name,
        'startups' => (int) $state->startups,
        'growth' => '+' . number_format(min(15.0, max(1.0, (float) $state->startups / 400)), 1) . '%',
      ];
    })->values()->all();

    $notifications = DB::table('notifications')
      ->leftJoin('startups', 'notifications.startup_id', '=', 'startups.id')
      ->select('notifications.title', 'notifications.message', 'notifications.priority', 'notifications.notification_type', 'notifications.created_at', 'notifications.is_read', 'startups.startup_name as startup_name')
      ->orderByDesc('notifications.created_at')
      ->limit(4)
      ->get();

    $activities = $notifications->map(function ($notification): array {
      $priority = strtolower((string) ($notification->priority ?? 'neutral'));
      $tone = match ($priority) {
        'high' => 'warning',
        'medium' => 'info',
        'low' => 'success',
        default => 'neutral',
      };

      $icon = match ((string) ($notification->notification_type ?? '')) {
        'dpiit_approval' => 'shield-check',
        'funding_round' => 'funding',
        'incubator_assignment' => 'map',
        'export_report' => 'download',
        default => 'bell',
      };

      return [
        'title' => $notification->title ?: ($notification->startup_name ? $notification->startup_name : 'Activity update'),
        'meta' => $notification->created_at ? Carbon::parse($notification->created_at)->diffForHumans() : 'Recently',
        'tone' => $tone,
        'icon' => $icon,
      ];
    })->all();

    $fundingRaisedUsd = (float) DB::table('startups')->sum('total_funding_usd');
    $fundingRaisedCr = $fundingRaisedUsd > 0 ? round($fundingRaisedUsd / 10000000, 2) : 0;
    $fundingTrend = $this->trendFromSeries($fundingSpark);
    $startupTrend = $this->trendFromSeries($totalSpark);
    $recognizedTrend = $this->trendFromSeries($recognizedSpark);
    $jobsTrend = $this->trendFromSeries($jobsSpark);

    $kpiCards = [
      [
        'label' => 'National overview',
        'value' => number_format($totalStartups),
        'trend' => $startupTrend,
        'icon' => 'rocket',
        'description' => 'Total startup records currently stored in the database.',
      ],
      [
        'label' => 'Active startups',
        'value' => number_format($activeStartups),
        'trend' => $startupTrend,
        'icon' => 'shield-check',
        'description' => 'Startups marked Active in the registry.',
      ],
      [
        'label' => 'Funding stages',
        'value' => number_format($fundingStages),
        'trend' => $fundingTrend,
        'icon' => 'funding',
        'description' => 'Unique funding stages present in the database.',
      ],
      [
        'label' => 'DPIIT Recognised',
        'value' => number_format($recognizedStartups),
        'trend' => $jobsTrend,
        'icon' => 'users',
        'description' => 'Recognised startups available for national reporting.',
      ],
    ];

    $months = $sparkLabels;
    $registrations = $totalSpark;
    $sectors = array_map(static function ($label, $value, $color): array {
      return [
        'label' => $label,
        'value' => $value,
        'color' => $color,
      ];
    }, $sectorLabels, $sectorValues, $sectorColors);

    $fundingSeries = $fundingSpark;

    $heatmapRows = [
      ['state' => $stateLabels[0] ?? 'Karnataka', 'cells' => [18, 26, 32, 42, 50, 60]],
      ['state' => $stateLabels[1] ?? 'Maharashtra', 'cells' => [16, 22, 29, 35, 46, 55]],
      ['state' => $stateLabels[2] ?? 'Delhi', 'cells' => [12, 18, 24, 30, 38, 44]],
      ['state' => $stateLabels[3] ?? 'Telangana', 'cells' => [10, 14, 20, 26, 32, 40]],
    ];

    return view('dashboard', [
      'title' => 'Dashboard',
      'pageTitle' => 'Startup India Progress Dashboard',
      'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Dashboard', 'url' => route('dashboard')],
      ],
      'sparkLabels' => $sparkLabels,
      'totalSpark' => $totalSpark,
      'recognizedSpark' => $recognizedSpark,
      'fundingSpark' => $fundingSpark,
      'jobsSpark' => $jobsSpark,
      'months' => $months,
      'registrations' => $registrations,
      'sectors' => $sectors,
      'stateLabels' => $stateLabels,
      'stateFunding' => $stateFunding,
      'fundingSeries' => $fundingSeries,
      'activities' => $activities,
      'latestStartups' => $latestStartups,
      'topStates' => $topStates,
      'heatmapRows' => $heatmapRows,
      'sectorCards' => $sectorCards,
      'registrationDatasets' => [[
        'label' => 'Registrations',
        'data' => $registrations,
        'borderColor' => '#4f46e5',
        'backgroundColor' => 'rgba(79, 70, 229, 0.12)',
        'tension' => 0.42,
        'fill' => true,
      ]],
      'sectorLabels' => $sectorLabels,
      'sectorValues' => $sectorValues,
      'sectorColors' => $sectorColors,
      'sectorDatasets' => $sectorDatasets,
      'sectorModalRows' => $sectorModalRows,
      'pieSectorRows' => $pieSectorRows,
      'sectorTotalActive' => $activeStartups,
      'sectorBubbleDatasets' => $sectorBubbleDatasets,
      'kpiCards' => $kpiCards,
    ]);
  }

  public function sectorDistribution(): \Illuminate\Http\JsonResponse
  {
    $total = (int) DB::table('startups')
      ->where('status', 'Active')
      ->count();

    $sectors = DB::table('startups as s')
      ->join('sectors as sec', 's.sector_id', '=', 'sec.id')
      ->where('s.status', 'Active')
      ->groupBy('sec.id', 'sec.sector_name')
      ->orderByDesc('count')
      ->selectRaw('sec.id as sector_id, sec.sector_name, COUNT(*) as count')
      ->get();

    return response()->json([
      'total_active_startups' => $total,
      'sectors' => $sectors->map(fn ($sector) => [
        'sector_id' => $sector->sector_id,
        'sector_name' => $sector->sector_name,
        'count' => (int) $sector->count,
        'share' => $total > 0 ? round(((int) $sector->count / $total) * 100, 2) : 0,
      ])->values(),
    ]);
  }

  /**
   * @return array{0: array<int, string>, 1: array<int, string>}
   */
  private function buildMonthSeries(int $months): array
  {
    $labels = [];
    $keys = [];
    $cursor = Carbon::now()->startOfMonth()->subMonths(max(0, $months - 1));

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
