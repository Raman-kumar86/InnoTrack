@extends('layouts.auth')

@section('content')
<div class="space-y-6 text-center">
    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-amber-500/15 text-amber-300 ring-1 ring-amber-400/20">
        <x-ui.icon name="shield" class="h-10 w-10" />
    </div>

    <div class="space-y-2">
        <p class="text-sm font-semibold uppercase tracking-[0.35em] text-amber-200">Access restricted</p>
        <h1 class="text-3xl font-semibold tracking-tight text-white">You have been blocked</h1>
        <p class="mx-auto max-w-md text-sm leading-6 text-slate-300">
            Your account is inactive. You cannot access the website until an administrator restores your access.
        </p>
    </div>

    <form method="POST" action="{{ route('auth.logout') }}" class="pt-2">
        @csrf
        <x-ui.button type="submit" class="w-full justify-center">Logout</x-ui.button>
    </form>
</div>
@endsection
