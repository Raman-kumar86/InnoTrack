<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
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

    public function showForgotPassword(): \Illuminate\View\View
    {
        return view('auth.forgot-password');
    }

    public function sendPasswordResetLink(Request $request): JsonResponse
{
    $validated = $request->validate([
        'email' => ['required', 'email', 'exists:users,email'],
    ]);

    $user = User::where('email', $validated['email'])->firstOrFail();

    // Generate OTP
    $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    $expiresAt = Carbon::now()->addMinutes(5);

    $jti = Str::random(40);

    $payload = [
        'email' => $user->email,
        'otp' => $otp,
        'exp' => $expiresAt->timestamp,
        'jti' => $jti,
    ];

    // Encrypt token
    $token = Crypt::encryptString(json_encode($payload));

    $resetUrl = route('auth.reset-password', [
        'token' => $token,
        'email' => $user->email,
    ]);

    /** @var array<string,mixed> $emailjs */
    $emailjs = (array) config('services.emailjs', []);

    // Validate EmailJS config
    if (
        blank($emailjs['service_id'] ?? null) ||
        blank($emailjs['template_id'] ?? null) ||
        blank($emailjs['public_key'] ?? null)
    ) {
        return response()->json([
            'message' => 'EmailJS is not configured properly.',
        ], 422);
    }

    // Send EmailJS request
    $response = Http::withHeaders([
        'origin' => config('app.url'),
    ])->asJson()->post(
        'https://api.emailjs.com/api/v1.0/email/send',
        [
            'service_id' => $emailjs['service_id'],
            'template_id' => $emailjs['template_id'],
            'user_id' => $emailjs['public_key'],

            'template_params' => [
                'from_name' => $emailjs['from_name'] ?? 'InnoTrack Support',
                'to_name' => $user->name,
                'to_email' => $user->email,
                'otp' => $otp,
                'reset_link' => $resetUrl,
                'reply_to' => config('mail.from.address'),
            ],
        ]
    );

    // Debug if failed
    if ($response->failed()) {

        return response()->json([
            'message' => 'Unable to send reset email.',
            'status' => $response->status(),
            'response' => $response->body(),
        ], 500);
    }

    // Prevent token reuse
    Cache::put(
        'password_reset_jti:' . $jti,
        false,
        $expiresAt
    );

    return response()->json([
        'message' => 'Password reset OTP sent successfully.',
        'email' => $user->email,
        'name' => $user->name,
    ]);
}

    public function showResetPassword(Request $request, string $token): \Illuminate\View\View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'otp' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        try {
            $decrypted = Crypt::decryptString($validated['token']);
            $payload = json_decode($decrypted, true);
        } catch (\Throwable $e) {
            throw ValidationException::withMessages(['email' => 'Invalid or malformed reset token.']);
        }

        if (! is_array($payload) || ($payload['email'] ?? '') !== $validated['email']) {
            throw ValidationException::withMessages(['email' => 'Invalid reset token.']);
        }

        if (Carbon::now()->timestamp > ($payload['exp'] ?? 0)) {
            throw ValidationException::withMessages(['email' => 'Reset token has expired. Request a new one.']);
        }

        // Ensure token not already used
        $jti = $payload['jti'] ?? null;
        if ($jti && Cache::get('password_reset_jti:' . $jti) === true) {
            throw ValidationException::withMessages(['email' => 'This reset token has already been used.']);
        }

        // Verify OTP
        if (! hash_equals((string) ($payload['otp'] ?? ''), (string) $validated['otp'])) {
            throw ValidationException::withMessages(['otp' => 'The provided OTP is invalid.']);
        }

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            throw ValidationException::withMessages(['email' => 'No account found for this email.']);
        }

        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'remember_token' => Str::random(60),
        ])->save();

        $this->logActivity([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'module' => 'Users',
            'action' => 'Reset password',
            'result' => 'Success',
            'loggable_type' => User::class,
            'loggable_id' => $user->id,
            'description' => $user->name . ' reset their password successfully.',
            'icon' => 'shield-check',
        ]);

        // Mark token as used until original expiry to prevent reuse
        if ($jti) {
            $ttl = max(1, ($payload['exp'] ?? 0) - Carbon::now()->timestamp);
            Cache::put('password_reset_jti:' . $jti, true, $ttl);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Password reset successfully.');
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
            'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
            'email' => $validated['email'],
            'role' => $role,
            'state' => $this->resolveState($role, $validated['state'] ?? null),
            'is_active' => true,
            'password' => Hash::make($validated['password']),
        ]);

        event(new Registered($user));

        Auth::login($user);
        $request->session()->regenerate();

        $this->logActivity([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'module' => 'Users',
            'action' => 'Registered account',
            'result' => 'Success',
            'loggable_type' => User::class,
            'loggable_id' => $user->id,
            'description' => $user->name . ' created a new account.',
            'icon' => 'users',
        ]);

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
