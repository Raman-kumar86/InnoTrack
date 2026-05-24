<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Contracts\Filesystem\Filesystem;
use Throwable;

class ImportCsvData extends Command
{
    protected $signature = 'import:csv {--dry-run : Preview the import without writing to the database}';

    protected $description = 'Import CSV files from storage/app/imports into mapped database tables.';

    /**
     * File name to table name map.
     *
     * @var array<string, string>
     */
    private array $tableMap = [
        'founders.csv' => 'founders',
        'incubators.csv' => 'incubators',
        'notifications.csv' => 'notifications',
        'users.csv' => 'users',
        'startups.csv' => 'startups',
        'funding_rounds.csv' => 'funding_rounds',
        'investors.csv' => 'investors',
        'sectors.csv' => 'sectors',
        'states.csv' => 'states',
        'startup_documents.csv' => 'startup_documents',
        'startup_incubator_map.csv' => 'startup_incubator_map',
        'startup_investors.csv' => 'startup_investors',
        'startup_metrics.csv' => 'startup_metrics',
        'startup_tags.csv' => 'startup_tags',
        'startup_updates.csv' => 'startup_updates',
    ];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $this->info($dryRun ? 'Starting CSV import in dry-run mode...' : 'Starting CSV import...');

        $importDisk = $this->importsDisk();
        $files = $importDisk->files('');

        if ($files === []) {
            $this->warn('No CSV files found in storage/app/imports.');

            return self::SUCCESS;
        }

        foreach ($files as $filePath) {
            $fileName = basename($filePath);

            if (strtolower(pathinfo($fileName, PATHINFO_EXTENSION)) !== 'csv') {
                $this->warn("Skipping non-CSV file: {$fileName}");

                Log::warning('CSV import skipped non-CSV file.', [
                    'file' => $fileName,
                    'path' => $filePath,
                ]);

                continue;
            }

            $tableName = $this->tableMap[$fileName] ?? null;

            if ($tableName === null) {
                $this->warn("Skipping unknown file: {$fileName}");

                Log::warning('CSV import skipped unknown file.', [
                    'file' => $fileName,
                    'path' => $filePath,
                ]);

                continue;
            }

            $this->line("Importing {$fileName} -> {$tableName}");

            if (! Schema::hasTable($tableName)) {
                $this->warn("Skipping {$fileName}: target table {$tableName} does not exist.");

                Log::warning('CSV import skipped missing table.', [
                    'file' => $fileName,
                    'table' => $tableName,
                ]);

                continue;
            }

            try {
                $insertedRows = $this->importFile($importDisk, $filePath, $tableName, $dryRun);

                $message = $dryRun
                    ? "Dry-run: would import {$insertedRows} rows from {$fileName} into {$tableName}"
                    : "Imported {$insertedRows} rows from {$fileName} into {$tableName}";

                $this->info($message);

                Log::info('CSV file imported successfully.', [
                    'file' => $fileName,
                    'table' => $tableName,
                    'rows_inserted' => $insertedRows,
                ]);
            } catch (Throwable $e) {
                $this->error("Failed to import {$fileName}: {$e->getMessage()}");

                Log::error('CSV import failed.', [
                    'file' => $fileName,
                    'table' => $tableName,
                    'error' => $e->getMessage(),
                ]);

                continue;
            }
        }

        $this->info($dryRun ? 'CSV dry-run completed successfully.' : 'CSV import completed successfully.');

