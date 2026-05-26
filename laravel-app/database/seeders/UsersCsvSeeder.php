<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\LazyCollection;
use App\Models\User;

class UsersCsvSeeder extends Seeder
{
    /**
     * Run the CSV import for users.
     */
    public function run(): void
    {
        $path = storage_path('imports/users.csv');

        if (! file_exists($path)) {
            $this->command->info('users.csv not found at ' . $path);
            return;
        }

        LazyCollection::make(function () use ($path) {
            $handle = fopen($path, 'r');

            try {
                while (($line = fgetcsv($handle)) !== false) {
                    yield $line;
                }
            } finally {
                fclose($handle);
            }
        })
        ->filter()
        ->skip(1) // skip header row
        ->chunk(500)
        ->each(function ($rows) {
            $inserts = [];

            foreach ($rows as $columns) {
                $columns = array_map('trim', $columns);

                $name = $columns[0] ?? null;
                $email = $columns[1] ?? null;
                $password = $columns[2] ?? null;
                $role = $columns[3] ?? 'user';
                $createdAt = $columns[4] ?? now();

                if (! $email) {
                    continue;
                }

                $inserts[] = [
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($password ?? 'secret'),
                    'role' => $role,
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'created_at' => $createdAt,
                    'updated_at' => now(),
                ];
            }

            if (! empty($inserts)) {
                // Idempotent: upsert by email
                User::upsert($inserts, ['email'], ['name', 'password', 'role', 'is_active', 'email_verified_at', 'updated_at']);
            }
        });

        $this->command->info('UsersCsvSeeder: import finished.');
    }
}
