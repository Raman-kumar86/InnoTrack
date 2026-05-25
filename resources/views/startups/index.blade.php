@extends('layouts.app')

@section('content')
<section class="space-y-6">
    <x-ui.section-header
        title="Startup management"
        subtitle="Search, filter, sort, export, and manage startup records in a data-rich admin interface."
    >
        <x-ui.button href="{{ route('reports.index') }}" variant="secondary">
            <x-ui.icon name="download" class="h-4 w-4" />
            Export CSV
        </x-ui.button>
        <x-ui.button href="{{ route('startups.create') }}">
            <x-ui.icon name="plus" class="h-4 w-4" />
            Add Startup
        </x-ui.button>
    </x-ui.section-header>

    <x-ui.card>
        <div class="grid gap-4 xl:grid-cols-12">
            <div class="xl:col-span-4">
                <x-ui.form-field label="Advanced search" help="Search by startup, founder, sector, or registration code.">
                    <input type="search" class="input-modern" placeholder="Search startups" />
                </x-ui.form-field>
            </div>
            <div class="xl:col-span-2">
                <x-ui.select-field label="Sector">
                    <select class="select-modern">
                        <option>All sectors</option>
                        <option>SaaS</option>
                        <option>FinTech</option>
                        <option>HealthTech</option>
                        <option>Agritech</option>
                    </select>
                </x-ui.select-field>
            </div>
            <div class="xl:col-span-2">
                <x-ui.select-field label="State">
                    <select class="select-modern">
                        <option>All states</option>
                        <option>Karnataka</option>
                        <option>Maharashtra</option>
                        <option>Delhi</option>
                        <option>Gujarat</option>
                    </select>
                </x-ui.select-field>
            </div>
            <div class="xl:col-span-2">
                <x-ui.select-field label="Sort by">
                    <select class="select-modern">
                        <option>Newest first</option>
                        <option>Funding highest</option>
                        <option>Growth rate</option>
                        <option>Status</option>
                    </select>
                </x-ui.select-field>
            </div>
            <div class="flex items-end gap-2 xl:col-span-2">
                <x-ui.button variant="secondary" class="w-full justify-center">
                    <x-ui.icon name="filter" class="h-4 w-4" />
                    Filters
                </x-ui.button>
            </div>
        </div>
    </x-ui.card>

    <x-ui.table title="Startup registry" subtitle="Responsive table with actions, status badges, and export-friendly spacing.">
        <thead class="table-head">
            <tr>
                <th class="px-6 py-4 font-semibold">Startup Name</th>
                <th class="px-6 py-4 font-semibold">Sector</th>
                <th class="px-6 py-4 font-semibold">State</th>
                <th class="px-6 py-4 font-semibold">Stage</th>
                <th class="px-6 py-4 font-semibold">Status</th>
                <th class="px-6 py-4 font-semibold">DPIIT</th>
                <th class="px-6 py-4 font-semibold">Founded Date</th>
                <th class="px-6 py-4 font-semibold">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
            @foreach ($startups as $startup)
                <tr class="group hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                    <td class="table-cell font-medium text-slate-900 dark:text-white">{{ $startup['name'] }}</td>
                    <td class="table-cell">{{ $startup['sector'] }}</td>
                    <td class="table-cell">{{ $startup['state'] }}</td>
                    <td class="table-cell">{{ $startup['stage'] }}</td>
                    <td class="table-cell">
                        <x-ui.badge :variant="$startup['status'] === 'Active' ? 'success' : ($startup['status'] === 'Review' ? 'warning' : 'neutral')">{{ $startup['status'] }}</x-ui.badge>
                    </td>
                    <td class="table-cell">{{ $startup['dpiit'] }}</td>
                    <td class="table-cell">{{ $startup['founded'] }}</td>
                    <td class="table-cell">
                        <div class="relative inline-flex">
                            <button type="button" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                                Actions
                                <x-ui.icon name="chevron-down" class="h-4 w-4" />
                            </button>
                            <div class="invisible absolute right-0 top-12 z-10 w-48 rounded-2xl border border-slate-200 bg-white p-2 opacity-0 shadow-xl shadow-slate-200/70 transition group-hover:visible group-hover:opacity-100 dark:border-slate-800 dark:bg-slate-950 dark:shadow-slate-950/40">
                                <a href="{{ route('startups.show', ['startup' => $startup['id']]) }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
                                    <x-ui.icon name="external" class="h-4 w-4" /> View profile
                                </a>
                                <a href="{{ route('startups.edit', ['startup' => $startup['id']]) }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
                                    <x-ui.icon name="edit" class="h-4 w-4" /> Edit startup
                                </a>
                                <button type="button" class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-sm text-rose-600 transition hover:bg-rose-500/10 dark:text-rose-400">
                                    <x-ui.icon name="trash" class="h-4 w-4" /> Delete
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </x-ui.table>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-ui.empty-state
            title="No matching startups"
            description="When filters return zero rows, this empty state keeps the page informative and actionable."
            class="hidden lg:flex"
            icon="circle"
        >
            <x-slot:action>
                <x-ui.button href="{{ route('startups.create') }}">
                    <x-ui.icon name="plus" class="h-4 w-4" />
                    Add Startup
                </x-ui.button>
            </x-slot:action>
        </x-ui.empty-state>

        <x-ui.card class="lg:col-span-2">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Pagination</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Stubbed pagination controls for the production table experience.</p>
                </div>
                <div class="flex items-center gap-2">
                    <x-ui.button variant="secondary">Previous</x-ui.button>
                    <x-ui.button>Next</x-ui.button>
                </div>
            </div>
        </x-ui.card>
    </div>
</section>
@endsection
