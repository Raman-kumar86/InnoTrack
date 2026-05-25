<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function edit()
    {
        return view('settings.index');
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'state' => ['nullable', 'string', 'max:100'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($user->role !== 'super_admin') {
            $user->state = filled($validated['state'] ?? null) ? trim($validated['state']) : null;
        }

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        $this->logActivity([
            'module' => 'Users',
            'action' => 'Updated profile settings',
            'result' => 'Success',
            'loggable_type' => User::class,
            'loggable_id' => $user->id,
            'description' => $user->name . ' updated personal settings.',
            'metadata' => [
                'user_id' => $user->id,
                'email' => $user->email,
                'state' => $user->state,
            ],
            'icon' => 'users',
        ]);

        return redirect()
            ->route('settings.index')
            ->with('success', 'Settings saved successfully.');
    }
}