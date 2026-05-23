@extends('layouts.auth')

@section('content')
<div class="space-y-6">
    <div class="text-center">
        <p class="text-sm font-semibold uppercase tracking-[0.35em] text-indigo-300">Create account</p>
        <h1 class="mt-3 text-3xl font-semibold tracking-tight text-white">Register a new workspace user</h1>
        <p class="mt-3 text-sm text-slate-300">Provision an account for ministry staff, analysts, or reporting officers.</p>
    </div>

    <form class="space-y-4">
        <div class="grid gap-4 sm:grid-cols-2">
            <x-ui.auth-field name="first_name" label="First name" value="Aarav" autocomplete="given-name" />
            <x-ui.auth-field name="last_name" label="Last name" value="Sharma" autocomplete="family-name" />
        </div>
        <x-ui.auth-field name="email" label="Official email" type="email" value="aarav@startupindia.gov.in" autocomplete="email" />
        <x-ui.auth-field name="password" label="Password" type="password" autocomplete="new-password" toggle="true" error="Use at least 12 characters with a number and symbol." />
        <x-ui.auth-field name="password_confirmation" label="Confirm password" type="password" autocomplete="new-password" toggle="true" />

        <label class="flex items-start gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-slate-300 backdrop-blur">
            <input type="checkbox" class="mt-1 h-4 w-4 rounded border-slate-600 bg-slate-900 text-indigo-500 focus:ring-indigo-500" checked>
            <span>
                I agree to the platform access policy and the handling of registry data under official use guidelines.
            </span>
        </label>

        <x-ui.button type="submit" class="w-full justify-center">Create account</x-ui.button>
    </form>

    <p class="text-center text-sm text-slate-300">
        Already have an account? <a href="{{ route('auth.login') }}" class="font-medium text-indigo-300 transition hover:text-indigo-200">Sign in</a>
    </p>
</div>
@endsection
