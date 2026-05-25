@extends('layouts.app')

@php
    $startupName = $startup->startup_name ?: 'Startup Profile';
    $title = $startupName;
    $pageTitle = $startupName;

    $initials = collect(preg_split('/\s+/', trim($startupName)) ?: [])
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_substr($part, 0, 1))
        ->implode('');
    $initials = $initials !== '' ? mb_strtoupper($initials) : 'SP';

    $status = $startup->status ?: 'Unknown';
    $statusKey = mb_strtolower($status);
    $statusVariant = match ($statusKey) {
        'active', 'scaling' => 'success',
        'inactive', 'paused' => 'warning',
        'acquired' => 'info',
        'failed' => 'danger',
        default => 'secondary',
    };

    $sectorName = $startup->sector?->sector_name ?? 'Sector not assigned';
    $stateName = $startup->state?->state_name ?? 'State not assigned';
    $description = $startup->description ?: 'No startup description has been added yet.';
    $website = $startup->website;
    $email = $startup->email;
    $phone = $startup->phone;
    $linkedinUrl = $startup->linkedin_url;

    $formatMoney = fn ($value, string $currency = 'USD') => $value === null || $value === ''
        ? 'Not available'
        : $currency.' '.number_format((float) $value, floor((float) $value) == (float) $value ? 0 : 2);

    $formatDate = fn ($date) => $date ? $date->format('d M Y') : 'Not available';

    $fundingStage = $startup->funding_stage ?: 'Not available';
    $foundedYear = $startup->founded_year ? (string) $startup->founded_year : 'Not available';
    $jobsCreated = $startup->jobs_created !== null ? number_format((int) $startup->jobs_created) : 'Not available';
    $employeeCount = $startup->employee_count !== null ? number_format((int) $startup->employee_count) : 'Not available';
    $founderCount = $startup->founder_count !== null ? number_format((int) $startup->founder_count) : 'Not available';
    $tags = $startup->tags->pluck('tag')->filter()->values();
    $founders = $startup->founders;
    $founderNamesPreview = $founders->take(3)->pluck('full_name')->join(', ');
    $founderOthersCount = max(0, $founders->count() - 3);
    $fundingRounds = $startup->fundingRounds;
    $timeline = $startup->updates;

    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Startups', 'url' => route('startups.index')],
        ['label' => $startupName, 'url' => route('startups.show', ['startup' => $startup->id])],
    ];
@endphp

