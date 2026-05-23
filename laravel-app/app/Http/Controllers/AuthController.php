<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin(): \Illuminate\View\View
    {
        return view('auth.login');
    }

    public function showRegister(): \Illuminate\View\View
    {
        return view('auth.register');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable'],
        ]);

        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'The provided credentials do not match our records.'])
                ->withInput($request->only('email', 'remember'));
        }

        $user = Auth::user();

        if (! $user || ! $user->is_active) {
            Auth::logout();

            return back()
                ->withErrors(['email' => 'This account is inactive. Contact an administrator.'])
                ->withInput($request->only('email', 'remember'));
        }

        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Signed in successfully.');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['viewer', 'state_officer'])],
            'state' => ['nullable', 'string', 'max:100', 'required_if:role,state_officer'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'terms' => ['accepted'],
        ]);

        $role = $this->resolveRole($validated['email'], $validated['role']);

        $user = User::create([
            'name' => trim($validated['first_name'].' '.$validated['last_name']),
            'email' => $validated['email'],
            'role' => $role,
            'state' => $this->resolveState($role, $validated['state'] ?? null),
            'is_active' => true,
            'password' => Hash::make($validated['password']),
        ]);

        event(new Registered($user));

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Account created successfully.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login')->with('success', 'Signed out successfully.');
    }

    private function resolveRole(?string $email, string $requestedRole): string
    {
        $superAdminEmail = trim((string) config('services.super_admin.email'));

        if ($superAdminEmail !== '' && strcasecmp((string) $email, $superAdminEmail) === 0) {
            return 'super_admin';
        }

        return $requestedRole;
    }

    private function resolveState(string $role, ?string $state): ?string
    {
        if ($role === 'super_admin') {
            return 'All';
        }

        $state = trim((string) $state);

        return $state === '' ? null : $state;
    }
}