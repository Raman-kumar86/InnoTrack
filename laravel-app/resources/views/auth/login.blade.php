@extends('layouts.auth')

@section('content')
<div class="space-y-6">
    <div class="text-center">
        <p class="text-sm font-semibold uppercase tracking-[0.35em] text-indigo-300">Secure access</p>
        <h1 class="mt-3 text-3xl font-semibold tracking-tight text-white">Sign in to the dashboard</h1>
        <p class="mt-3 text-sm text-slate-300">Access government analytics, approval workflows, and startup intelligence in one secure workspace.</p>
    </div>

    <form class="space-y-4">
        <x-ui.auth-field name="email" label="Official email" type="email" value="admin@startupindia.gov.in" autocomplete="email" error="Please enter a valid government email address." />
        <x-ui.auth-field name="password" label="Password" type="password" autocomplete="current-password" toggle="true" />

        <div class="flex items-center justify-between gap-3 text-sm">
            <label class="flex items-center gap-2 text-slate-300">
                <input type="checkbox" class="h-4 w-4 rounded border-slate-600 bg-slate-900 text-indigo-500 focus:ring-indigo-500" checked>
                Remember me
            </label>
            <a href="{{ route('auth.forgot-password') }}" class="font-medium text-indigo-300 transition hover:text-indigo-200">Forgot password?</a>
        </div>

        <x-ui.button type="submit" class="w-full justify-center">Sign in</x-ui.button>
    </form>

    <div class="rounded-3xl border border-white/10 bg-white/5 p-4 text-sm text-slate-300 backdrop-blur">
        This portal is reserved for authorized public sector and ecosystem operations users.
    </div>

    <p class="text-center text-sm text-slate-300">
        New user? <a href="{{ route('auth.register') }}" class="font-medium text-indigo-300 transition hover:text-indigo-200">Create an account</a>
    </p>
</div>
@endsection
