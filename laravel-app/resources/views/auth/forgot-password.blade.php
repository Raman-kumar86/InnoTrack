@extends('layouts.auth')

@section('content')
<div class="space-y-6">
    <div class="text-center">
        <p class="text-sm font-semibold uppercase tracking-[0.35em] text-indigo-300">Password help</p>
        <h1 class="mt-3 text-3xl font-semibold tracking-tight text-white">Reset your password</h1>
        <p class="mt-3 text-sm text-slate-300">Enter your official email and we will send a secure reset link.</p>
    </div>

    <form class="space-y-4">
        <x-ui.auth-field name="email" label="Official email" type="email" value="admin@startupindia.gov.in" autocomplete="email" error="We could not verify this address in the workspace registry." />
        <x-ui.button type="submit" class="w-full justify-center">Send reset link</x-ui.button>
    </form>

    <p class="text-center text-sm text-slate-300">
        Back to sign in? <a href="{{ route('auth.login') }}" class="font-medium text-indigo-300 transition hover:text-indigo-200">Return to login</a>
    </p>
</div>
@endsection
