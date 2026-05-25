<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * @mixin \Illuminate\Foundation\Auth\Access\AuthorizesRequests
 */
class UserManagementController extends Controller
{
    public function __construct(private readonly ActivityLogService $activityLogService)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $search = $request->string('search')->toString() ?: null;
        $role = $request->string('role')->toString() ?: null;
        $status = $request->string('status')->toString() ?: null;

        $query = User::query()
            ->when($search, static function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->when($role, static fn ($query, string $role) => $query->where('role', $role))
            ->when($status, static fn ($query, string $status) => $query->where('status', $status));

        $authUser = $request->user();
        if ($authUser?->isStateAnalyst()) {
            $query->where('role', 'reviewer');
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('users.index', [
            'title' => 'User Management',
            'pageTitle' => 'User Management',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('dashboard')],
                ['label' => 'User Management', 'url' => route('users.index')],
            ],
            'users' => $users,
            'filters' => ['search' => $search, 'role' => $role, 'status' => $status],
            'roleOptions' => ['super_admin', 'state_analyst', 'reviewer'],
            'statusOptions' => ['active', 'blocked'],
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'role' => $request->string('role')->toString(),
            'status' => $request->string('status')->toString(),
            'is_active' => $request->string('status')->toString() === 'active',
            'password' => Hash::make($request->string('password')->toString()),
        ]);

        return back()->with('success', 'User ' . $user->name . ' created successfully.');
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $user->fill([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'role' => $request->string('role')->toString(),
            'status' => $request->string('status')->toString(),
            'is_active' => $request->string('status')->toString() === 'active',
        ]);

        if ($request->filled('password')) {
            $user->password = Hash::make($request->string('password')->toString());
        }

        $user->save();

        return back()->with('success', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $actor = $request->user();
        $userName = $user->name;
        $user->delete();

        $this->activityLogService->logUserAction(
            $actor,
            $user,
            'user_deleted',
            $this->actorLabel($actor) . ' deleted ' . $this->targetLabel($user) . ' ' . $userName
        );

        return back()->with('success', 'User deleted successfully.');
    }

    public function block(Request $request, User $user): RedirectResponse
    {
        $this->authorize('block', $user);

        $user->update(['status' => 'blocked', 'is_active' => false]);

        $actor = $request->user();
        $this->activityLogService->logUserAction(
            $actor,
            $user,
            'user_blocked',
            $this->actorLabel($actor) . ' blocked ' . $this->targetLabel($user) . ' ' . $user->name
        );

        return back()->with('success', 'User blocked successfully.');
    }

    public function unblock(Request $request, User $user): RedirectResponse
    {
        $this->authorize('unblock', $user);

        $user->update(['status' => 'active', 'is_active' => true]);

        $actor = $request->user();
        $this->activityLogService->logUserAction(
            $actor,
            $user,
            'user_unblocked',
            $this->actorLabel($actor) . ' unblocked ' . $this->targetLabel($user) . ' ' . $user->name
        );

        return back()->with('success', 'User unblocked successfully.');
    }

    public function promote(Request $request, User $user): RedirectResponse
    {
        $this->authorize('promote', $user);

        $oldRole = $user->role;
        if ($oldRole === 'reviewer') {
            $user->update(['role' => 'state_analyst']);
            $action = 'promoted_to_state_analyst';
            $message = 'User promoted to State Analyst.';
        } elseif ($oldRole === 'state_analyst') {
            $user->update(['role' => 'super_admin']);
            $action = 'promoted_to_super_admin';
            $message = 'User promoted to Super Admin.';
        } else {
            return back()->with('warning', 'Promotion not applicable for this role.');
        }

        $actor = $request->user();
        $this->activityLogService->logUserAction(
            $actor,
            $user,
            $action,
            $this->actorLabel($actor) . ' promoted ' . $user->name . ' from ' . ucwords(str_replace('_', ' ', $oldRole)) . ' to ' . ucwords(str_replace('_', ' ', $user->role))
        );

        return back()->with('success', $message);
    }

    public function demote(Request $request, User $user): RedirectResponse
    {
        $this->authorize('demote', $user);

        $user->update(['role' => 'reviewer']);

        $actor = $request->user();
        $this->activityLogService->logUserAction(
            $actor,
            $user,
            'demoted_to_reviewer',
            $this->actorLabel($actor) . ' demoted ' . $user->name . ' to Reviewer'
        );

        return back()->with('success', 'User demoted to Reviewer.');
    }

    private function actorLabel(User $user): string
    {
        return match ($user->role) {
            'super_admin' => 'Super Admin',
            'state_analyst' => 'State Analyst',
            default => 'Reviewer',
        };
    }

    private function targetLabel(User $user): string
    {
        return match ($user->role) {
            'super_admin' => 'Super Admin',
            'state_analyst' => 'State Analyst',
            default => 'Reviewer',
        };
    }
}
