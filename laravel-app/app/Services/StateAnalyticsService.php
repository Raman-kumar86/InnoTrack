<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StateAnalyticsService
{
    public function buildDateRange(string $fiscalYear, string $quarter): array
    {
        $fiscalYears = (array) config('analytics.fiscal_years', []);
        $quarters = (array) config('analytics.quarters', []);

        $fy = $fiscalYears[$fiscalYear] ?? reset($fiscalYears);
        $q = $quarters[$quarter] ?? reset($quarters);

        $fyStart = Carbon::parse($fy['start']);
        $fyEnd = Carbon::parse($fy['end']);

        if ($quarter === 'ALL') {
            return [
                'start' => $fyStart,
                'end' => $fyEnd,
            ];
        }

        if ($quarter === 'Q4') {
            $start = Carbon::create($fyEnd->year, 1, 1);
            $end = Carbon::create($fyEnd->year, 3, 31);
        } else {
            $start = Carbon::create($fyStart->year, $q['month_start'], 1);
            $end = Carbon::create($fyStart->year, $q['month_end'], 1)->endOfMonth();
        }

        return [
            'start' => $start->max($fyStart),
            'end' => $end->min($fyEnd),
        ];
    }

    public function getStateActivity(Carbon $start, Carbon $end): Collection
    {
        $threshold = (int) config('analytics.high_growth_threshold', 50);

        return DB::table('states as st')
            ->leftJoin('startups as s', function ($join) use ($start, $end): void {
                $join->on('s.state_id', '=', 'st.id')
                    ->whereBetween('s.created_at', [$start, $end]);
            })
            ->select([
                'st.id as state_id',
                'st.state_name',
                'st.state_code',
                'st.region',
                'st.startup_hub',
                DB::raw('COUNT(s.id) as total_startups'),
                DB::raw("SUM(CASE WHEN s.status = 'Active' THEN 1 ELSE 0 END) as active_startups"),
                DB::raw("SUM(CASE WHEN s.growth_percentage > {$threshold} THEN 1 ELSE 0 END) as high_growth_startups"),
                DB::raw("SUM(CASE WHEN s.women_led IN ('Yes', '1', 1) THEN 1 ELSE 0 END) as women_led_startups"),
                DB::raw("SUM(CASE WHEN s.dpiit_recognized IN ('Yes', '1', 1) THEN 1 ELSE 0 END) as dpiit_recognized"),
                DB::raw('AVG(s.growth_percentage) as avg_growth_pct'),
                DB::raw('SUM(s.employee_count) as total_employees'),
                DB::raw('SUM(s.jobs_created) as total_jobs'),
                DB::raw("SUM(CASE WHEN s.ai_enabled IN ('Yes', '1', 1) THEN 1 ELSE 0 END) as ai_enabled_startups"),
            ])
            ->groupBy(
                'st.id',
                'st.state_name',
                'st.state_code',
                'st.region',
                'st.startup_hub'
            )
            ->orderByDesc('active_startups')
            ->get();
    }

    public function getStateFunding(Carbon $start, Carbon $end): Collection
    {
        return DB::table('funding_rounds as fr')
            ->join('startups as s', 'fr.startup_id', '=', 's.id')
            ->whereBetween('fr.funding_date', [$start, $end])
            ->select([
                's.state_id',
                DB::raw('SUM(fr.amount_raised_usd) as total_funding_usd'),
                DB::raw('COUNT(fr.id) as total_deals'),
                DB::raw('AVG(fr.amount_raised_usd) as avg_deal_size_usd'),
                DB::raw('MAX(fr.amount_raised_usd) as largest_deal_usd'),
                DB::raw('COUNT(DISTINCT fr.investor_id) as unique_investors'),
            ])
            ->groupBy('s.state_id')
            ->get()
            ->keyBy('state_id');
    }

    public function getStateSectors(Carbon $start, Carbon $end): array
    {
        $rows = DB::table('startups as s')
            ->join('sectors as sec', 's.sector_id', '=', 'sec.id')
            ->whereBetween('s.created_at', [$start, $end])
            ->select([
                's.state_id',
                'sec.sector_name',
                DB::raw('COUNT(*) as sector_count'),
            ])
            ->groupBy('s.state_id', 'sec.sector_name')
            ->orderBy('s.state_id')
            ->orderByDesc('sector_count')
            ->get();

        $byState = [];
        foreach ($rows as $row) {
            $sid = $row->state_id;
            if (! isset($byState[$sid])) {
                $byState[$sid] = [
                    'dominant_sector' => $row->sector_name,
                    'sector_cluster_count' => 0,
                    'sectors' => [],
                ];
            }

            $byState[$sid]['sector_cluster_count']++;
            $byState[$sid]['sectors'][] = [
                'name' => $row->sector_name,
                'count' => (int) $row->sector_count,
            ];
        }

        return $byState;
    }

    public function getMergedStateData(Carbon $start, Carbon $end): Collection
    {
        $activity = $this->getStateActivity($start, $end);
        $funding = $this->getStateFunding($start, $end);
        $sectors = $this->getStateSectors($start, $end);

        $usdToInr = (float) config('analytics.usd_to_inr', 83.5);
        $tier1 = (int) config('analytics.tier_thresholds.tier1', 25);
        $tier2 = (int) config('analytics.tier_thresholds.tier2', 15);

        return $activity->map(function ($state) use ($funding, $sectors, $usdToInr, $tier1, $tier2) {
            $sid = $state->state_id;
            $f = $funding->get($sid);
            $s = $sectors[$sid] ?? [];

            $fundingUsd = (float) ($f?->total_funding_usd ?? 0);
            $fundingCr = round($fundingUsd * $usdToInr / 10000000, 2);

            $active = (int) ($state->active_startups ?? 0);
            $tier = match (true) {
                $active >= $tier1 => 'Tier 1',
                $active >= $tier2 => 'Tier 2',
                default => 'Tier 3',
            };

            return (object) [
                'state_id' => (int) $sid,
                'state_name' => $state->state_name,
                'state_code' => $state->state_code,
                'region' => $state->region,
                'startup_hub' => $state->startup_hub,
                'total_startups' => (int) ($state->total_startups ?? 0),
                'active_startups' => $active,
                'high_growth_startups' => (int) ($state->high_growth_startups ?? 0),
                'women_led_startups' => (int) ($state->women_led_startups ?? 0),
                'dpiit_recognized' => (int) ($state->dpiit_recognized ?? 0),
                'ai_enabled_startups' => (int) ($state->ai_enabled_startups ?? 0),
                'avg_growth_pct' => round((float) ($state->avg_growth_pct ?? 0), 2),
                'total_employees' => (int) ($state->total_employees ?? 0),
                'total_jobs' => (int) ($state->total_jobs ?? 0),
                'total_funding_usd' => $fundingUsd,
                'funding_inr_cr' => $fundingCr,
                'total_deals' => (int) ($f?->total_deals ?? 0),
                'avg_deal_size_usd' => round((float) ($f?->avg_deal_size_usd ?? 0)),
                'largest_deal_usd' => (float) ($f?->largest_deal_usd ?? 0),
                'unique_investors' => (int) ($f?->unique_investors ?? 0),
                'dominant_sector' => $s['dominant_sector'] ?? '—',
                'sector_cluster_count' => (int) ($s['sector_cluster_count'] ?? 0),
                'sectors' => $s['sectors'] ?? [],
                'tier' => $tier,
            ];
        });
    }

    public function getSummary(Collection $current, Collection $previous): array
    {
        $activeCurrent = $current->where('active_startups', '>', 0)->count();
        $activePrev = $previous->where('active_startups', '>', 0)->count();
        $activeChange = $activePrev > 0 ? round((($activeCurrent - $activePrev) / $activePrev) * 100, 1) : 0;

        $highGrowthThreshold = (float) config('analytics.high_growth_threshold', 50);
        $highCurrent = $current->where('avg_growth_pct', '>', $highGrowthThreshold)->count();
        $highPrev = $previous->where('avg_growth_pct', '>', $highGrowthThreshold)->count();
        $highChange = $highPrev > 0 ? round((($highCurrent - $highPrev) / $highPrev) * 100, 1) : 0;

        $fundingCurrent = round((float) $current->sum('funding_inr_cr'), 2);
        $fundingPrev = round((float) $previous->sum('funding_inr_cr'), 2);
        $fundingChange = $fundingPrev > 0 ? round((($fundingCurrent - $fundingPrev) / $fundingPrev) * 100, 1) : 0;

        $sectorsCurrent = $current->pluck('dominant_sector')->filter(fn ($s) => $s !== '—')->unique()->count();
        $sectorsPrev = $previous->pluck('dominant_sector')->filter(fn ($s) => $s !== '—')->unique()->count();
        $sectorsChange = $sectorsPrev > 0 ? round((($sectorsCurrent - $sectorsPrev) / $sectorsPrev) * 100, 1) : 0;

        return [
            'active_states' => [
                'value' => $activeCurrent,
                'change' => abs($activeChange),
                'direction' => $activeChange >= 0 ? 'up' : 'down',
                'formatted' => ($activeChange >= 0 ? '+' : '-') . abs($activeChange) . '%',
            ],
            'high_growth_states' => [
                'value' => $highCurrent,
                'change' => abs($highChange),
                'direction' => $highChange >= 0 ? 'up' : 'down',
                'formatted' => ($highChange >= 0 ? '+' : '-') . abs($highChange) . '%',
            ],
            'funding_concentration' => [
                'value' => $fundingCurrent,
                'change' => abs($fundingChange),
                'direction' => $fundingChange >= 0 ? 'up' : 'down',
                'formatted' => ($fundingChange >= 0 ? '+' : '-') . abs($fundingChange) . '%',
            ],
            'sector_clusters' => [
                'value' => $sectorsCurrent,
                'change' => abs($sectorsChange),
                'direction' => $sectorsChange >= 0 ? 'up' : 'down',
                'formatted' => ($sectorsChange >= 0 ? '+' : '-') . abs($sectorsChange) . '%',
            ],
        ];
    }
}
