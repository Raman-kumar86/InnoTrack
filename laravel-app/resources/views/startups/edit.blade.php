@extends('layouts.app')

@php
    $title = 'Edit Startup';
    $pageTitle = 'Edit Startup';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Startups', 'url' => route('startups.index')],
        ['label' => $startup->startup_name ?: 'Edit Startup', 'url' => route('startups.edit', ['startup' => $startup->id])],
    ];
@endphp

@section('content')
<section class="space-y-6">
    <x-ui.section-header
        title="Edit startup"
        subtitle="Update metadata, recognition status, and public profile details with a premium admin form layout."
    >
            <x-ui.button href="{{ route('startups.show', ['startup' => $startup->id]) }}" variant="secondary">Preview profile</x-ui.button>
            <x-ui.button type="submit" form="startup-edit-form">Save changes</x-ui.button>
    </x-ui.section-header>

        <form id="startup-edit-form" class="space-y-6" method="POST" action="{{ route('startups.update', ['startup' => $startup->id]) }}">
            @csrf
            @method('PATCH')
        <x-ui.card>
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                <x-ui.form-field label="Startup name">
                        <input name="startup_name" type="text" class="input-modern" value="{{ old('startup_name', $startup->startup_name) }}" required />
                </x-ui.form-field>
                
                <x-ui.form-field label="Official website">
                        <input name="website" type="url" class="input-modern" value="{{ old('website', $startup->website) }}" />
                </x-ui.form-field>
                <x-ui.form-field label="Registration number">
                        <input name="registration_number" type="text" class="input-modern" value="{{ old('registration_number', $startup->registration_number) }}" />
                </x-ui.form-field>
                <x-ui.select-field label="Sector">
                        <select name="sector_id" class="select-modern">
                            <option value="">Select sector</option>
                            @foreach($sectors as $sector)
                                <option value="{{ $sector->id }}" @selected((string) old('sector_id', (string) $startup->sector_id) === (string) $sector->id)>{{ $sector->sector_name }}</option>
                            @endforeach
                        </select>
                </x-ui.select-field>
                <x-ui.select-field label="State">
                        <select name="state_id" class="select-modern">
                            <option value="">Select state</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}" @selected((string) old('state_id', (string) $startup->state_id) === (string) $state->id)>{{ $state->state_name }}</option>
                            @endforeach
                        </select>
                </x-ui.select-field>
                <x-ui.select-field label="Stage">
                        <select name="funding_stage" class="select-modern">
                            <option value="">Select stage</option>
                            @foreach($fundingStages as $stage)
                                <option value="{{ $stage }}" @selected(old('funding_stage', $startup->funding_stage) === $stage)>{{ $stage }}</option>
                            @endforeach
                        </select>
                </x-ui.select-field>
            </div>
        </x-ui.card>

        <x-ui.card>
            <div class="grid gap-5 lg:grid-cols-2">
                <x-ui.form-field label="Primary founder">
                    <input name="primary_founder" type="text" class="input-modern" value="{{ old('primary_founder', $startup->founders->first()?->full_name ?? '') }}" />
                </x-ui.form-field>
                <x-ui.form-field label="Founder email">
                    <input name="primary_founder_email" type="email" class="input-modern" value="{{ old('primary_founder_email', $startup->founders->first()?->email ?? '') }}" />
                </x-ui.form-field>
                <x-ui.form-field label="Short description" class="lg:col-span-2">
                    <textarea name="description" class="textarea-modern">{{ old('description', $startup->description) }}</textarea>
                </x-ui.form-field>
                <x-ui.form-field label="Status">
                    <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-slate-800 dark:bg-slate-900">
                        <div>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">Public listing enabled</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Visible to ministry and state teams</p>
                        </div>
                        <input name="public_listing" type="checkbox" value="1" class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" @checked(old('public_listing', $startup->public_listing ?? false))>
                    </div>
                </x-ui.form-field>
                <x-ui.form-field label="Recognition status">
                    <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-slate-800 dark:bg-slate-900">
                        <div>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">DPIIT recognised</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Eligibility verified against registry</p>
                        </div>
                        <input name="dpiit_recognized" type="checkbox" value="1" class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" @checked(old('dpiit_recognized', $startup->dpiit_recognized ?? false))>
                    </div>
                </x-ui.form-field>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h3 class="mb-3 text-sm font-semibold text-slate-700">Founders</h3>
            <div id="founders-list" class="space-y-3" data-next-index="{{ $startup->founders->count() }}">
                @foreach($startup->founders->sortBy('full_name')->values() as $i => $founder)
                    <div class="founder-row flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-3">
                        <input type="hidden" name="founders[{{ $i }}][id]" value="{{ $founder->id }}">
                        <input type="hidden" name="founders[{{ $i }}][_destroy]" value="0" class="founder-destroy">
                        <div class="flex-1">
                            <x-ui.form-field label="Full name">
                                <input name="founders[{{ $i }}][full_name]" type="text" class="input-modern" value="{{ old('founders.'.$i.'.full_name', $founder->full_name) }}" />
                            </x-ui.form-field>
                            <x-ui.form-field label="Email">
                                <input name="founders[{{ $i }}][email]" type="email" class="input-modern" value="{{ old('founders.'.$i.'.email', $founder->email) }}" />
                            </x-ui.form-field>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <button type="button" class="text-sm text-rose-600 remove-founder" data-remove-idx="{{ $i }}">Remove</button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-3">
                <x-ui.button type="button" variant="secondary" id="add-founder">Add founder</x-ui.button>
            </div>

            <template id="founder-template">
                <div class="founder-row flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-3">
                    <input type="hidden" name="founders[__IDX__][id]" value="">
                    <input type="hidden" name="founders[__IDX__][_destroy]" value="0" class="founder-destroy">
                    <div class="flex-1">
                        <x-ui.form-field label="Full name">
                            <input name="founders[__IDX__][full_name]" type="text" class="input-modern" value="" />
                        </x-ui.form-field>
                        <x-ui.form-field label="Email">
                            <input name="founders[__IDX__][email]" type="email" class="input-modern" value="" />
                        </x-ui.form-field>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <button type="button" class="text-sm text-rose-600 remove-founder" data-remove-idx="__IDX__">Remove</button>
                    </div>
                </div>
            </template>
        </x-ui.card>

        <x-ui.card>
            <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                <x-ui.form-field label="Contact email">
                    <input name="email" type="email" class="input-modern" value="{{ old('email', $startup->email) }}" />
                </x-ui.form-field>
                <x-ui.form-field label="Phone">
                    <input name="phone" type="text" class="input-modern" value="{{ old('phone', $startup->phone) }}" />
                </x-ui.form-field>
                <x-ui.form-field label="LinkedIn URL">
                    <input name="linkedin_url" type="url" class="input-modern" value="{{ old('linkedin_url', $startup->linkedin_url) }}" />
                </x-ui.form-field>
                <x-ui.form-field label="Founded year">
                    <input name="founded_year" type="number" class="input-modern" value="{{ old('founded_year', $startup->founded_year) }}" />
                </x-ui.form-field>
                <x-ui.form-field label="Founder count">
                    <input name="founder_count" type="number" class="input-modern" value="{{ old('founder_count', $startup->founder_count) }}" />
                </x-ui.form-field>
                <x-ui.form-field label="Employees">
                    <input name="employee_count" type="number" class="input-modern" value="{{ old('employee_count', $startup->employee_count) }}" />
                </x-ui.form-field>
                <x-ui.form-field label="Total funding (Rs.)">
                    <input name="total_funding_usd" type="text" class="input-modern" value="{{ old('total_funding_usd', $startup->total_funding_usd) }}" />
                </x-ui.form-field>
                <x-ui.form-field label="Valuation (Rs.)">
                    <input name="valuation_usd" type="text" class="input-modern" value="{{ old('valuation_usd', $startup->valuation_usd) }}" />
                </x-ui.form-field>
                <x-ui.form-field label="Annual revenue (Rs.)">
                    <input name="annual_revenue_inr" type="text" class="input-modern" value="{{ old('annual_revenue_inr', $startup->annual_revenue_inr) }}" />
                </x-ui.form-field>
                <x-ui.form-field label="Jobs created">
                    <input name="jobs_created" type="number" class="input-modern" value="{{ old('jobs_created', $startup->jobs_created) }}" />
                </x-ui.form-field>
                <x-ui.form-field label="Patents filed">
                    <input name="patents_filed" type="number" class="input-modern" value="{{ old('patents_filed', $startup->patents_filed) }}" />
                </x-ui.form-field>
                <x-ui.form-field label="City">
                    <input name="city" type="text" class="input-modern" value="{{ old('city', $startup->city) }}" />
                </x-ui.form-field>
                <x-ui.form-field label="Status">
                    <input name="status" type="text" class="input-modern" value="{{ old('status', $startup->status) }}" />
                </x-ui.form-field>
                <x-ui.form-field label="Women led">
                    <input name="women_led" type="checkbox" value="1" class="h-5 w-5 rounded border-slate-300 text-indigo-600" @checked(old('women_led', $startup->women_led ?? false)) />
                </x-ui.form-field>
                <x-ui.form-field label="AI enabled">
                    <input name="ai_enabled" type="checkbox" value="1" class="h-5 w-5 rounded border-slate-300 text-indigo-600" @checked(old('ai_enabled', $startup->ai_enabled ?? false)) />
                </x-ui.form-field>
                <x-ui.form-field label="Sustainability focus">
                    <input name="sustainability_focus" type="checkbox" value="1" class="h-5 w-5 rounded border-slate-300 text-indigo-600" @checked(old('sustainability_focus', $startup->sustainability_focus ?? false)) />
                </x-ui.form-field>
                <x-ui.form-field label="Export business">
                    <input name="export_business" type="checkbox" value="1" class="h-5 w-5 rounded border-slate-300 text-indigo-600" @checked(old('export_business', $startup->export_business ?? false)) />
                </x-ui.form-field>
            </div>
        </x-ui.card>

        <div class="sticky bottom-4 z-20 flex justify-end">
            <div class="surface-card flex items-center gap-3 px-4 py-3 shadow-2xl shadow-slate-950/10">
                <x-ui.button href="{{ route('startups.show', ['startup' => $startup->id]) }}" variant="secondary">Cancel</x-ui.button>
                <x-ui.button type="submit">Update startup</x-ui.button>
            </div>
        </div>
    </form>
