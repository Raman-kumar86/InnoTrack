<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserManagementSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Super Admin One', 'email' => 'superadmin@startupindia.gov.in', 'role' => 'super_admin', 'status' => 'active'],
            ['name' => 'Aarav Sharma', 'email' => 'aarav.sharma@startupindia.gov.in', 'role' => 'state_analyst', 'status' => 'active'],
            ['name' => 'Meera Kapoor', 'email' => 'meera.kapoor@startupindia.gov.in', 'role' => 'state_analyst', 'status' => 'active'],
            ['name' => 'Irfan Khan', 'email' => 'irfan.khan@startupindia.gov.in', 'role' => 'reviewer', 'status' => 'active'],
            ['name' => 'Nisha Iyer', 'email' => 'nisha.iyer@startupindia.gov.in', 'role' => 'reviewer', 'status' => 'active'],
            ['name' => 'Rohan Mehta', 'email' => 'rohan.mehta@startupindia.gov.in', 'role' => 'reviewer', 'status' => 'active'],
            ['name' => 'Sana Ali', 'email' => 'sana.ali@startupindia.gov.in', 'role' => 'reviewer', 'status' => 'blocked'],
            ['name' => 'Dev Patel', 'email' => 'dev.patel@startupindia.gov.in', 'role' => 'reviewer', 'status' => 'active'],
        ];

        foreach ($users as $entry) {
            User::updateOrCreate(
                ['email' => $entry['email']],
                [
                    'name' => $entry['name'],
                    'role' => $entry['role'],
                    'status' => $entry['status'],
                    'is_active' => $entry['status'] === 'active',
                    'state' => $entry['role'] === 'super_admin' ? 'All' : null,
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                ]
            );
        }
    }
}
