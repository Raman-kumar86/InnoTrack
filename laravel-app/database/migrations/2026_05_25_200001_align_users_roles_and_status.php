<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'status')) {
                $table->string('status', 20)->default('active')->after('role');
            }
        });

        // Map legacy role values to the new normalized roles
        DB::table('users')->where('role', 'state_officer')->update(['role' => 'state_analyst']);
        DB::table('users')->where('role', 'viewer')->update(['role' => 'reviewer']);

        // Any other unexpected/legacy roles will be downgraded to 'reviewer' (least privilege)
        DB::table('users')->whereNotIn('role', ['super_admin', 'state_analyst', 'reviewer'])->update(['role' => 'reviewer']);

        // Normalize is_active into the new status column
        DB::table('users')->where('is_active', true)->orWhereNull('is_active')->update(['status' => 'active']);
        DB::table('users')->where('is_active', false)->update(['status' => 'blocked']);

        // Now that all role/status values are within the allowed set, modify columns to ENUMs
        DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin','state_analyst','reviewer') NOT NULL DEFAULT 'reviewer'");
        DB::statement("ALTER TABLE users MODIFY status ENUM('active','blocked') NOT NULL DEFAULT 'active'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY role VARCHAR(255) NOT NULL DEFAULT 'reviewer'");
        DB::statement("ALTER TABLE users MODIFY status VARCHAR(20) NOT NULL DEFAULT 'active'");

        DB::table('users')->where('role', 'state_analyst')->update(['role' => 'state_officer']);
        DB::table('users')->where('role', 'reviewer')->update(['role' => 'viewer']);

        if (Schema::hasColumn('users', 'status')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('status');
            });
        }
    }
};
