@extends('layouts.auth')

@section('content')
@php($formErrors = isset($errors) ? $errors : new \Illuminate\Support\ViewErrorBag())
<div class="space-y-6">
    <div class="text-center">
        <p class="text-sm font-semibold uppercase tracking-[0.35em] text-indigo-300">Password help</p>
        <h1 class="mt-3 text-3xl font-semibold tracking-tight text-white">Reset your password</h1>
        <p class="mt-3 text-sm text-slate-300">Enter your official email and we will send a secure reset link through EmailJS.</p>
    </div>

    <form id="forgot-password-form" class="space-y-4" method="POST" action="{{ route('auth.forgot-password.send') }}">
        @csrf
        <x-ui.auth-field name="email" label="Official email" type="email" value="{{ old('email') }}" autocomplete="email" :error="$formErrors->first('email')" />
        <x-ui.button type="submit" class="w-full justify-center">Send reset link</x-ui.button>
    </form>

    <div data-forgot-password-message class="hidden rounded-3xl border border-white/10 bg-white/5 px-4 py-3 text-sm backdrop-blur"></div>

    <p class="text-center text-sm text-slate-300">
        Back to sign in? <a href="{{ route('auth.login') }}" class="font-medium text-indigo-300 transition hover:text-indigo-200">Return to login</a>
    </p>
</div>

@push('scripts')
    <script>
        (function () {
            const form = document.getElementById('forgot-password-form');
            const message = document.querySelector('[data-forgot-password-message]');
            const submitButton = form?.querySelector('button[type="submit"]');

            function showMessage(text, type) {
                if (!message) return;

                message.textContent = text;
                message.classList.remove('hidden', 'border-emerald-400/20', 'bg-emerald-500/10', 'text-emerald-100', 'border-rose-400/20', 'bg-rose-500/10', 'text-rose-100');

                if (type === 'success') {
                    message.classList.add('border', 'border-emerald-400/20', 'bg-emerald-500/10', 'text-emerald-100');
                } else {
                    message.classList.add('border', 'border-rose-400/20', 'bg-rose-500/10', 'text-rose-100');
                }
            }

            if (!form) return;

            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                const originalText = submitButton?.textContent ?? 'Send reset link';
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.textContent = 'Sending...';
                }

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                        },
                        body: new URLSearchParams(new FormData(form)),
                    });

                    const payload = await response.json();

                    if (!response.ok) {
                        throw new Error(payload.message || 'Unable to send reset OTP.');
                    }

                    showMessage('Password reset OTP sent. Check your inbox.', 'success');
                    form.reset();
                } catch (error) {
                    showMessage(error.message || 'Unable to send reset OTP right now.', 'error');
                } finally {
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.textContent = originalText;
                    }
                }
            });
        })();
    </script>
@endpush
@endsection
