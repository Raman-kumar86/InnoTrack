<?php

namespace App\Traits;

use App\Services\ActivityLogService;

trait LogsActivity
{
    protected function logActivity(array $data): void
    {
        app(ActivityLogService::class)->log($data);
    }
}