<?php

namespace App\Http\Controllers;

use App\Models\Sector;
use App\Models\Startup;
use App\Models\State;
use Illuminate\Http\Request;

class StartupController extends Controller
{
    public function index(Request $request)
    {
        $sectors = Sector::query()->orderBy('sector_name')->get(['id', 'sector_name']);
        $states = State::query()->orderBy('state_name')->get(['id', 'state_name']);

        $fundingStages = Startup::query()
            ->select('funding_stage')
            ->whereNotNull('funding_stage')
            ->where('funding_stage', '!=', '')
            ->distinct()
            ->orderBy('funding_stage')
            ->pluck('funding_stage');

        $statuses = Startup::query()
            ->select('status')
            ->whereNotNull('status')
            ->where('status', '!=', '')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');

        $filters = $request->only([
            'search', 'sector_id', 'state_id',
            'funding_stage', 'status', 'dpiit_recognized',
            'women_led', 'ai_enabled',
            'year_from', 'year_to',
            'funding_min', 'funding_max',
        ]);

        $sort = $request->string('sort', 'newest')->toString();
        $perPage = (int) $request->integer('per_page', 25);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;

        $query = Startup::query()->with(['sector', 'state', 'tags'])->filter($filters);

        $startups = (clone $query)
            ->sorted($sort)
            ->paginate($perPage)
            ->withQueryString();

        $filteredTotal = (clone $query)->count();
        $activeCount = (clone $query)->where('status', 'Active')->count();
        $dpiitCount = (clone $query)->where('dpiit_recognized', true)->count();
        $womenLedCount = (clone $query)->where('women_led', true)->count();

        $hasActiveFilters = collect($filters)->filter(function ($value): bool {
            return ! is_null($value) && $value !== '' && $value !== [];
        })->isNotEmpty();

        return view('startups.index', compact(
            'startups', 'sectors', 'states',
            'fundingStages', 'statuses',
            'filters', 'sort', 'perPage',
            'filteredTotal', 'activeCount',
            'dpiitCount', 'womenLedCount',
            'hasActiveFilters',
        ));
    }

    public function create()
    {
        return view('startups.create');
    }

    public function show(Startup $startup)
    {
        $startup->load([
            'sector',
            'state',
            'founders' => fn ($query) => $query->orderBy('full_name'),
            'fundingRounds' => fn ($query) => $query->orderByDesc('funding_date')->orderByDesc('id'),
            'tags' => fn ($query) => $query->orderBy('tag'),
            'updates' => fn ($query) => $query->orderByDesc('update_date')->orderByDesc('created_at'),
        ]);

        return view('startups.show', compact('startup'));
    }

    public function edit(Startup $startup)
    {
        return view('startups.edit', compact('startup'));
    }

    public function destroy(Startup $startup)
    {
        $startup->delete();

        return redirect()->route('startups.index')->with('success', 'Startup deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = array_values($request->input('ids', []));

        if ($ids === []) {
            return response()->json(['error' => 'No startups selected.'], 422);
        }

        Startup::query()->whereIn('id', $ids)->delete();

        return response()->json(['message' => count($ids).' startups deleted.']);
    }

    public function export(Request $request)
    {
        $filters = $request->only([
            'search', 'sector_id', 'state_id',
            'funding_stage', 'status', 'dpiit_recognized',
            'women_led', 'ai_enabled',
            'year_from', 'year_to',
            'funding_min', 'funding_max',
        ]);

        $sort = $request->string('sort', 'newest')->toString();

        $startups = Startup::query()
            ->with(['sector', 'state', 'tags'])
            ->filter($filters)
            ->sorted($sort)
            ->get();

        return $this->streamStartupsCsv(
            $startups,
            'startups_export_'.now()->format('Ymd_His').'.csv',
        );
    }

    public function bulkExport(Request $request)
    {
        $ids = array_values($request->input('ids', []));

        if ($ids === []) {
            return response()->json(['error' => 'No startups selected.'], 422);
        }

        $startups = Startup::query()
            ->with(['sector', 'state', 'tags'])
            ->whereIn('id', $ids)
            ->get();

        return $this->streamStartupsCsv(
            $startups,
            'startups_selected_export_'.now()->format('Ymd_His').'.csv',
        );
    }

    public function bulkStatusUpdate(Request $request)
    {
        $ids = array_values($request->input('ids', []));
        $status = $request->input('status');

        $allowed = ['Active', 'Inactive', 'Acquired', 'Failed'];

        if ($ids === []) {
            return response()->json(['error' => 'No startups selected.'], 422);
        }

        if (! in_array($status, $allowed, true)) {
            return response()->json(['error' => 'Invalid status'], 422);
        }

        Startup::query()->whereIn('id', $ids)->update(['status' => $status]);

        return response()->json(['message' => count($ids).' startups updated to '.$status]);
    }

    private function streamStartupsCsv($startups, string $filename)
    {
        return response()->streamDownload(function () use ($startups): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID', 'Name', 'Sector', 'State', 'City',
                'Funding Stage', 'Status', 'DPIIT',
                'Founded Year', 'Total Funding (USD)',
                'Valuation (USD)', 'Revenue (INR)',
                'Employees', 'Growth %', 'Women Led',
                'AI Enabled', 'Jobs Created', 'Patents Filed',
                'Registration No', 'Website', 'Email',
                'Last Funding Date', 'Tags',
            ]);

            foreach ($startups as $startup) {
                fputcsv($handle, [
                    $startup->id,
                    $startup->startup_name,
                    $startup->sector?->sector_name ?? '',
                    $startup->state?->state_name ?? '',
                    $startup->city,
                    $startup->funding_stage,
                    $startup->status,
                    $startup->dpiit_recognized ? 'Yes' : 'No',
                    $startup->founded_year,
                    $startup->total_funding_usd,
                    $startup->valuation_usd,
                    $startup->annual_revenue_inr,
                    $startup->employee_count,
                    $startup->growth_percentage,
                    $startup->women_led ? 'Yes' : 'No',
                    $startup->ai_enabled ? 'Yes' : 'No',
                    $startup->jobs_created,
                    $startup->patents_filed,
                    $startup->registration_number,
                    $startup->website,
                    $startup->email,
                    $startup->last_funding_date?->format('d M Y'),
                    $startup->tags->pluck('tag')->filter()->implode(', '),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}