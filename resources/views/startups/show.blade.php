@extends('layouts.app')

@php
    $title = 'Startup Profile';
    $pageTitle = 'AeroNex Robotics';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Startups', 'url' => route('startups.index')],
        ['label' => 'AeroNex Robotics', 'url' => route('startups.show', ['startup' => 1])],
    ];

    $milestones = [
        ['label' => 'Founded', 'value' => '12 Apr 2022'],
        ['label' => 'DPIIT Recognised', 'value' => '28 Nov 2022'],
        ['label' => 'Seed Round', 'value' => '14 Aug 2023'],
        ['label' => 'Series A', 'value' => '10 Jan 2026'],
    ];

    $founders = [
        ['name' => 'Ananya Rao', 'role' => 'Co-founder and CEO', 'email' => 'ananya@aeronex.in'],
        ['name' => 'Rahul Mehta', 'role' => 'Co-founder and CTO', 'email' => 'rahul@aeronex.in'],
    ];

    $fundingHistory = [
        ['date' => '10 Jan 2026', 'round' => 'Series A', 'amount' => 'Rs 18 Cr', 'investor' => 'National Growth Fund'],
        ['date' => '14 Aug 2023', 'round' => 'Seed', 'amount' => 'Rs 4.5 Cr', 'investor' => 'Accelerator Partners'],
        ['date' => '03 Dec 2022', 'round' => 'Grant', 'amount' => 'Rs 80 L', 'investor' => 'Innovation Mission'],
    ];

    $timeline = [
        ['title' => 'Application submitted', 'meta' => 'Review queue accepted', 'time' => '12 Nov 2022'],
        ['title' => 'DPIIT approval issued', 'meta' => 'Certificate published to portal', 'time' => '28 Nov 2022'],
        ['title' => 'State incentive approved', 'meta' => 'Karnataka expansion support', 'time' => '07 Feb 2024'],
        ['title' => 'Series A closed', 'meta' => 'Growth capital infusion approved', 'time' => '10 Jan 2026'],
    ];
@endphp

@section('content')
<section class="space-y-6">
    <div class="surface-card overflow-hidden">
        <div class="bg-gradient-to-r from-slate-950 via-indigo-950 to-slate-900 px-6 py-8 text-white sm:px-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                    <div class="flex h-24 w-24 items-center justify-center rounded-3xl bg-white/10 text-3xl font-semibold ring-1 ring-white/15">AR</div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-3xl font-semibold tracking-tight">AeroNex Robotics</h2>
                            <x-ui.badge variant="success">Active</x-ui.badge>
                            <x-ui.badge variant="info">DPIIT Recognised</x-ui.badge>
                        </div>
                        <p class="mt-3 max-w-3xl text-sm text-white/70">Autonomous inspection robotics for manufacturing, logistics, and public infrastructure monitoring.</p>
                        <div class="mt-4 flex flex-wrap items-center gap-2 text-sm text-white/75">
                            <span class="rounded-full bg-white/10 px-3 py-1">Deep Tech</span>
                            <span class="rounded-full bg-white/10 px-3 py-1">Karnataka</span>
                            <span class="rounded-full bg-white/10 px-3 py-1">Series A</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <x-ui.button href="https://example.com" variant="secondary">Website</x-ui.button>
                    <x-ui.button href="{{ route('startups.edit', ['startup' => 1]) }}">Edit startup</x-ui.button>
                </div>
            </div>
        </div>

        <div class="grid gap-4 border-t border-slate-200 p-6 dark:border-slate-800 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl bg-slate-50 p-4 dark:bg-slate-900">
                <p class="text-sm text-slate-500 dark:text-slate-400">Total funding</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Rs 22.5 Cr</p>
            </div>
            <div class="rounded-3xl bg-slate-50 p-4 dark:bg-slate-900">
                <p class="text-sm text-slate-500 dark:text-slate-400">Jobs created</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">184</p>
            </div>
            <div class="rounded-3xl bg-slate-50 p-4 dark:bg-slate-900">
                <p class="text-sm text-slate-500 dark:text-slate-400">Milestone stage</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Series A</p>
            </div>
            <div class="rounded-3xl bg-slate-50 p-4 dark:bg-slate-900">
                <p class="text-sm text-slate-500 dark:text-slate-400">Current status</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Scaling</p>
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <x-ui.card class="xl:col-span-2">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Funding history</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Round chronology with investors, amounts, and timing.</p>
                </div>
                <x-ui.button href="{{ route('funding.create') }}" variant="secondary">
                    <x-ui.icon name="plus" class="h-4 w-4" /> Add funding round
                </x-ui.button>
            </div>

            <div class="mt-6 overflow-hidden">
                <x-ui.table>
                    <thead class="table-head">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Date</th>
                            <th class="px-6 py-4 font-semibold">Round</th>
                            <th class="px-6 py-4 font-semibold">Amount</th>
                            <th class="px-6 py-4 font-semibold">Investor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach ($fundingHistory as $funding)
                            <tr>
                                <td class="table-cell">{{ $funding['date'] }}</td>
                                <td class="table-cell font-medium text-slate-900 dark:text-white">{{ $funding['round'] }}</td>
                                <td class="table-cell">{{ $funding['amount'] }}</td>
                                <td class="table-cell">{{ $funding['investor'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-ui.table>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Founder information</h3>
            <div class="mt-5 space-y-4">
                @foreach ($founders as $founder)
                    <div class="rounded-3xl border border-slate-200 p-4 dark:border-slate-800">
                        <p class="font-medium text-slate-900 dark:text-white">{{ $founder['name'] }}</p>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $founder['role'] }}</p>
                        <p class="mt-2 text-sm text-indigo-600 dark:text-indigo-400">{{ $founder['email'] }}</p>
                    </div>
                @endforeach
            </div>
        </x-ui.card>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <x-ui.card>
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Milestones</h3>
            <div class="mt-5 space-y-4">
                @foreach ($milestones as $milestone)
                    <div class="flex items-start gap-4">
                        <div class="mt-1 h-3 w-3 rounded-full bg-indigo-500"></div>
                        <div>
                            <p class="font-medium text-slate-900 dark:text-white">{{ $milestone['label'] }}</p>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $milestone['value'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-ui.card>

        <x-ui.card class="xl:col-span-2">
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
    </div>
</section>
@endsection
