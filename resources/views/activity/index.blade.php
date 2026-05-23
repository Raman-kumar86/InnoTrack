@extends('layouts.app')

@php
    $title = 'Activity Logs';
    $pageTitle = 'Activity Logs';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Activity Logs', 'url' => route('activity.index')],
    ];

    $logs = [
        ['time' => '09:42', 'user' => 'Aarav Sharma', 'module' => 'Funding', 'action' => 'Approved round', 'result' => 'Success', 'tone' => 'success'],
        ['time' => '10:15', 'user' => 'Meera Kapoor', 'module' => 'State Analytics', 'action' => 'Updated ranking weights', 'result' => 'Success', 'tone' => 'info'],
        ['time' => '11:03', 'user' => 'Irfan Khan', 'module' => 'Users', 'action' => 'Access request rejected', 'result' => 'Blocked', 'tone' => 'danger'],
        ['time' => '12:27', 'user' => 'Nisha Iyer', 'module' => 'Reports', 'action' => 'Generated quarterly brief', 'result' => 'Success', 'tone' => 'success'],
    ];

    $timeline = [
        ['title' => 'Portfolio sync completed', 'meta' => 'Registry data refreshed from state feed', 'time' => 'Today, 08:30'],
        ['title' => 'Funding audit exported', 'meta' => 'CSV package delivered to admin mailbox', 'time' => 'Today, 10:05'],
        ['title' => 'Role permissions updated', 'meta' => 'Reviewer access adjusted for 4 users', 'time' => 'Yesterday, 17:40'],
    ];
@endphp

@section('content')
<section class="space-y-6">
    <x-ui.section-header
        title="Activity log"
        subtitle="Track audit trails, user actions, and system events with a structured timeline and filter panel."
    >
        <x-ui.button variant="secondary">Export log</x-ui.button>
        <x-ui.button>Apply filter</x-ui.button>
    </x-ui.section-header>

    <x-ui.card>
        <div class="grid gap-4 xl:grid-cols-12">
            <div class="xl:col-span-4">
                <x-ui.form-field label="Search activity">
                    <input type="search" class="input-modern" placeholder="Search users or actions" />
                </x-ui.form-field>
            </div>
            <div class="xl:col-span-3">
                <x-ui.select-field label="Action type">
                    <select class="select-modern">
                        <option>All actions</option>
                        <option>Approve</option>
                        <option>Update</option>
                        <option>Delete</option>
                        <option>Export</option>
                    </select>
                </x-ui.select-field>
            </div>
            <div class="xl:col-span-3">
                <x-ui.select-field label="Date range">
                    <select class="select-modern">
                        <option>Today</option>
                        <option>Last 7 days</option>
                        <option>Last 30 days</option>
                    </select>
                </x-ui.select-field>
            </div>
            <div class="flex items-end xl:col-span-2">
                <x-ui.button variant="secondary" class="w-full justify-center">Reset</x-ui.button>
            </div>
        </div>
    </x-ui.card>

    <x-ui.table title="Audit trail" subtitle="Color-coded rows show the nature of each change and who performed it.">
        <thead class="table-head">
            <tr>
                <th class="px-6 py-4 font-semibold">Timestamp</th>
                <th class="px-6 py-4 font-semibold">User</th>
                <th class="px-6 py-4 font-semibold">Module</th>
                <th class="px-6 py-4 font-semibold">Action</th>
                <th class="px-6 py-4 font-semibold">Result</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
            @foreach ($logs as $log)
                <tr>
                    <td class="table-cell font-medium text-slate-900 dark:text-white">{{ $log['time'] }}</td>
                    <td class="table-cell">{{ $log['user'] }}</td>
                    <td class="table-cell">{{ $log['module'] }}</td>
                    <td class="table-cell">{{ $log['action'] }}</td>
                    <td class="table-cell">
                        <x-ui.badge :variant="$log['tone'] === 'danger' ? 'danger' : ($log['tone'] === 'info' ? 'info' : 'success')">{{ $log['result'] }}</x-ui.badge>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </x-ui.table>

    <x-ui.card>
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Activity timeline</h3>
        <div class="mt-5 space-y-4">
            @foreach ($timeline as $item)
                <div class="rounded-3xl border border-slate-200 p-4 dark:border-slate-800">
                    <div class="flex items-center justify-between gap-3">
                        <p class="font-medium text-slate-900 dark:text-white">{{ $item['title'] }}</p>
                        <span class="text-sm text-slate-500 dark:text-slate-400">{{ $item['time'] }}</span>
                    </div>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $item['meta'] }}</p>
                </div>
            @endforeach
        </div>
    </x-ui.card>
</section>
@endsection