@section('content')
<section class="space-y-6">
    <div class="surface-card overflow-hidden">
        <div class="bg-linear-to-r from-slate-950 via-indigo-950 to-slate-900 px-6 py-8 text-white sm:px-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                    <div class="flex h-24 w-24 items-center justify-center rounded-3xl bg-white/10 text-3xl font-semibold ring-1 ring-white/15">{{ $initials }}</div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-3xl font-semibold tracking-tight">{{ $startupName }}</h2>
                            @if($founders->isNotEmpty())
                                <div class="mt-1 flex items-center gap-3 text-sm text-white/80">
                                    <div class="flex -space-x-2">
                                        @foreach($founders->take(3) as $f)
                                            <div class="h-7 w-7 flex items-center justify-center rounded-full bg-white/20 text-xs font-semibold ring-1 ring-white/25">{{ mb_strtoupper(mb_substr($f->full_name, 0, 1)) }}</div>
                                        @endforeach
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm text-white/90">{{ $founderNamesPreview }}{{ $founderOthersCount ? ' and '.$founderOthersCount.' others' : '' }}</p>
                                    </div>
                                </div>
                            @endif
                            <x-ui.badge :variant="$statusVariant">{{ $status }}</x-ui.badge>
                            <x-ui.badge variant="info">{{ $startup->dpiit_recognized ? 'DPIIT Recognised' : 'Not DPIIT Recognised' }}</x-ui.badge>
                        </div>
                        <p class="mt-3 max-w-3xl text-sm text-white/70">{{ $description }}</p>
                        <div class="mt-4 flex flex-wrap items-center gap-2 text-sm text-white/75">
                            <span class="rounded-full bg-white/10 px-3 py-1">{{ $sectorName }}</span>
                            <span class="rounded-full bg-white/10 px-3 py-1">{{ $stateName }}</span>
                            <span class="rounded-full bg-white/10 px-3 py-1">{{ $fundingStage }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    @if ($website)
                        <x-ui.button href="{{ $website }}" variant="secondary">Website</x-ui.button>
                    @endif
                    <x-ui.button href="{{ route('startups.edit', ['startup' => $startup->id]) }}">Edit startup</x-ui.button>
                </div>
            </div>
        </div>

        <div class="grid gap-4 border-t border-slate-200 p-6 dark:border-slate-800 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl bg-slate-50 p-4 dark:bg-slate-900">
                <p class="text-sm text-slate-500 dark:text-slate-400">Total funding</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">{{ $formatMoney($startup->total_funding_usd, 'USD') }}</p>
            </div>
            <div class="rounded-3xl bg-slate-50 p-4 dark:bg-slate-900">
                <p class="text-sm text-slate-500 dark:text-slate-400">Employees</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">{{ $employeeCount }}</p>
            </div>
            <div class="rounded-3xl bg-slate-50 p-4 dark:bg-slate-900">
                <p class="text-sm text-slate-500 dark:text-slate-400">Founded</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">{{ $foundedYear }}</p>
            </div>
            <div class="rounded-3xl bg-slate-50 p-4 dark:bg-slate-900">
                <p class="text-sm text-slate-500 dark:text-slate-400">Jobs created</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">{{ $jobsCreated }}</p>
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <x-ui.card class="xl:col-span-2">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Funding history</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Funding rounds loaded from the startup record and its related history.</p>
                </div>
            </div>

            <div class="mt-6 overflow-hidden">
                <x-ui.table>
                    <thead class="table-head">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Date</th>
                            <th class="px-6 py-4 font-semibold">Round</th>
                            <th class="px-6 py-4 font-semibold">Amount</th>
                            <th class="px-6 py-4 font-semibold">Lead investor</th>
                            <th class="px-6 py-4 font-semibold">Valuation after round</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse ($fundingRounds as $round)
                            <tr>
                                <td class="table-cell">{{ $formatDate($round->funding_date) }}</td>
                                <td class="table-cell font-medium text-slate-900 dark:text-white">{{ $round->round_type ?: 'Not available' }}</td>
                                <td class="table-cell">{{ $formatMoney($round->amount_raised_usd, $round->currency ?: 'USD') }}</td>
                                <td class="table-cell">{{ $round->lead_investor ? 'Yes' : 'No' }}</td>
                                <td class="table-cell">{{ $formatMoney($round->valuation_after_round_usd, $round->currency ?: 'USD') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="table-cell" colspan="5">No funding rounds have been added yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-ui.table>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Company details</h3>
            <div class="mt-5 space-y-4">
                <div class="space-y-3 rounded-3xl border border-slate-200 p-4 dark:border-slate-800">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Registration number</p>
                            <p class="mt-1 font-medium text-slate-900 dark:text-white">{{ $startup->registration_number ?: 'Not available' }}</p>
                        </div>
                        <x-ui.badge :variant="$startup->dpiit_recognized ? 'success' : 'secondary'">{{ $startup->dpiit_recognized ? 'DPIIT Yes' : 'DPIIT No' }}</x-ui.badge>
                    </div>

                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Sector</p>
                        <p class="mt-1 font-medium text-slate-900 dark:text-white">{{ $sectorName }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">State / City</p>
                        <p class="mt-1 font-medium text-slate-900 dark:text-white">{{ $stateName }}{{ $startup->city ? ' · '.$startup->city : '' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Funding stage</p>
                        <p class="mt-1 font-medium text-slate-900 dark:text-white">{{ $fundingStage }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Contact</p>
                        <div class="mt-1 space-y-1 text-sm text-slate-900 dark:text-white">
                            <p>{{ $email ?: 'Email not available' }}</p>
                            <p>{{ $phone ?: 'Phone not available' }}</p>
                            <p>{{ $linkedinUrl ?: 'LinkedIn not available' }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Tags</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @forelse ($tags as $tag)
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700 dark:bg-slate-900 dark:text-slate-200">{{ $tag }}</span>
                            @empty
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700 dark:bg-slate-900 dark:text-slate-200">No tags added</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </x-ui.card>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <x-ui.card>
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Founders</h3>
            <div class="mt-5 space-y-4">
                <div class="rounded-3xl border border-slate-200 p-4 dark:border-slate-800">
                    <p class="font-medium text-slate-900 dark:text-white">Founder count</p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $founderCount }}</p>
                </div>

                @forelse ($founders as $founder)
                    <div class="rounded-3xl border border-slate-200 p-4 dark:border-slate-800">
                        <div class="flex items-start gap-4">
                            <div class="shrink-0">
                                <div class="h-12 w-12 flex items-center justify-center rounded-full bg-indigo-600 text-white font-semibold">{{ mb_strtoupper(mb_substr($founder->full_name, 0, 1)) }}</div>
                            </div>
                            <div class="flex-1">
                                <p class="text-lg font-semibold text-slate-900 dark:text-white">{{ $founder->full_name }}</p>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $founder->education ?: 'Founder profile not fully completed' }}</p>
                                <div class="mt-3 space-y-1 text-sm text-slate-600 dark:text-slate-300">
                                    <p>{{ $founder->email ?: 'Email not available' }}</p>
                                    <p>{{ $founder->phone ?: 'Phone not available' }}</p>
                                    <p>{{ $founder->experience_years ? $founder->experience_years.' years experience' : 'Experience not listed' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-300 p-4 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
                        No founders have been linked to this startup yet.
                    </div>
                @endforelse
            </div>
        </x-ui.card>

        <x-ui.card class="xl:col-span-2">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Activity timeline</h3>
            <div class="mt-5 space-y-4">
                @forelse ($timeline as $item)
                    <div class="rounded-3xl border border-slate-200 p-4 dark:border-slate-800">
                        <div class="flex items-center justify-between gap-3">
                            <p class="font-medium text-slate-900 dark:text-white">{{ $item->title ?: 'Update' }}</p>
                            <span class="text-sm text-slate-500 dark:text-slate-400">{{ $formatDate($item->update_date) }}</span>
                        </div>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $item->description ?: 'No description available.' }}</p>
                        <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                            <span class="rounded-full bg-slate-100 px-3 py-1 dark:bg-slate-900">{{ $item->update_type ?: 'General update' }}</span>
                            @if ($item->is_published !== null)
                                <span class="rounded-full bg-slate-100 px-3 py-1 dark:bg-slate-900">{{ $item->is_published ? 'Published' : 'Draft' }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-300 p-4 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
                        No timeline updates have been recorded yet.
                    </div>
                @endforelse
            </div>
        </x-ui.card>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <x-ui.card>
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Milestones</h3>
            <div class="mt-5 space-y-4">
                <div class="flex items-start gap-4">
                    <div class="mt-1 h-3 w-3 rounded-full bg-indigo-500"></div>
                    <div>
                        <p class="font-medium text-slate-900 dark:text-white">Founded</p>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $foundedYear }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="mt-1 h-3 w-3 rounded-full bg-indigo-500"></div>
                    <div>
                        <p class="font-medium text-slate-900 dark:text-white">DPIIT status</p>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $startup->dpiit_recognized ? 'Recognised' : 'Not recognised' }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="mt-1 h-3 w-3 rounded-full bg-indigo-500"></div>
                    <div>
                        <p class="font-medium text-slate-900 dark:text-white">Latest funding</p>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $formatDate($startup->last_funding_date) }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="mt-1 h-3 w-3 rounded-full bg-indigo-500"></div>
                    <div>
                        <p class="font-medium text-slate-900 dark:text-white">Founder count</p>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $founderCount }}</p>
                    </div>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card class="xl:col-span-2">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Business snapshot</h3>
            <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <div class="rounded-3xl bg-slate-50 p-4 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Annual revenue</p>
                    <p class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ $formatMoney($startup->annual_revenue_inr, 'INR') }}</p>
                </div>
                <div class="rounded-3xl bg-slate-50 p-4 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Valuation</p>
                    <p class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ $formatMoney($startup->valuation_usd, 'USD') }}</p>
                </div>
                <div class="rounded-3xl bg-slate-50 p-4 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Growth</p>
                    <p class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ $startup->growth_percentage !== null ? number_format((float) $startup->growth_percentage, 2).'%' : 'Not available' }}</p>
                </div>
                <div class="rounded-3xl bg-slate-50 p-4 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Women led</p>
                    <p class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ $startup->women_led ? 'Yes' : 'No' }}</p>
                </div>
                <div class="rounded-3xl bg-slate-50 p-4 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">AI enabled</p>
                    <p class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ $startup->ai_enabled ? 'Yes' : 'No' }}</p>
                </div>
                <div class="rounded-3xl bg-slate-50 p-4 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Export business</p>
                    <p class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ $startup->export_business ? 'Yes' : 'No' }}</p>
                </div>
            </div>
        </x-ui.card>
    </div>
</section>
@endsection
