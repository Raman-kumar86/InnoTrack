<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Startup;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim((string) $request->string('query')->toString());
        $user = $request->user();

        $destinations = $this->destinationPages($user)
            ->filter(static function (array $page) use ($query): bool {
                if ($query === '') {
                    return true;
                }

                return str_contains(mb_strtolower($page['title'] . ' ' . implode(' ', $page['keywords'])), mb_strtolower($query));
            })
            ->values();

        $startupResults = collect();
        $stateResults = collect();
        $userResults = collect();
        $activityResults = collect();

        if ($query !== '') {
            $startupResults = Startup::query()
                ->with(['sector', 'state'])
                ->filter(['search' => $query])
                ->sorted('newest')
                ->limit(6)
                ->get();

            $stateResults = State::query()
                ->where('state_name', 'like', '%' . $query . '%')
                ->orderBy('state_name')
                ->limit(6)
                ->get();

            if (Gate::allows('manage-users')) {
                $userResults = User::query()
                    ->where(function ($queryBuilder) use ($query): void {
                        $queryBuilder->where('name', 'like', '%' . $query . '%')
                            ->orWhere('email', 'like', '%' . $query . '%');
                    })
                    ->orderBy('name')
                    ->limit(6)
                    ->get();
            }

            if (Gate::allows('view-activity-logs')) {
                $activityResults = ActivityLog::query()
                    ->with(['causer', 'targetUser'])
                    ->search($query)
                    ->latest('created_at')
                    ->limit(6)
                    ->get();
            }
        }

        $totalResults = $startupResults->count() + $stateResults->count() + $userResults->count() + $activityResults->count();

        return view('search.index', [
            'title' => 'Search',
            'pageTitle' => 'Search',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('dashboard')],
                ['label' => 'Search', 'url' => route('search.index')],
            ],
            'query' => $query,
            'destinations' => $destinations,
            'startupResults' => $startupResults,
            'stateResults' => $stateResults,
            'userResults' => $userResults,
            'activityResults' => $activityResults,
            'totalResults' => $totalResults,
        ]);
    }

    private function destinationPages(?User $user): Collection
    {
        $pages = collect([
            [
                'title' => 'Dashboard',
                'subtitle' => 'Overview of the startup ecosystem',
                'route' => route('dashboard'),
                'keywords' => ['dashboard', 'overview', 'home'],
                'visible' => true,
            ],
            [
                'title' => 'Startup Registry',
                'subtitle' => 'Browse and filter startup records',
                'route' => route('startups.index'),
                'keywords' => ['startup', 'startups', 'registry'],
                'visible' => true,
            ],
            [
                'title' => 'State Analytics',
                'subtitle' => 'Review state-level startup activity',
                'route' => route('state-analytics.index'),
                'keywords' => ['state', 'states', 'analytics'],
                'visible' => true,
            ],
            [
                'title' => 'Reports',
                'subtitle' => 'Executive, funding, and state reports',
                'route' => route('reports.index'),
                'keywords' => ['report', 'reports', 'funding', 'executive'],
                'visible' => true,
            ],
            [
                'title' => 'Settings',
                'subtitle' => 'Manage your profile and preferences',
                'route' => route('settings.index'),
                'keywords' => ['settings', 'profile', 'preferences'],
                'visible' => true,
            ],
            [
                'title' => 'User Management',
                'subtitle' => 'Manage portal access and roles',
                'route' => route('users.index'),
                'keywords' => ['user', 'users', 'role', 'portal'],
                'visible' => (bool) $user && Gate::forUser($user)->allows('manage-users'),
            ],
            [
                'title' => 'Activity Logs',
                'subtitle' => 'View the audit trail and actions',
                'route' => route('activity-logs.index'),
                'keywords' => ['activity', 'log', 'logs', 'audit'],
                'visible' => (bool) $user && Gate::forUser($user)->allows('view-activity-logs'),
            ],
        ]);

        return $pages->filter(static fn (array $page): bool => $page['visible'])->values();
    }
}