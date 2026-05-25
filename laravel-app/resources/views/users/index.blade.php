@extends('layouts.app')

@php
    $title = 'User Management';
    $pageTitle = 'User Management';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'User Management', 'url' => route('users.index')],
    ];

    // `$users` is provided by the controller (paginated Eloquent results)
    $roleOptions = $roleOptions ?? ['super_admin', 'state_analyst', 'reviewer'];
    $statusOptions = $statusOptions ?? ['active', 'blocked'];
    $filters = $filters ?? ['search' => null, 'role' => null, 'status' => null];
@endphp

@section('content')
<section class="space-y-6">
    <x-ui.section-header
        title="User management"
        subtitle="Manage role access, onboarding status, and admin permissions with audit-ready controls."
    >
    </x-ui.section-header>

    <x-ui.card>
        <form method="GET" action="{{ route('users.index') }}">
            <div class="grid gap-4 xl:grid-cols-12">
                <div class="xl:col-span-5">
                    <x-ui.form-field label="Search users">
                        <input type="search" name="search" value="{{ $filters['search'] }}" class="input-modern" placeholder="Search by name or email" />
                    </x-ui.form-field>
                </div>
                <div class="xl:col-span-3">
                    <x-ui.select-field label="Role filter">
                        <select name="role" class="select-modern">
                            <option value="">All roles</option>
                            @foreach($roleOptions as $opt)
                                <option value="{{ $opt }}" {{ $filters['role'] === $opt ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $opt)) }}</option>
                            @endforeach
                        </select>
                    </x-ui.select-field>
                </div>
                <div class="xl:col-span-2">
                    <x-ui.select-field label="Status">
                        <select name="status" class="select-modern">
                            <option value="">All</option>
                            @foreach($statusOptions as $opt)
                                <option value="{{ $opt }}" {{ $filters['status'] === $opt ? 'selected' : '' }}>{{ ucwords($opt) }}</option>
                            @endforeach
                        </select>
                    </x-ui.select-field>
                </div>
                <div class="flex items-end gap-2 xl:col-span-2">
                    <x-ui.button variant="secondary" type="submit" class="w-full justify-center">Filter</x-ui.button>
                </div>
            </div>
        </form>
    </x-ui.card>

    <x-ui.table title="Access control table" subtitle="Role badges, active toggles, and quick admin actions.">
        <thead class="table-head">
            <tr>
                <th class="px-6 py-4 font-semibold">User</th>
                <th class="px-6 py-4 font-semibold">Email</th>
                <th class="px-6 py-4 font-semibold">Role</th>
                <th class="px-6 py-4 font-semibold">Status</th>
                <th class="px-6 py-4 font-semibold">Portal access</th>
                <th class="px-6 py-4 font-semibold">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
            @foreach ($users as $user)
                <tr>
                    <td class="table-cell font-medium text-slate-900 dark:text-white">{{ $user->name }}</td>
                    <td class="table-cell">{{ $user->email }}</td>
                    <td class="table-cell">
                        <x-ui.badge variant="info">{{ ucwords(str_replace('_', ' ', $user->role)) }}</x-ui.badge>
                    </td>
                    <td class="table-cell">
                        <x-ui.badge :variant="$user->status === 'active' ? 'success' : 'neutral'">{{ ucwords($user->status) }}</x-ui.badge>
                    </td>
                    <td class="table-cell">
                        <label class="inline-flex items-center">
                            <input aria-label="Portal access" type="checkbox" class="h-5 w-10 rounded-full border-slate-300 bg-slate-200 text-indigo-600 focus:ring-indigo-500" {{ $user->status === 'active' ? 'checked' : '' }} disabled>
                        </label>
                    </td>
                    <td class="table-cell">
                        <div class="flex items-center gap-2">
                            @can('update', $user)
                                <button type="button" class="rounded-2xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-800 dark:text-slate-200 dark:hover:bg-slate-800" data-modal-open="#edit-user-modal" data-user-id="{{ $user->id }}">
                                    Edit
                                </button>
                            @endcan

                            @if ($user->status === 'active')
                                @can('block', $user)
                                    <form method="POST" action="{{ route('users.block', $user) }}">
                                        @csrf
                                        <button type="submit" class="rounded-2xl border border-yellow-200 px-3 py-2 text-sm font-semibold text-yellow-600 transition hover:bg-yellow-500/10 dark:border-yellow-900/40 dark:text-yellow-400">Block</button>
                                    </form>
                                @endcan
                            @else
                                @can('unblock', $user)
                                    <form method="POST" action="{{ route('users.unblock', $user) }}">
                                        @csrf
                                        <button type="submit" class="rounded-2xl border border-green-200 px-3 py-2 text-sm font-semibold text-green-600 transition hover:bg-green-500/10 dark:border-green-900/40 dark:text-green-400">Unblock</button>
                                    </form>
                                @endcan
                            @endif

                            @can('promote', $user)
                                <form method="POST" action="{{ route('users.promote', $user) }}">
                                    @csrf
                                    @if($user->role === 'reviewer')
                                        <button type="submit" class="rounded-2xl border border-indigo-200 px-3 py-2 text-sm font-semibold text-indigo-600 transition hover:bg-indigo-500/10 dark:border-indigo-900/40 dark:text-indigo-400">Promote to State Analyst</button>
                                    @elseif($user->role === 'state_analyst')
                                        <button type="submit" class="rounded-2xl border border-indigo-200 px-3 py-2 text-sm font-semibold text-indigo-600 transition hover:bg-indigo-500/10 dark:border-indigo-900/40 dark:text-indigo-400">Promote to Super Admin</button>
                                    @endif
                                </form>
                            @endcan

                            @can('demote', $user)
                                <form method="POST" action="{{ route('users.demote', $user) }}">
                                    @csrf
                                    <button type="submit" class="rounded-2xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-800 dark:text-slate-200 dark:hover:bg-slate-800">Demote to Reviewer</button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </x-ui.table>

    <div class="mt-4">
        {{ $users->links() }}
    </div>

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
