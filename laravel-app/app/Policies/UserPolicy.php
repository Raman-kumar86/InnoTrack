<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'state_analyst', 'reviewer'], true);
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, User $target): bool
    {
        return $user->isSuperAdmin() && $target->role !== 'super_admin';
    }

    public function delete(User $user, User $target): bool
    {
        if (! $user->isSuperAdmin()) {
            return false;
        }

        return $user->id !== $target->id;
    }

    public function block(User $user, User $target): bool
    {
        if ($user->isSuperAdmin()) {
            return $user->id !== $target->id;
        }

        if ($user->isStateAnalyst()) {
            return $target->isReviewer();
        }

        return false;
    }

    public function unblock(User $user, User $target): bool
    {
        return $this->block($user, $target);
    }

    public function promote(User $user, User $target): bool
    {
        if (! $user->isSuperAdmin()) {
            return false;
        }

        // Super admin may promote reviewer -> state_analyst, and state_analyst -> super_admin
        return $target->isReviewer() || $target->isStateAnalyst();
    }

    public function demote(User $user, User $target): bool
    {
        return $user->isSuperAdmin() && $target->isStateAnalyst();
    }

    public function viewLogs(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isStateAnalyst();
    }

    public function exportLogs(User $user): bool
    {
        return $user->isSuperAdmin();
    }
}
