@extends('layouts.app')

@php
    $title = 'User Management';
    $pageTitle = 'User Management';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'User Management', 'url' => route('users.index')],
    ];

    $users = [
        ['name' => 'Aarav Sharma', 'email' => 'aarav@startupindia.gov.in', 'role' => 'Super Admin', 'status' => 'Active'],
        ['name' => 'Meera Kapoor', 'email' => 'meera@startupindia.gov.in', 'role' => 'State Analyst', 'status' => 'Active'],
        ['name' => 'Irfan Khan', 'email' => 'irfan@startupindia.gov.in', 'role' => 'Reviewer', 'status' => 'Inactive'],
        ['name' => 'Nisha Iyer', 'email' => 'nisha@startupindia.gov.in', 'role' => 'Reporting Officer', 'status' => 'Active'],
    ];
@endphp

@section('content')
<section class="space-y-6">
    <x-ui.section-header
        title="User management"
        subtitle="Manage role access, onboarding status, and admin permissions with audit-ready controls."
    >
        <x-ui.button variant="secondary">Invite user</x-ui.button>
        <x-ui.button>New role</x-ui.button>
    </x-ui.section-header>

    <x-ui.card>
        <div class="grid gap-4 xl:grid-cols-12">
            <div class="xl:col-span-5">
                <x-ui.form-field label="Search users">
                    <input type="search" class="input-modern" placeholder="Search by name or email" />
                </x-ui.form-field>
            </div>
            <div class="xl:col-span-3">
                <x-ui.select-field label="Role filter">
                    <select class="select-modern">
                        <option>All roles</option>
                        <option>Super Admin</option>
                        <option>State Analyst</option>
                        <option>Reviewer</option>
                        <option>Reporting Officer</option>
                    </select>
                </x-ui.select-field>
            </div>
            <div class="xl:col-span-2">
                <x-ui.select-field label="Status">
                    <select class="select-modern">
                        <option>All</option>
                        <option>Active</option>
                        <option>Inactive</option>
                    </select>
                </x-ui.select-field>
            </div>
            <div class="flex items-end gap-2 xl:col-span-2">
                <x-ui.button variant="secondary" class="w-full justify-center">Filter</x-ui.button>
            </div>
        </div>
    </x-ui.card>

    <x-ui.table title="Access control table" subtitle="Role badges, active toggles, and quick admin actions.">
        <thead class="table-head">
            <tr>
                <th class="px-6 py-4 font-semibold">User</th>
                <th class="px-6 py-4 font-semibold">Email</th>
                <th class="px-6 py-4 font-semibold">Role</th>
                <th class="px-6 py-4 font-semibold">Status</th>
                <th class="px-6 py-4 font-semibold">Access</th>
                <th class="px-6 py-4 font-semibold">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
            @foreach ($users as $user)
                <tr>
                    <td class="table-cell font-medium text-slate-900 dark:text-white">{{ $user['name'] }}</td>
                    <td class="table-cell">{{ $user['email'] }}</td>
                    <td class="table-cell">
                        <x-ui.badge variant="info">{{ $user['role'] }}</x-ui.badge>
                    </td>
                    <td class="table-cell">
                        <x-ui.badge :variant="$user['status'] === 'Active' ? 'success' : 'neutral'">{{ $user['status'] }}</x-ui.badge>
                    </td>
                    <td class="table-cell">
                        <label class="inline-flex cursor-pointer items-center gap-3">
                            <input type="checkbox" class="h-5 w-10 rounded-full border-slate-300 bg-slate-200 text-indigo-600 focus:ring-indigo-500" checked>
                            <span class="text-sm text-slate-500 dark:text-slate-400">Portal access</span>
                        </label>
                    </td>
                    <td class="table-cell">
                        <div class="flex items-center gap-2">
                            <button type="button" class="rounded-2xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-800 dark:text-slate-200 dark:hover:bg-slate-800" data-modal-open="#edit-user-modal">
                                Edit
                            </button>
                            <button type="button" class="rounded-2xl border border-rose-200 px-3 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-500/10 dark:border-rose-900/40 dark:text-rose-400" data-modal-open="#delete-user-modal">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </x-ui.table>

    <x-ui.modal id="edit-user-modal" title="Edit user">
        <div class="grid gap-4 md:grid-cols-2">
            <x-ui.form-field label="Full name"><input type="text" class="input-modern" value="Aarav Sharma"></x-ui.form-field>
            <x-ui.form-field label="Email"><input type="email" class="input-modern" value="aarav@startupindia.gov.in"></x-ui.form-field>
            <x-ui.select-field label="Role">
                <select class="select-modern"><option>Super Admin</option><option>State Analyst</option><option>Reviewer</option></select>
            </x-ui.select-field>
            <x-ui.select-field label="Status">
                <select class="select-modern"><option>Active</option><option>Inactive</option></select>
            </x-ui.select-field>
        </div>
        <div class="mt-6 flex justify-end gap-3">
            <x-ui.button variant="secondary" type="button" data-modal-close>Cancel</x-ui.button>
            <x-ui.button type="button">Save changes</x-ui.button>
        </div>
    </x-ui.modal>

    <x-ui.modal id="delete-user-modal" title="Delete user">
        <p class="text-sm text-slate-500 dark:text-slate-400">This action will deactivate the selected account and preserve the audit trail.</p>
        <div class="mt-6 flex justify-end gap-3">
            <x-ui.button variant="secondary" type="button" data-modal-close>Cancel</x-ui.button>
            <x-ui.button variant="danger" type="button">Delete user</x-ui.button>
        </div>
    </x-ui.modal>
</section>
@endsection
