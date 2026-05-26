# InnoTrack — Developer Setup & CSV Seeding Guide

This repository contains the InnoTrack Laravel dashboard application (Laravel app located in `laravel-app/`).
This document covers everything a new developer needs to set up the project locally, configure the database, and import production-style datasets from CSV files for seeding.

Table of contents
- Overview
- Prerequisites
- Quick start (minimal steps)
- Detailed setup (clone, environment, dependencies)
- Database creation, migrations and seeding from CSV
- Example CSV seeder (sample code)
- Running the app and tests
- Troubleshooting and common issues
- Useful commands

Overview
--------

The application is a Laravel-based admin/dashboard. The Laravel application root is in the `laravel-app/` folder. The instructions below assume you will run commands from the repository root or change directory into `laravel-app/` when noted.

Prerequisites
-------------
- Git (to clone repository)
- PHP 8.1+ (or the PHP version required by composer.json — verify locally)
- Composer (for PHP dependencies)
- Node.js 18+ and npm/yarn (for frontend build: Vite + Tailwind)
- A relational database: MySQL, MariaDB, or PostgreSQL (instructions below assume MySQL/MariaDB)
- Optional: Docker + Docker Compose or Laravel Sail for containerized development

Quick start (minimal)
---------------------
From a fresh machine the shortest path to run the app locally:

1. Clone the repo and enter the project:

```bash
git clone <repo-url> innotrack
cd innotrack/laravel-app
```

2. Copy environment file and update DB settings:

```bash
cp .env.example .env
# Edit .env and set DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
```

3. Install backend and frontend dependencies, build assets:

```bash
composer install --optimize-autoloader --no-dev
npm ci
npm run build
```

4. Generate app key and run migrations:

```bash
php artisan key:generate
php artisan migrate
```

5. Seed demo data from CSV (see detailed section below):

```bash
# Example: php artisan db:seed --class=UsersCsvSeeder
```

6. Serve the app locally:

```bash
php artisan serve
# Visit http://127.0.0.1:8000
```

Detailed setup (step-by-step)
-----------------------------
1) Clone and checkout

```bash
git clone <repo-url> InnoTrack
cd InnoTrack/laravel-app
```

2) Environment

- Copy ` .env.example` to `.env`.
- Set `APP_URL`, `APP_ENV`, and database variables: `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
- If you plan to use mail/SMS/reporting services set those credentials here as well.

3) PHP dependencies

```bash
composer install
```

If composer runs out of memory on Linux/WSL: `php -d memory_limit=-1 /usr/local/bin/composer install` or increase memory limit.

4) Frontend

```bash
npm ci
npm run dev      # for development (hot reload)
npm run build    # for production
```

5) Application key & caches

```bash
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

6) Create the database and run migrations

- Create an empty database using your DB admin tool or CLI (example MySQL):

```sql
CREATE DATABASE innotrack CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Then run migrations:

```bash
php artisan migrate --seed
```

Note: `--seed` will run the seeders registered in `DatabaseSeeder`. If you're using CSV-based seeders, see the CSV seeding section and run the CSV seeder class explicitly.

Database & CSV seeding (full guide)
----------------------------------

This project supports seeding from CSV files (useful when you have exported datasets). Follow these steps to import CSV data safely.

1) CSV placement

- Place CSV files under `storage/imports/` (create the folder if missing). This keeps imports outside web root.

Example structure:

```
laravel-app/storage/imports/users.csv
laravel-app/storage/imports/startups.csv
```

2) CSV format

- Ensure the CSV header row contains field names that map to model attributes.
- Example `users.csv` header:

```
name,email,password,role,created_at
```

- Passwords in CSV should be plain text only if you will hash them during import—never commit real passwords.

3) Example seeder (safe, chunked, memory-friendly)

Create a seeder file at `database/seeders/UsersCsvSeeder.php` and adapt the column mapping to your CSV.

```php
<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\LazyCollection;
use App\Models\User;

