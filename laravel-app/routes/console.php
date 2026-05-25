<?php

use App\Models\ActivityLog;
use App\Models\Founder;
use App\Models\Startup;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('founders:backfill {--dry-run : Preview the founder-to-startup mapping without writing to the database}', function () {
    $dryRun = (bool) $this->option('dry-run');

    $startups = Startup::query()
        ->orderByDesc('id')
        ->get(['id', 'startup_name', 'founder_count']);

    $founders = Founder::query()
        ->whereNull('startup_id')
        ->orderBy('id')
        ->get(['id', 'full_name']);

    if ($startups->isEmpty()) {
        $this->warn('No startups were found.');

        return self::SUCCESS;
    }

    if ($founders->isEmpty()) {
        $this->warn('No unlinked founders were found.');

        return self::SUCCESS;
    }

    $assignments = [];
    $founderIndex = 0;

    foreach ($startups as $startup) {
        if ($founderIndex >= $founders->count()) {
            break;
        }

        $count = (int) ($startup->founder_count ?? 0);

        if ($count < 1) {
            continue;
        }

        $selected = $founders->slice($founderIndex, $count);

        if ($selected->isEmpty()) {
            break;
        }

        foreach ($selected as $founder) {
            $assignments[] = [
                'founder_id' => $founder->id,
                'founder_name' => $founder->full_name,
                'startup_id' => $startup->id,
                'startup_name' => $startup->startup_name,
            ];
        }

        $founderIndex += $selected->count();
    }

    if ($assignments === []) {
        $this->warn('No founder assignments were generated.');

        return self::SUCCESS;
    }

    $this->table(
        ['Founder ID', 'Founder', 'Startup ID', 'Startup'],
        array_map(static fn (array $row): array => [
            $row['founder_id'],
            $row['founder_name'],
            $row['startup_id'],
            $row['startup_name'],
        ], $assignments)
    );

    if ($dryRun) {
        $this->info('Dry-run complete. No database changes were made.');

        return self::SUCCESS;
    }

    DB::transaction(function () use ($assignments): void {
        foreach ($assignments as $assignment) {
            Founder::query()
                ->whereKey($assignment['founder_id'])
                ->update(['startup_id' => $assignment['startup_id']]);
        }
    });

    $this->info('Backfill complete. Assigned '.count($assignments).' founders to startups.');

    return self::SUCCESS;
})->purpose('Backfill founder records onto startups using startup founder counts');

Schedule::call(static function (): void {
    $days = (int) config('activity.prune_after_days', 90);

    ActivityLog::query()
        ->where('created_at', '<', now()->subDays($days))
        ->delete();
})->daily()->at('02:00');
