@extends('layouts.auth')

@section('content')
@php($formErrors = isset($errors) ? $errors : new \Illuminate\Support\ViewErrorBag())
<div class="space-y-6">
    <div class="text-center">
        <p class="text-sm font-semibold uppercase tracking-[0.35em] text-indigo-300">Create account</p>
        <h1 class="mt-3 text-3xl font-semibold tracking-tight text-white">Register a new workspace user</h1>
        <p class="mt-3 text-sm text-slate-300">Viewer is the default role. Choose state officer if you need state access.</p>
    </div>

    <form class="space-y-4" method="POST" action="{{ route('auth.register.store') }}">
        @csrf
        <div class="grid gap-4 sm:grid-cols-2">
            <x-ui.auth-field name="first_name" label="First name" value="{{ old('first_name') }}" autocomplete="given-name" :error="$formErrors->first('first_name')" />
            <x-ui.auth-field name="last_name" label="Last name" value="{{ old('last_name') }}" autocomplete="family-name" :error="$formErrors->first('last_name')" />
        </div>
        <x-ui.auth-field name="email" label="Email address" type="email" value="{{ old('email') }}" autocomplete="email" :error="$formErrors->first('email')" />
        <x-ui.select-field name="role" label="Account role" :error="$formErrors->first('role')">
            <option value="viewer" @selected(old('role', 'viewer') === 'viewer')>Viewer</option>
            <option value="state_officer" @selected(old('role') === 'state_officer')>State officer</option>
        </x-ui.select-field>
        <x-ui.auth-field name="state" label="State / Territory" value="{{ old('state') }}" autocomplete="address-level1" :error="$formErrors->first('state')" />
        <x-ui.auth-field name="password" label="Password" type="password" autocomplete="new-password" toggle="true" :error="$formErrors->first('password')" />
        <x-ui.auth-field name="password_confirmation" label="Confirm password" type="password" autocomplete="new-password" toggle="true" :error="$formErrors->first('password_confirmation')" />

        <label class="flex items-start gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-slate-300 backdrop-blur">
            <input name="terms" type="checkbox" class="mt-1 h-4 w-4 rounded border-slate-600 bg-slate-900 text-indigo-500 focus:ring-indigo-500" {{ old('terms') ? 'checked' : '' }}>
            <span>
                I agree to the platform access policy and the handling of registry data under official use guidelines.
            </span>
        </label>

        @if ($formErrors->has('terms'))
            <p class="text-xs font-medium text-rose-500">{{ $formErrors->first('terms') }}</p>
        @endif

        <x-ui.button type="submit" class="w-full justify-center">Create account</x-ui.button>
    </form>

    <p class="text-center text-sm text-slate-300">
        Already have an account? <a href="{{ route('auth.login') }}" class="font-medium text-indigo-300 transition hover:text-indigo-200">Sign in</a>
    </p>
</div>
@endsection
