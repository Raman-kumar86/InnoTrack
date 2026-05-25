<?php

namespace App\Providers;

use App\Models\Notification;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);

        Gate::define('manage-users', static fn (User $user): bool => $user->isSuperAdmin() || $user->isStateAnalyst());
        Gate::define('view-activity-logs', static fn (User $user): bool => $user->isSuperAdmin() || $user->isStateAnalyst());
        Gate::define('export-activity-logs', static fn (User $user): bool => $user->isSuperAdmin());

        View::composer('partials.navbar', function ($view): void {
            $user = auth()->user();

            if (! $user) {
                $view->with([
                    'navNotifications' => collect(),
                    'navUnreadNotificationCount' => 0,
                ]);

                return;
            }

            $navNotifications = Notification::query()
                ->with('startup')
                ->where('user_id', $user->id)
                ->latest('created_at')
                ->limit(5)
                ->get();

            $view->with([
                'navNotifications' => $navNotifications,
                'navUnreadNotificationCount' => Notification::query()
                    ->where('user_id', $user->id)
                    ->where('is_read', false)
                    ->count(),
            ]);
        });
    }
}