</section>
@push('scripts')
    <script>
        // Simple founders add/remove helper (no Blade tokens inside script)
        (function () {
            var listEl = document.getElementById('founders-list');
            var nextFounderIndex = 0;
            if (listEl && listEl.dataset && listEl.dataset.nextIndex) {
                nextFounderIndex = parseInt(listEl.dataset.nextIndex, 10) || 0;
            }

            window.addEventListener('DOMContentLoaded', function () {
                var addBtn = document.getElementById('add-founder');
                var list = listEl;
                var tplEl = document.getElementById('founder-template');
                var tpl = '';
                if (tplEl && tplEl.innerHTML) tpl = tplEl.innerHTML;

                window.removeFounder = function (idx) {
                    try {
                        var destroySelector = "input[name='founders[" + idx + "][ _destroy]']".replace("[ _destroy]", "[_destroy]");
                        var destroyInput = document.querySelector(destroySelector);
                        var fullInput = document.querySelector("input[name='founders[" + idx + "][full_name]']");

                        var row = null;
                        if (fullInput) {
                            var el = fullInput;
                            while (el && !el.classList.contains('founder-row')) el = el.parentElement;
                            row = el;
                        }

                        if (destroyInput) {
                            destroyInput.value = '1';
                            if (row) row.style.display = 'none';
                            return;
                        }

                        var selector = "#founders-list .founder-row[data-new-idx='" + idx + "']";
                        var newRow = document.querySelector(selector);
                        if (newRow && newRow.parentNode) newRow.parentNode.removeChild(newRow);
                    } catch (e) {
                        console.error('removeFounder error', e);
                    }
                };

                if (addBtn && list) {
                    addBtn.addEventListener('click', function (e) {
                        var html = tpl.replace(/__IDX__/g, nextFounderIndex);
                        var wrapper = document.createElement('div');
                        wrapper.innerHTML = html;
                        var row = wrapper.firstElementChild;
                        if (row) {
                            row.setAttribute('data-new-idx', nextFounderIndex);
                            list.appendChild(row);
                        }
                        nextFounderIndex++;
                    });
                }

                // delegated handler for remove buttons
                if (list) {
                    list.addEventListener('click', function (e) {
                        var btn = e.target.closest && e.target.closest('.remove-founder');
                        if (!btn) return;
                        var idx = btn.getAttribute('data-remove-idx');
                        if (idx !== null) removeFounder(idx);
                    });
                }
            });
        }());
    </script>
@endpush

@endsection
