<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    protected function logActivity(array $data): void
    {
        ActivityLog::create([
            'user_id' => $data['user_id'] ?? auth()->id(),
            'user_name' => $data['user_name'] ?? auth()->user()?->name ?? 'System',
            'module' => $data['module'],
            'action' => $data['action'],
            'result' => $data['result'] ?? 'Success',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'loggable_type' => $data['loggable_type'] ?? null,
            'loggable_id' => $data['loggable_id'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'description' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? 'activity',
            'is_system' => $data['is_system'] ?? false,
        ]);
    }
}