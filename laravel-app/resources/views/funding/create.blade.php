@extends('layouts.app')

@php
    $title = 'Add Funding Round';
    $pageTitle = 'Add Funding Round';
    $breadcrumbs = $breadcrumbs ?? [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Startups', 'url' => route('startups.index')],
        ['label' => 'Add Funding Round', 'url' => route('funding.create')],
    ];
@endphp

@section('content')
<section class="mx-auto max-w-5xl space-y-6" x-data="fundingForm()" x-init="init()">
    <x-ui.section-header
        title="Add funding round"
        subtitle="Record a new equity, grant, or debt round with a structured approval-ready form layout."
    >
        <x-ui.button href="{{ route('startups.index') }}" variant="secondary">
            <x-ui.icon name="arrow-left" class="h-4 w-4" />
            Back to Startups
        </x-ui.button>
    </x-ui.section-header>

    @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5 dark:border-rose-900 dark:bg-rose-950/40">
            <div class="flex items-start gap-3">
                <x-ui.icon name="alert-circle" class="mt-0.5 h-5 w-5 shrink-0 text-rose-500" />
                <div>
                    <p class="text-sm font-semibold text-rose-700 dark:text-rose-400">Please fix the following errors:</p>
                    <ul class="mt-2 list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="text-sm text-rose-600 dark:text-rose-400">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('funding.store') }}" enctype="multipart/form-data" id="funding-form" novalidate class="space-y-6">
        @csrf

        <x-ui.card>
            <div class="mb-6 space-y-1">
                <h2 class="text-base font-semibold text-slate-900 dark:text-white">Startup</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Select the startup this round belongs to.</p>
            </div>

            <div class="relative" x-data="startupSearch()">
                <input type="hidden" name="startup_id" x-model="selectedId" value="{{ old('startup_id', $preselectedStartup?->id) }}" />

                <x-ui.form-field label="Search startup" help="Search by startup name or registration number.">
                    <input
                        type="text"
                        class="input-modern"
                        placeholder="Type startup name or reg. code..."
                        x-model="query"
                        @input.debounce.300ms="search()"
                        @focus="open = true"
                    />
                </x-ui.form-field>

                <div
                    x-show="open && results.length > 0"
                    x-transition
                    class="absolute z-20 mt-1 w-full overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-800 dark:bg-slate-950"
                >
                    <template x-for="s in results" :key="s.id">
                        <button
                            type="button"
                            class="flex w-full flex-col border-b border-slate-100 px-4 py-3 text-left hover:bg-slate-50 last:border-0 dark:border-slate-800 dark:hover:bg-slate-800"
                            @click="select(s)"
                        >
                            <span class="text-sm font-semibold text-slate-900 dark:text-white" x-text="s.startup_name"></span>
                            <span class="mt-0.5 text-xs text-slate-400 dark:text-slate-500" x-text="s.registration_number + ' - ' + s.sector + ' - ' + s.state"></span>
                        </button>
                    </template>
                </div>

                <div
                    x-show="selectedStartup"
                    x-transition
                    class="mt-3 flex items-center justify-between rounded-2xl border border-indigo-200 bg-indigo-50/50 p-4 dark:border-indigo-900 dark:bg-indigo-950/30"
                >
                    <div>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white" x-text="selectedStartup?.startup_name"></p>
                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                            Current stage:
                            <span class="font-medium" x-text="selectedStartup?.current_stage ?? 'None'"></span>
                            - Total raised:
                            <span class="font-mono font-medium" x-text="'$' + Number(selectedStartup?.total_funding_usd ?? 0).toLocaleString()"></span>
                        </p>
                    </div>
                    <button type="button" @click="clearSelection()" class="text-slate-400 transition hover:text-rose-500">
                        <x-ui.icon name="x" class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card>
            <div class="mb-6 space-y-1">
                <h2 class="text-base font-semibold text-slate-900 dark:text-white">Round details</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Specify the type, status, and timing of this funding round.</p>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <x-ui.select-field label="Round type">
                    <select name="round_type" class="select-modern" x-model="roundType">
                        <option value="">Select round type</option>
                        @foreach ($roundTypes as $type)
                            <option value="{{ $type }}" @selected(old('round_type') === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                </x-ui.select-field>

                <x-ui.select-field label="Round status">
                    <select name="round_status" class="select-modern">
                        @foreach ($roundStatuses as $status)
                            <option value="{{ $status }}" @selected(old('round_status', 'Completed') === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </x-ui.select-field>

                <x-ui.form-field label="Funding date">
                    <input type="date" name="funding_date" class="input-modern" value="{{ old('funding_date', now()->format('Y-m-d')) }}" max="{{ now()->format('Y-m-d') }}" />
                </x-ui.form-field>

                <x-ui.form-field label="Expected close date" help="Optional. For in-progress rounds.">
                    <input type="date" name="expected_close_date" class="input-modern" value="{{ old('expected_close_date') }}" />
                </x-ui.form-field>
            </div>

            <div x-show="roundType === 'Debt'" x-transition class="mt-5">
                <x-ui.form-field label="Interest rate (%)">
                    <input type="number" name="interest_rate" class="input-modern" step="0.01" min="0" max="100" value="{{ old('interest_rate') }}" />
                </x-ui.form-field>
            </div>

            <div x-show="roundType === 'Grant'" x-transition class="mt-5">
                <x-ui.form-field label="Grant authority">
                    <input type="text" name="grant_authority" class="input-modern" value="{{ old('grant_authority') }}" />
                </x-ui.form-field>
            </div>

            <div x-show="roundType === 'Convertible Note'" x-transition class="mt-5 grid gap-5 sm:grid-cols-2">
                <x-ui.form-field label="Conversion cap (USD)">
                    <input type="number" name="conversion_cap" class="input-modern" min="0" value="{{ old('conversion_cap') }}" />
                </x-ui.form-field>
                <x-ui.form-field label="Discount rate (%)">
                    <input type="number" name="discount_rate" class="input-modern" min="0" max="100" step="0.01" value="{{ old('discount_rate') }}" />
                </x-ui.form-field>
            </div>
        </x-ui.card>

        <x-ui.card>
            <div class="mb-6 space-y-1">
                <h2 class="text-base font-semibold text-slate-900 dark:text-white">Financial details</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Enter the amount raised and valuation figures.</p>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <x-ui.select-field label="Currency">
                    <select name="currency" class="select-modern" x-model="currency" @change="onCurrencyChange()">
                        @foreach ($currencies as $code => $cur)
                            <option value="{{ $code }}" @selected(old('currency', 'USD') === $code)>{{ $cur['label'] }}</option>
                        @endforeach
                    </select>
                </x-ui.select-field>

                <div x-show="currency !== 'USD'" x-transition>
                    <x-ui.form-field label="Exchange rate to USD">
                        <input type="number" name="exchange_rate_to_usd" class="input-modern" step="0.0001" x-model="exchangeRate" @input="calculateUsd()" value="{{ old('exchange_rate_to_usd') }}" />
                    </x-ui.form-field>
                </div>

                <x-ui.form-field label="Amount raised">
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400" x-text="currencySymbol()"></span>
                        <input type="number" name="amount_raised" class="input-modern pl-8" min="1" step="1" x-model="amountRaised" @input="calculateUsd(); calculatePostMoney()" value="{{ old('amount_raised') }}" />
                    </div>
                </x-ui.form-field>

                <div x-show="currency !== 'USD' && amountUsd > 0" x-transition class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800/40">
                    <p class="text-xs uppercase tracking-wide text-slate-400">Equivalent in USD</p>
                    <p class="mt-1 font-mono text-xl font-bold text-slate-900 dark:text-white" x-text="'$' + Number(amountUsd).toLocaleString()"></p>
                </div>

                <x-ui.form-field label="Pre-money valuation (USD)">
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400">$</span>
                        <input type="number" name="pre_money_valuation_usd" class="input-modern pl-8" min="0" step="1" x-model="preMoney" @input="calculatePostMoney()" value="{{ old('pre_money_valuation_usd') }}" />
                    </div>
                </x-ui.form-field>

                <x-ui.form-field label="Post-money valuation (USD)">
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400">$</span>
                        <input type="number" name="valuation_after_round_usd" class="input-modern pl-8" min="1" step="1" x-model="postMoney" value="{{ old('valuation_after_round_usd') }}" />
                    </div>
                </x-ui.form-field>

                <div x-show="roundType !== 'Grant'" x-transition>
                    <x-ui.form-field label="Equity diluted (%)">
                        <input type="number" name="equity_diluted_percent" class="input-modern" min="0" max="100" step="0.01" value="{{ old('equity_diluted_percent') }}" />
                    </x-ui.form-field>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card>
            <div class="mb-6 space-y-1">
                <h2 class="text-base font-semibold text-slate-900 dark:text-white">Investor details</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Identify lead and co-investors for this round.</p>
            </div>

            <div class="mb-5 flex items-center gap-3">
                <button
                    type="button"
                    @click="newInvestor = !newInvestor"
                    class="relative inline-flex h-6 w-11 items-center rounded-full transition"
                    :class="newInvestor ? 'bg-indigo-600' : 'bg-slate-200 dark:bg-slate-700'"
                >
                    <span class="inline-block h-5 w-5 transform rounded-full bg-white transition" :class="newInvestor ? 'translate-x-5' : 'translate-x-1'"></span>
                </button>
                <span class="text-sm text-slate-700 dark:text-slate-300">Investor not in system - add new</span>
            </div>

            <div x-show="!newInvestor" x-transition>
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-ui.select-field label="Lead investor">
                        <select name="investor_id" class="select-modern">
                            <option value="">Select investor</option>
                            @foreach ($investors as $investor)
                                <option value="{{ $investor->id }}" @selected(old('investor_id') == $investor->id)>{{ $investor->investor_name }} ({{ $investor->investor_type }})</option>
                            @endforeach
                        </select>
                    </x-ui.select-field>

                    <x-ui.select-field label="Is lead investor?">
                        <select name="lead_investor" class="select-modern">
                            <option value="Yes" @selected(old('lead_investor', 'Yes') === 'Yes')>Yes</option>
                            <option value="No" @selected(old('lead_investor') === 'No')>No</option>
                        </select>
                    </x-ui.select-field>
                </div>

                <div class="mt-5">
                    <x-ui.select-field label="Co-investors" help="Hold Ctrl/Cmd to select multiple.">
                        <select name="co_investor_ids[]" class="select-modern" multiple size="5">
                            @foreach ($investors as $investor)
                                <option value="{{ $investor->id }}" @selected(in_array($investor->id, old('co_investor_ids', [])))>{{ $investor->investor_name }} ({{ $investor->investor_type }})</option>
                            @endforeach
                        </select>
                    </x-ui.select-field>
                </div>
            </div>

            <div x-show="newInvestor" x-transition class="grid gap-5 sm:grid-cols-2">
                <x-ui.form-field label="Investor name">
                    <input type="text" name="new_investor_name" class="input-modern" value="{{ old('new_investor_name') }}" />
                </x-ui.form-field>

                <x-ui.select-field label="Investor type">
                    <select name="new_investor_type" class="select-modern">
                        <option value="">Select type</option>
                        @foreach ($investorTypes as $type)
                            <option value="{{ $type }}" @selected(old('new_investor_type') === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                </x-ui.select-field>

                <x-ui.select-field label="Is lead investor?">
                    <select name="lead_investor" class="select-modern">
                        <option value="Yes" @selected(old('lead_investor', 'Yes') === 'Yes')>Yes</option>
                        <option value="No" @selected(old('lead_investor') === 'No')>No</option>
                    </select>
                </x-ui.select-field>
            </div>
        </x-ui.card>

        <x-ui.card>
            <div class="mb-6 space-y-1">
                <h2 class="text-base font-semibold text-slate-900 dark:text-white">Documents & notes</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Attach supporting docs and review remarks.</p>
            </div>

            <div class="space-y-5">
                <x-ui.form-field label="Term sheet (PDF)" help="Max 10MB. PDF only.">
                    <div
                        class="group relative flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-8 text-center transition hover:border-indigo-400 dark:border-slate-700 dark:bg-slate-800/30 dark:hover:border-indigo-600"
                        @dragover.prevent
                        @drop.prevent="handleDrop($event)"
                        @click="$refs.fileInput.click()"
                    >
                        <x-ui.icon name="upload-cloud" class="mb-3 h-8 w-8 text-slate-300 transition group-hover:text-indigo-400 dark:text-slate-600" />
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400"><span class="text-indigo-600 dark:text-indigo-400">Click to upload</span> or drag and drop</p>
                        <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">PDF up to 10MB</p>
                        <p x-show="fileName" x-text="fileName" class="mt-2 font-mono text-xs text-indigo-600 dark:text-indigo-400"></p>
                        <input type="file" name="term_sheet" accept=".pdf" class="hidden" x-ref="fileInput" @change="onFileChange($event)" />
                    </div>
                </x-ui.form-field>

                <x-ui.form-field label="Notes / remarks" help="Internal notes for approval review.">
                    <textarea name="notes" class="input-modern min-h-30 resize-y" maxlength="2000" x-on:input="notesCount = $el.value.length">{{ old('notes') }}</textarea>
                    <div class="mt-1 text-right text-xs text-slate-400"><span x-text="notesCount"></span>/2000</div>
                </x-ui.form-field>

                <div class="flex items-center justify-between rounded-2xl border border-slate-200 p-4 dark:border-slate-700">
                    <div>
                        <p class="text-sm font-medium text-slate-900 dark:text-white">Publicly announced</p>
                        <p class="mt-0.5 text-xs text-slate-400 dark:text-slate-500">Triggers a higher-priority notification.</p>
                    </div>
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="checkbox" name="is_publicly_announced" value="1" class="peer sr-only" {{ old('is_publicly_announced') ? 'checked' : '' }} />
                        <span class="h-6 w-11 rounded-full bg-slate-200 transition peer-checked:bg-indigo-600 dark:bg-slate-700"></span>
                        <span class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white transition peer-checked:translate-x-5"></span>
                    </label>
                </div>
            </div>
        </x-ui.card>

        <div class="sticky bottom-4 z-20 flex justify-end">
            <div class="surface-card flex items-center gap-3 px-4 py-3 shadow-2xl shadow-slate-950/10">
                <x-ui.button href="{{ route('startups.index') }}" variant="secondary">Cancel</x-ui.button>
                <button type="submit" name="action" value="draft" class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Save as draft</button>
                <x-ui.button type="submit" name="action" value="submit" x-bind:disabled="submitting" @click="submitting = true">
                    <span x-show="!submitting">Submit for approval</span>
                    <span x-show="submitting">Saving...</span>
                </x-ui.button>
            </div>
        </div>
    </form>
</section>
@endsection

@php
    $fundingFormState = [
        'roundType' => old('round_type', ''),
        'currency' => old('currency', 'USD'),
        'exchangeRate' => old('exchange_rate_to_usd', config('funding.exchange_rates.USD', 1)),
        'amountRaised' => (float) old('amount_raised', 0),
        'preMoney' => (float) old('pre_money_valuation_usd', 0),
        'postMoney' => (float) old('valuation_after_round_usd', 0),
        'newInvestor' => (bool) old('new_investor_name'),
        'notesCount' => strlen(old('notes', '')),
        'currencies' => config('funding.currencies', []),
        'exchangeRates' => config('funding.exchange_rates', []),
    ];

    $startupSearchState = [
        'query' => old('startup_id') ? '' : ($preselectedStartup?->startup_name ?? ''),
        'selectedId' => old('startup_id', $preselectedStartup?->id),
        'selectedStartup' => $preselectedStartup ? [
            'id' => $preselectedStartup->id,
            'startup_name' => $preselectedStartup->startup_name,
            'registration_number' => $preselectedStartup->registration_number,
            'sector' => $preselectedStartup->sector?->sector_name ?? '',
            'state' => $preselectedStartup->state?->state_name ?? '',
            'current_stage' => $preselectedStartup->funding_stage,
            'total_funding_usd' => $preselectedStartup->total_funding_usd,
        ] : null,
    ];
@endphp

@push('scripts')
<script type="application/json" id="funding-form-state">{!! json_encode($fundingFormState) !!}</script>
<script type="application/json" id="startup-search-state">{!! json_encode($startupSearchState) !!}</script>
<script>
    function readJsonPayload(id, fallback) {
        const element = document.getElementById(id);
        if (!element) {
            return fallback;
        }

        try {
            return JSON.parse(element.textContent || 'null') ?? fallback;
        } catch (error) {
            return fallback;
        }
    }

    const fundingPageState = readJsonPayload('funding-form-state', {});
    const startupPageState = readJsonPayload('startup-search-state', {});

    function fundingForm() {
        return {
            roundType: fundingPageState.roundType ?? '',
            currency: fundingPageState.currency ?? 'USD',
            exchangeRate: fundingPageState.exchangeRate ?? 1,
            amountRaised: fundingPageState.amountRaised ?? 0,
            amountUsd: 0,
            preMoney: fundingPageState.preMoney ?? 0,
            postMoney: fundingPageState.postMoney ?? 0,
            newInvestor: Boolean(fundingPageState.newInvestor),
            fileName: '',
            notesCount: fundingPageState.notesCount ?? 0,
            submitting: false,
            exchangeRates: fundingPageState.exchangeRates ?? {},
            currencies: fundingPageState.currencies ?? {},

            init() {
                this.onCurrencyChange();
                this.calculateUsd();
                this.calculatePostMoney();
            },

            onCurrencyChange() {
                if (!this.exchangeRate || this.currency === 'USD') {
                    this.exchangeRate = this.exchangeRates[this.currency] || 1;
                }
                this.calculateUsd();
            },

            currencySymbol() {
                const currency = this.currencies[this.currency] || {};
                return currency.symbol || '$';
            },

            calculateUsd() {
                if (this.currency === 'USD') {
                    this.amountUsd = parseFloat(this.amountRaised) || 0;
                    return;
                }
                const rate = parseFloat(this.exchangeRate) || 1;
                this.amountUsd = Math.round((parseFloat(this.amountRaised) || 0) / rate);
            },

            calculatePostMoney() {
                const pre = parseFloat(this.preMoney) || 0;
                const amt = parseFloat(this.amountUsd) || 0;
                if (pre > 0 && amt > 0 && (!this.postMoney || this.postMoney < pre)) {
                    this.postMoney = pre + amt;
                }
            },

            onFileChange(event) {
                const file = event.target.files[0];
                this.fileName = file ? file.name : '';
            },

            handleDrop(event) {
                const file = event.dataTransfer.files[0];
                if (file && file.type === 'application/pdf') {
                    this.$refs.fileInput.files = event.dataTransfer.files;
                    this.fileName = file.name;
                }
            },
        };
    }

    function startupSearch() {
        return {
            query: startupPageState.query ?? '',
            results: [],
            open: false,
            selectedId: startupPageState.selectedId ?? null,
            selectedStartup: startupPageState.selectedStartup ?? null,

            async search() {
                if (!this.query || this.query.length < 2) {
                    this.results = [];
                    return;
                }
                const res = await fetch('/api/startups/search?q=' + encodeURIComponent(this.query));
                this.results = await res.json();
                this.open = true;
            },

            select(startup) {
                this.selectedId = startup.id;
                this.selectedStartup = startup;
                this.query = startup.startup_name;
                this.results = [];
                this.open = false;
            },

            clearSelection() {
                this.selectedId = null;
                this.selectedStartup = null;
                this.query = '';
                this.results = [];
            },
        };
    }
</script>
@endpush
