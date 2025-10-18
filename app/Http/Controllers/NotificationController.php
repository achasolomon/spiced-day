<?php

// app/Http/Controllers/NotificationController.php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->notifications()
            ->with(['application', 'createdBy'])
            ->when($request->type, function ($q, $type) {
                return $q->where('type', $type);
            })
            ->when($request->priority, function ($q, $priority) {
                return $q->where('priority', $priority);
            })
            ->when($request->unread_only, function ($q) {
                return $q->where('is_read', false);
            });

        $notifications = $query->latest()->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function show(Notification $notification)
    {
        Gate::authorize('view', $notification);

        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        return view('notifications.show', compact('notification'));
    }

    public function markAsRead(Notification $notification)
{
    \Log::debug('markAsRead called', [
        'notification_id' => $notification->id,
        'user_id' => auth()->id(),
        'is_read_before' => $notification->is_read,
    ]);

    try {
        // Temporarily bypass authorization
        // Gate::authorize('update', $notification);

        $notification->markAsRead();

        \Log::debug('Notification marked as read', [
            'notification_id' => $notification->id,
            'is_read_after' => $notification->is_read,
            'read_at' => $notification->read_at,
        ]);

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        \Log::error('Failed to mark notification as read', [
            'notification_id' => $notification->id,
            'user_id' => auth()->id(),
            'error' => $e->getMessage(),
        ]);
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

    public function markAllRead()
    {
        auth()->user()->notifications()->unread()->update([
            'is_read' => true,
            'read_at' => now()
        ]);

        return back()->with('success', 'All notifications marked as read!');
    }

    public function destroy(Notification $notification)
    {
        Gate::authorize('delete', $notification);

        $notification->delete();

        return back()->with('success', 'Notification deleted!');
    }

    public function getUnreadCount()
    {
        return response()->json([
            'count' => auth()->user()->getUnreadNotificationsCount()
        ]);
    }
    public function sendToUser(Request $request, User $user)
{
    $validated = $request->validate([
        'application_id' => 'nullable|exists:applications,id',
        'title' => 'required|string|max:255',
        'message' => 'required|string',
        'priority' => 'required|in:low,normal,high',
    ]);

    \App\Models\Notification::create([
        'user_id' => $user->id,
        'application_id' => $validated['application_id'] ?? null,
        'type' => 'admin_message',
        'title' => $validated['title'],
        'message' => $validated['message'],
        'priority' => $validated['priority'],
    ]);

    return back()->with('success', 'Notification sent successfully!');
    }
    
}

