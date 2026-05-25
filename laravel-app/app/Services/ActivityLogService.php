<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;

class ActivityLogService
{
    /**
     * Log an activity record.
     *
     * @param array<string,mixed> $data
     */
    public function log(array $data): ActivityLog
    {
        /** @var \App\Models\User|null $actor */
        $actor = null;
        if (isset($data['causer']) && $data['causer'] instanceof User) {
            $actor = $data['causer'];
        } else {
            $actor = auth()->user();
        }

        /** @var \App\Models\User|null $target */
        $target = null;
        if (isset($data['target_user']) && $data['target_user'] instanceof User) {
            $target = $data['target_user'];
        }

        return ActivityLog::create([
            'causer_id' => $data['causer_id'] ?? $actor?->id,
            'target_user_id' => $data['target_user_id'] ?? $target?->id,
            'action' => $data['action'],
            'description' => $data['description'] ?? null,
            'ip_address' => $data['ip_address'] ?? request()->ip(),
            'user_agent' => $data['user_agent'] ?? request()->userAgent(),

            // Backward-compatible columns used by existing dashboard pieces
            'user_id' => $data['causer_id'] ?? $actor?->id,
            'user_name' => $data['user_name'] ?? $actor?->name ?? 'System',
            'module' => $data['module'] ?? 'Users',
            'result' => $data['result'] ?? 'Success',
            'loggable_type' => $data['loggable_type'] ?? User::class,
            'loggable_id' => $data['loggable_id'] ?? ($target?->id ?? $actor?->id ?? 1),
            'metadata' => $data['metadata'] ?? null,
            'icon' => $data['icon'] ?? 'activity',
            'is_system' => $data['is_system'] ?? false,
        ]);
    }

    public function logUserAction(User $actor, User $target, string $action, string $description): ActivityLog
    {
        return $this->log([
            'causer' => $actor,
            'target_user' => $target,
            'action' => $action,
            'description' => $description,
            'module' => 'Users',
            'result' => 'Success',
            'icon' => 'activity',
        ]);
    }
}
