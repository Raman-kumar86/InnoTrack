<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
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
    }
}
