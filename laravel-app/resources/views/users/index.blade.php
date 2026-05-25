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
        subtitle="Manage role access, onboarding status, and admin permissions with audit-ready controls.">
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
                        @if ($user->status === 'active')
                        <form method="POST" action="{{ route('users.block', $user) }}">
                            @csrf
                            <input
                                aria-label="Portal access"
                                type="checkbox"
                                class="h-5 w-10 rounded-full border-slate-300 bg-slate-200 text-indigo-600 focus:ring-indigo-500"
                                checked
                                @can('block', $user)
                                onchange="this.form.submit()"
                                @else
                                disabled
                                @endcan>
                        </form>
                        @else
                        <form method="POST" action="{{ route('users.unblock', $user) }}">
                            @csrf
                            <input
                                aria-label="Portal access"
                                type="checkbox"
                                class="h-5 w-10 rounded-full border-slate-300 bg-slate-200 text-indigo-600 focus:ring-indigo-500"
                                @can('unblock', $user)
                                onchange="this.form.submit()"
                                @else
                                disabled
                                @endcan>
                        </form>
                        @endif
                    </label>
                </td>
                <td class="table-cell">
                    <div class="relative inline-flex" x-data="{ open: false }" @keydown.escape.window="open = false">
                        <button type="button" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800" @click="open = !open" :aria-expanded="open.toString()" aria-haspopup="true">
                            Actions
                            <span class="transition-transform duration-200" :class="open ? 'rotate-180' : ''">
                                <x-ui.icon name="chevron-down" class="h-4 w-4" />
                            </span>
                        </button>

                        <div x-cloak x-show="open" x-transition.origin.top.right @click.outside="open = false" class="absolute right-full top-0 mr-6 z-20 w-64 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl shadow-slate-200/70 dark:border-slate-800 dark:bg-slate-950 dark:shadow-slate-950/40">
                            <div class="space-y-1">
                                @can('update', $user)
                                <button type="button" class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white" data-modal-open="#edit-user-modal" data-user-id="{{ $user->id }}" @click="open = false">
                                    <span>Edit</span>
                                    <x-ui.icon name="edit" class="h-4 w-4" />
                                </button>
                                @endcan

                                @if ($user->status === 'active')
                                @can('block', $user)
                                <form method="POST" action="{{ route('users.block', $user) }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-sm text-yellow-600 transition hover:bg-yellow-500/10 dark:text-yellow-400">
                                        <span>Block</span>
                                        <x-ui.icon name="ban" class="h-4 w-4" />
                                    </button>
                                </form>
                                @endcan
                                @else
                                @can('unblock', $user)
                                <form method="POST" action="{{ route('users.unblock', $user) }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-sm text-green-600 transition hover:bg-green-500/10 dark:text-green-400">
                                        <span>Unblock</span>
                                        <x-ui.icon name="check-circle" class="h-4 w-4" />
                                    </button>
                                </form>
                                @endcan
                                @endif

                                @can('promote', $user)
                                <form method="POST" action="{{ route('users.promote', $user) }}">
                                    @csrf
                                    @if($user->role === 'reviewer')
                                    <button type="submit" class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-sm text-indigo-600 transition hover:bg-indigo-500/10 dark:text-indigo-400">
                                        <span>Promote to State Analyst</span>
                                        <x-ui.icon name="arrow-up" class="h-4 w-4" />
                                    </button>
                                    @elseif($user->role === 'state_analyst')
                                    <button type="submit" class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-sm text-indigo-600 transition hover:bg-indigo-500/10 dark:text-indigo-400">
                                        <span>Promote to Super Admin</span>
                                        <x-ui.icon name="arrow-up" class="h-4 w-4" />
                                    </button>
                                    @endif
                                </form>
                                @endcan

                                @can('demote', $user)
                                <form method="POST" action="{{ route('users.demote', $user) }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-sm text-slate-700 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800">
                                        <span>Demote to Reviewer</span>
                                        <x-ui.icon name="arrow-down" class="h-4 w-4" />
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </div>
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
                <select class="select-modern">
                    <option>Super Admin</option>
                    <option>State Analyst</option>
                    <option>Reviewer</option>
                </select>
            </x-ui.select-field>
            <x-ui.select-field label="Status">
                <select class="select-modern">
                    <option>Active</option>
                    <option>Inactive</option>
                </select>
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