class UsersCsvSeeder extends Seeder
{
	public function run(): void
	{
		$path = storage_path('imports/users.csv');

		if (! file_exists($path)) {
			$this->command->info('users.csv not found at ' . $path);
			return;
		}

		$header = null;

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
		->skip(1) // skip header if present
		->chunk(500)
		->each(function ($rows) use ($header) {
			$inserts = [];

			foreach ($rows as $columns) {
				[$name, $email, $password, $role, $createdAt] = $columns + array_fill(0, 5, null);

				$inserts[] = [
					'name' => $name,
					'email' => $email,
					'password' => Hash::make($password ?? 'secret'),
					'role' => $role ?? 'user',
					'created_at' => $createdAt ?? now(),
					'updated_at' => now(),
				];
			}

			// Use insert for performance; ensure model events or observers are not required
			User::insert($inserts);
		});
	}
}
```

Notes on the seeder above:
- `LazyCollection` and `chunk()` keep memory footprint small for large CSVs.
- Use `Hash::make()` to hash plaintext passwords during import.
- If you rely on model events (observers) for side-effects (e.g., profile creation), use model `create()` inside transactions but be aware of performance costs.

4) Run the CSV seeder

```bash
php artisan db:seed --class=Database\Seeders\UsersCsvSeeder
```

If you have many different CSV seeders, register them in `DatabaseSeeder.php` and call them in the desired order.

5) Idempotency and safety

- Design your CSV seeders to be idempotent: use `updateOrInsert()` or check by unique keys (email, slug) to avoid duplicate rows when running multiple times.
- For destructive actions (truncate tables) require an explicit `--force` flag or interactive confirmation.

Example of idempotent insert using `upsert`:

```php
User::upsert($rows, ['email'], ['name','password','role','updated_at']);
```

Example small CSV validator (optional)

Before seeding validate column presence and types (fast script):

```php
$first = array_map('trim', str_getcsv(file_get_contents(storage_path('imports/users.csv'))));
// assert header contains email,name
```

Running the application & tests
-------------------------------

- Serve locally (dev):

```bash
php artisan serve --host=127.0.0.1 --port=8000
npm run dev
```

- Or using Docker + Sail:

```bash
./vendor/bin/sail up -d
./vendor/bin/sail composer install
./vendor/bin/sail artisan migrate --force
```

- Run unit tests:

```bash
php artisan test
```

Troubleshooting
---------------

- Permissions: ensure `storage/` and `bootstrap/cache` are writable by your webserver/PHP user:

```bash
chmod -R 775 storage bootstrap/cache
```

- Composer timeout/memory: run `php -d memory_limit=-1 composer install` if you hit memory issues.
- Migration errors: confirm `.env` DB settings and that the database exists and the user has privileges.

Useful commands (cheat sheet)
----------------------------

- Install dependencies: `composer install`, `npm ci`
- Build assets: `npm run build`
- Dev server: `php artisan serve` or `npm run dev` (for frontend HMR)
- Migrate: `php artisan migrate`
- Seed (all): `php artisan db:seed`
- Seed specific class: `php artisan db:seed --class=UsersCsvSeeder`
- Clear caches: `php artisan optimize:clear`

Security & best practices
-------------------------
- Never commit real production `.env` files or CSVs containing secrets or real passwords.
- Keep CSV import code reviewed — CSVs from external sources should be validated and sanitized.
- When importing large datasets prefer database-native bulk import (MySQL `LOAD DATA INFILE`) behind a secure process.

Questions or next steps
-----------------------
- Do you want me to add a sample `UsersCsvSeeder.php` file to `database/seeders/` and a small example CSV under `storage/imports/`? I can create them and wire them into `DatabaseSeeder` for one-command seeding.

---

This README provides a developer-focused setup and safe CSV seeding workflow. Follow the example seeder pattern for other models (startups, investments, activity logs) and adapt chunk size and hashing logic for your needs.

