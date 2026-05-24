@extends('layouts.auth')

@section('content')
@php($formErrors = isset($errors) ? $errors : new \Illuminate\Support\ViewErrorBag())
<div class="space-y-6">
    <div class="text-center">
        <p class="text-sm font-semibold uppercase tracking-[0.35em] text-indigo-300">Secure reset</p>
        <h1 class="mt-3 text-3xl font-semibold tracking-tight text-white">Choose a new password</h1>
        <p class="mt-3 text-sm text-slate-300">Set a fresh password and return to the dashboard immediately.</p>
    </div>

    <form class="space-y-4" method="POST" action="{{ route('auth.reset-password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <x-ui.auth-field name="email" label="Official email" type="email" value="{{ old('email', $email) }}" autocomplete="email" :error="$formErrors->first('email')" />
        <x-ui.auth-field name="otp" label="One-time code (sent by email)" type="text" value="" autocomplete="one-time-code" :error="$formErrors->first('otp')" />
        <x-ui.auth-field name="password" label="New password" type="password" autocomplete="new-password" toggle="true" :error="$formErrors->first('password')" />
        <x-ui.auth-field name="password_confirmation" label="Confirm password" type="password" autocomplete="new-password" toggle="true" />

        <x-ui.button type="submit" class="w-full justify-center">Reset password</x-ui.button>
    </form>

    <div class="rounded-3xl border border-white/10 bg-white/5 p-4 text-sm text-slate-300 backdrop-blur">
        After confirmation, your session will be refreshed and all active devices will be logged out.
    </div>
</div>
@endsection