        return self::SUCCESS;
    }

    /**
     * Import a single CSV file into the given table.
     */
    private function importFile(FilesystemAdapter|Filesystem $importDisk, string $filePath, string $tableName, bool $dryRun = false): int
    {
        $stream = $importDisk->readStream($filePath);

        if ($stream === false) {
            throw new \RuntimeException("Unable to open CSV file: {$filePath}");
        }

        $batch = [];
        $insertedCount = 0;
        $headers = null;
        $tableColumns = Schema::getColumnListing($tableName);
        $tableColumnLookup = array_flip($tableColumns);
        $upsertColumns = $this->resolveUpsertColumns($tableName, $tableColumns);

        try {
            while (($row = fgetcsv($stream)) !== false) {
                if ($headers === null) {
                    $headers = $this->normalizeHeaders($row);
                    continue;
                }

                if ($this->isEmptyRow($row)) {
                    continue;
                }

                $record = $this->combineRowWithHeaders($headers, $row, $tableColumnLookup);

                if ($record === []) {
                    continue;
                }

                if ($tableName === 'users' && ! isset($record['password'])) {
                    $record['password'] = Hash::make('Password@123');
                }

                $batch[] = $record;

                if (count($batch) >= 500) {
                    $insertedCount += $this->flushBatch($tableName, $batch, $upsertColumns, $dryRun);
                    $batch = [];
                }
            }

            if ($batch !== []) {
                $insertedCount += $this->flushBatch($tableName, $batch, $upsertColumns, $dryRun);
            }
        } finally {
            fclose($stream);
        }

        return $insertedCount;
    }

    /**
     * @param array<int, string|null> $row
     * @return array<int, string>
     */
    private function normalizeHeaders(array $row): array
    {
        return array_map(static function ($value): string {
            $header = trim((string) $value);
            $header = preg_replace('/^\xEF\xBB\xBF/', '', $header) ?? $header;

            return Str::snake(mb_strtolower($header));
        }, $row);
    }

    /**
     * @param array<int, string> $headers
     * @param array<int, string|null> $row
     * @param array<string, int> $tableColumnLookup
     * @return array<string, string|null>
     */
    private function combineRowWithHeaders(array $headers, array $row, array $tableColumnLookup): array
    {
        $record = [];

        foreach ($headers as $index => $header) {
            if ($header === '' || ! isset($tableColumnLookup[$header])) {
                continue;
            }

            $value = isset($row[$index]) ? trim((string) $row[$index]) : null;
            $record[$header] = $value === '' ? null : $value;
        }

        return $record;
    }

    /**
     * Persist a batch using insert or upsert.
     *
     * @param array<int, array<string, string|null>> $batch
     * @param array<int, string> $upsertColumns
     */
    private function flushBatch(string $tableName, array $batch, array $upsertColumns, bool $dryRun): int
    {
        if ($dryRun) {
            return count($batch);
        }

        if ($upsertColumns !== []) {
            DB::table($tableName)->upsert($batch, $upsertColumns);

            return count($batch);
        }

        DB::table($tableName)->insert($batch);

        return count($batch);
    }

    /**
     * Resolve upsert columns from the table's unique or primary indexes.
     *
     * @param array<int, string> $tableColumns
     * @return array<int, string>
     */
    private function resolveUpsertColumns(string $tableName, array $tableColumns): array
    {
        $indexes = DB::select('SHOW INDEX FROM `' . $tableName . '` WHERE Non_unique = 0');

        $grouped = [];

        foreach ($indexes as $index) {
            $keyName = (string) ($index->Key_name ?? '');
            $columnName = (string) ($index->Column_name ?? '');
            $seq = (int) ($index->Seq_in_index ?? 0);

            if ($keyName === '' || $columnName === '') {
                continue;
            }

            $grouped[$keyName][$seq] = $columnName;
        }

        if (isset($grouped['PRIMARY'])) {
            ksort($grouped['PRIMARY']);

            $columns = array_values(array_intersect(array_values($grouped['PRIMARY']), $tableColumns));

            if ($columns !== []) {
                return $columns;
            }
        }

        foreach ($grouped as $keyName => $columnsBySeq) {
            if ($keyName === 'PRIMARY') {
                continue;
            }

            ksort($columnsBySeq);

            $columns = array_values(array_intersect(array_values($columnsBySeq), $tableColumns));

            if ($columns !== []) {
                return $columns;
            }
        }

        if (in_array('id', $tableColumns, true)) {
            return ['id'];
        }

        return [];
    }

    /**
     * Build a Storage disk rooted at storage/app/imports.
     */
    private function importsDisk(): FilesystemAdapter|Filesystem
    {
        return Storage::build([
            'driver' => 'local',
            'root' => storage_path('app/imports'),
        ]);
    }

    /**
     * @param array<int, string|null> $row
     */
    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }
}
