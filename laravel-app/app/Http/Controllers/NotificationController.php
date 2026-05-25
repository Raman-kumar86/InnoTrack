<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;

class NotificationController extends Controller
{
    public function markAsRead(Notification $notification): RedirectResponse
    {
        $this->authorizeNotification($notification);

        if (! $notification->is_read) {
            $notification->update(['is_read' => true]);
        }

        return $this->redirectFor($notification)->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead(): RedirectResponse
    {
        $user = auth()->user();

        if (! $user) {
            abort(403);
        }

        Notification::query()
            ->where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }

    private function authorizeNotification(Notification $notification): void
    {
        $user = auth()->user();

        if (! $user || (int) $notification->user_id !== (int) $user->id) {
            abort(403);
        }
    }

    private function redirectFor(Notification $notification): RedirectResponse
    {
        if ($notification->startup_id) {
            return redirect()->route('startups.show', ['startup' => $notification->startup_id]);
        }

        return redirect()->route('dashboard');
    }
}
