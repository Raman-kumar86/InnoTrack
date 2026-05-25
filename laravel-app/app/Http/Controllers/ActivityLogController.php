<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\LogsActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActivityLogController extends Controller
{
    use LogsActivity;

    public function index(Request $request): View
    {
        /** @var array<string,mixed> $filters */
        $filters = [
            'search' => $request->string('search')->toString() ?: null,
            'module' => $request->string('module')->toString() ?: null,
            'result' => $request->string('result')->toString() ?: null,
            'user_id' => $request->string('user_id')->toString() ?: null,
            'date_range' => $request->string('date_range')->toString() ?: 'today',
            'date_from' => $request->string('date_from')->toString() ?: null,
            'date_to' => $request->string('date_to')->toString() ?: null,
        ];

        $sort = $request->string('sort')->toString() ?: 'newest';
        $perPage = 10;

        $query = $this->filteredQuery($filters);

        $logs = (clone $query)
            ->with(['causer', 'targetUser'])
            ->when($sort === 'oldest', fn ($query) => $query->oldest('created_at'), fn ($query) => $query->latest('created_at'))
            ->paginate($perPage)
            ->withQueryString();

        $summaryQuery = $this->filteredQuery($filters);
        $totalToday = (clone $summaryQuery)->count();
        $successToday = (clone $summaryQuery)->where('result', 'Success')->count();
        $blockedToday = (clone $summaryQuery)->where('result', 'Blocked')->count();
        $failedToday = (clone $summaryQuery)->where('result', 'Failed')->count();

        $modules = config('activity.modules', []);
        $results = array_keys(config('activity.results', []));
        $users = User::query()
            ->whereIn('id', ActivityLog::query()->select('user_id')->whereNotNull('user_id')->distinct())
            ->orderBy('name')
            ->get(['id', 'name']);
        $dateRanges = config('activity.date_ranges', []);
        $perPageOptions = config('activity.per_page_options', [25, 50, 100, 250]);

        $activeFilterCount = collect($filters)
            ->except(['date_range'])
            ->filter(static fn ($value) => ! is_null($value) && $value !== '')
            ->count() + ($filters['date_range'] !== 'today' ? 1 : 0);

        $hasActiveFilters = $activeFilterCount > 0;

        return view('activity-logs.index', [
            'title' => 'Activity Logs',
            'pageTitle' => 'Activity Logs',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('dashboard')],
                ['label' => 'Activity Logs', 'url' => route('activity-logs.index')],
            ],
            'logs' => $logs,
            'filters' => $filters,
            'sort' => $sort,
            'perPage' => $perPage,
            'modules' => $modules,
            'results' => $results,
            'users' => $users,
            'dateRanges' => $dateRanges,
            'totalToday' => $totalToday,
            'successToday' => $successToday,
            'blockedToday' => $blockedToday,
            'failedToday' => $failedToday,
            'hasActiveFilters' => $hasActiveFilters,
            'activeFilterCount' => $activeFilterCount,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $filters = $this->filtersFromRequest($request);
        $logs = $this->filteredQuery($filters)->latest('created_at')->get();

        $filename = 'activity-log-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(static function () use ($logs): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Timestamp', 'User', 'Module', 'Action', 'Result', 'IP Address', 'Description']);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->created_at?->format('Y-m-d H:i:s'),
                    $log->user_name ?? 'System',
                    $log->module,
                    $log->action,
                    $log->result,
                    $log->ip_address,
                    $log->description,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function destroy(ActivityLog $activityLog): RedirectResponse
    {
        $module = $activityLog->module;
        $action = $activityLog->action;

        $activityLog->delete();

        $this->logActivity([
            'module' => 'System',
            'action' => 'Deleted activity log entry',
            'result' => 'Success',
            'description' => $module . ' / ' . $action . ' was deleted.',
            'icon' => 'trash',
            'is_system' => false,
        ]);

        return back()->with('success', 'Log entry deleted.');
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        $ids = collect($request->input('ids', []))
            ->filter(static fn ($value) => is_numeric($value))
            ->map(static fn ($value) => (int) $value)
            ->values();

        if ($ids->isEmpty()) {
            return response()->json(['message' => 'No log entries selected.'], 422);
        }

        $deleted = ActivityLog::query()->whereIn('id', $ids)->delete();

        $this->logActivity([
            'module' => 'System',
            'action' => 'Bulk deleted activity log entries',
            'result' => 'Success',
            'description' => $deleted . ' activity log entries were deleted.',
            'metadata' => ['deleted_ids' => $ids->all(), 'deleted_count' => $deleted],
            'icon' => 'trash',
            'is_system' => false,
        ]);

        return response()->json(['message' => $deleted . ' log entries deleted.']);
    }

    public function prune(): JsonResponse
    {
        $days = (int) config('activity.prune_after_days', 90);
        $deleted = ActivityLog::query()->where('created_at', '<', now()->subDays($days))->delete();

        $this->logActivity([
            'module' => 'System',
            'action' => 'Pruned old activity logs',
            'result' => 'Success',
            'description' => 'Deleted ' . $deleted . ' logs older than ' . $days . ' days.',
            'icon' => 'trash',
            'is_system' => true,
        ]);

        return response()->json(['deleted' => $deleted]);
    }

    private function filteredQuery(array $filters): Builder
    {
        return ActivityLog::query()
            ->with('user')
            ->search($filters['search'] ?? null)
            ->filterModule($filters['module'] ?? null)
            ->filterResult($filters['result'] ?? null)
            ->filterUser($filters['user_id'] ?? null)
            ->filterDateRange($filters['date_range'] ?? null, $filters['date_from'] ?? null, $filters['date_to'] ?? null);
    }

    private function filtersFromRequest(Request $request): array
    {
        return [
            'search' => $request->string('search')->toString() ?: null,
            'module' => $request->string('module')->toString() ?: null,
            'result' => $request->string('result')->toString() ?: null,
            'user_id' => $request->string('user_id')->toString() ?: null,
            'date_range' => $request->string('date_range')->toString() ?: 'today',
            'date_from' => $request->string('date_from')->toString() ?: null,
            'date_to' => $request->string('date_to')->toString() ?: null,
        ];
    }
}