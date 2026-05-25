<?php

namespace App\Http\Controllers;

use App\Models\Sector;
use App\Models\Startup;
use App\Models\State;
use App\Models\Founder;
use Illuminate\Support\Collection;
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
        $sectors = Sector::query()->orderBy('sector_name')->get(['id', 'sector_name']);
        $states = State::query()->orderBy('state_name')->get(['id', 'state_name']);

        // Ensure related data is loaded so the edit form is prefilled reliably
        $startup->load([
            'founders' => fn ($q) => $q->orderBy('full_name'),
            'sector',
            'state',
        ]);

        $fundingStages = Startup::query()
            ->select('funding_stage')
            ->whereNotNull('funding_stage')
            ->where('funding_stage', '!=', '')
            ->distinct()
            ->orderBy('funding_stage')
            ->pluck('funding_stage');

        return view('startups.edit', compact('startup', 'sectors', 'states', 'fundingStages'));
    }

    public function update(Request $request, Startup $startup)
    {
        $previousName = $startup->startup_name;
        $data = $request->validate([
            'startup_name' => ['required', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:255'],
            'sector_id' => ['nullable', 'exists:sectors,id'],
            'state_id' => ['nullable', 'exists:states,id'],
            'funding_stage' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'dpiit_recognized' => ['nullable', 'in:0,1'],
            'public_listing' => ['nullable', 'in:0,1'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'founded_year' => ['nullable', 'integer', 'min:1900', 'max:' . now()->year],
            'founder_count' => ['nullable', 'integer', 'min:0'],
            'employee_count' => ['nullable', 'integer', 'min:0'],
            'total_funding_usd' => ['nullable', 'numeric'],
            'valuation_usd' => ['nullable', 'numeric'],
            'annual_revenue_inr' => ['nullable', 'numeric'],
            'jobs_created' => ['nullable', 'integer', 'min:0'],
            'patents_filed' => ['nullable', 'integer', 'min:0'],
            'city' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:50'],
            'women_led' => ['nullable', 'in:0,1'],
            'ai_enabled' => ['nullable', 'in:0,1'],
            'sustainability_focus' => ['nullable', 'in:0,1'],
            'export_business' => ['nullable', 'in:0,1'],
            'founders' => ['nullable', 'array'],
            'founders.*.id' => ['nullable', 'integer', 'exists:founders,id'],
            'founders.*.full_name' => ['nullable', 'string', 'max:255'],
            'founders.*.email' => ['nullable', 'email', 'max:255'],
            'founders.*._destroy' => ['nullable', 'in:0,1'],
        ]);

        $startup->fill([
            'startup_name' => $data['startup_name'],
            'website' => $data['website'] ?? null,
            'registration_number' => $data['registration_number'] ?? null,
            'sector_id' => $data['sector_id'] ?? null,
            'state_id' => $data['state_id'] ?? null,
            'funding_stage' => $data['funding_stage'] ?? null,
            'description' => $data['description'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'linkedin_url' => $data['linkedin_url'] ?? null,
            'founded_year' => $data['founded_year'] ?? null,
            'founder_count' => $data['founder_count'] ?? null,
            'employee_count' => $data['employee_count'] ?? null,
            'total_funding_usd' => $data['total_funding_usd'] ?? null,
            'valuation_usd' => $data['valuation_usd'] ?? null,
            'annual_revenue_inr' => $data['annual_revenue_inr'] ?? null,
            'jobs_created' => $data['jobs_created'] ?? null,
            'patents_filed' => $data['patents_filed'] ?? null,
            'city' => $data['city'] ?? null,
            'status' => $data['status'] ?? null,
        ]);

        $startup->dpiit_recognized = isset($data['dpiit_recognized']) && $data['dpiit_recognized'] === '1';
        $startup->women_led = isset($data['women_led']) && $data['women_led'] === '1';
        $startup->ai_enabled = isset($data['ai_enabled']) && $data['ai_enabled'] === '1';
        $startup->sustainability_focus = isset($data['sustainability_focus']) && $data['sustainability_focus'] === '1';
        $startup->export_business = isset($data['export_business']) && $data['export_business'] === '1';

        if (array_key_exists('public_listing', $data)) {
            $startup->public_listing = $data['public_listing'] === '1';
        }

        $startup->save();

        // Process founders CRUD: create, update, mark for delete
        $foundersInput = $request->input('founders', []);
        $keep = [];

        foreach ($foundersInput as $f) {
            $f = array_map(function ($v) {
                return $v === '' ? null : $v;
            }, $f);

            $destroy = isset($f['_destroy']) && $f['_destroy'] === '1';

            if (! empty($f['id'])) {
                $founder = Founder::find($f['id']);
                if (! $founder || $founder->startup_id !== $startup->id) {
                    // skip any founder not belonging to this startup
                    continue;
                }

                if ($destroy) {
                    $founder->delete();
                    continue;
                }

                $founder->fill([
                    'full_name' => $f['full_name'] ?? $founder->full_name,
                    'email' => $f['email'] ?? $founder->email,
                ]);

                $founder->save();
                $keep[] = $founder->id;
                continue;
            }

            if ($destroy) {
                continue;
            }

            // create new founder
            if (! empty($f['full_name']) || ! empty($f['email'])) {
                $new = Founder::create([
                    'startup_id' => $startup->id,
                    'full_name' => $f['full_name'] ?? null,
                    'email' => $f['email'] ?? null,
                ]);

                $keep[] = $new->id;
            }
        }

        // remove any founders that were not included in the submitted list
        $existing = $startup->founders()->pluck('id')->all();
        $toDelete = array_diff($existing, $keep);
        if (! empty($toDelete)) {
            Founder::query()->whereIn('id', $toDelete)->delete();
        }

        $this->logActivity([
            'module' => 'Startups',
            'action' => 'Updated startup',
            'result' => 'Success',
            'loggable_type' => Startup::class,
            'loggable_id' => $startup->id,
            'description' => $startup->startup_name . ' was updated successfully.',
            'metadata' => [
                'startup_id' => $startup->id,
                'previous_name' => $previousName,
                'current_name' => $startup->startup_name,
            ],
            'icon' => 'edit',
        ]);

        return redirect()->route('startups.show', ['startup' => $startup->id])->with('success', 'Startup updated successfully.');
    }

    public function destroy(Startup $startup)
    {
        $startupName = $startup->startup_name;
        $startup->delete();

        $this->logActivity([
            'module' => 'Startups',
            'action' => 'Deleted startup',
            'result' => 'Success',
            'loggable_type' => Startup::class,
            'loggable_id' => $startup->id,
            'description' => $startupName . ' was deleted.',
            'icon' => 'trash',
        ]);

        return redirect()->route('startups.index')->with('success', 'Startup deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = array_values($request->input('ids', []));

        if ($ids === []) {
            return response()->json(['error' => 'No startups selected.'], 422);
        }

        $deleted = Startup::query()->whereIn('id', $ids)->delete();

        $this->logActivity([
            'module' => 'Startups',
            'action' => 'Bulk deleted startups',
            'result' => 'Success',
            'description' => $deleted . ' startups were deleted.',
            'metadata' => ['deleted_ids' => $ids, 'deleted_count' => $deleted],
            'icon' => 'trash',
        ]);

        return response()->json(['message' => $deleted . ' startups deleted.']);
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

        $this->logActivity([
            'module' => 'Startups',
            'action' => 'Exported startups',
            'result' => 'Success',
            'description' => 'Exported filtered startup list.',
            'metadata' => ['filters' => $filters, 'sort' => $sort, 'count' => $startups->count()],
            'icon' => 'file-text',
        ]);

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

        $this->logActivity([
            'module' => 'Startups',
            'action' => 'Exported selected startups',
            'result' => 'Success',
            'description' => 'Exported ' . $startups->count() . ' selected startups.',
            'metadata' => ['selected_ids' => $ids, 'count' => $startups->count()],
            'icon' => 'file-text',
        ]);

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

        $this->logActivity([
            'module' => 'Startups',
            'action' => 'Bulk updated startup status',
            'result' => 'Success',
            'description' => count($ids) . ' startups updated to ' . $status . '.',
            'metadata' => ['selected_ids' => $ids, 'status' => $status],
            'icon' => 'check-circle',
        ]);

        return response()->json(['message' => count($ids).' startups updated to '.$status]);
    }

    private function streamStartupsCsv(Collection|array $startups, string $filename)
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