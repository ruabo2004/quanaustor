<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get user notifications (for dropdown)
     */
    public function index()
    {
        $notifications = Notification::where(function($query) {
                $query->where('user_id', Auth::id())
                      ->orWhereNull('user_id'); // System notifications
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $unreadCount = Notification::where(function($query) {
                $query->where('user_id', Auth::id())
                      ->orWhereNull('user_id');
            })
            ->unread()
            ->count();

        return response()->json([
            'notifications' => $notifications->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'icon' => $notification->icon,
                    'color' => $notification->color,
                    'read' => $notification->read,
                    'time_ago' => $notification->time_ago,
                    'data' => $notification->data
                ];
            }),
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Get all user notifications (paginated page)
     */
    public function all()
    {
        $notifications = Notification::where(function($query) {
                $query->where('user_id', Auth::id())
                      ->orWhereNull('user_id');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
            ->where(function($query) {
                $query->where('user_id', Auth::id())
                      ->orWhereNull('user_id');
            })
            ->first();

        if ($notification && !$notification->read) {
            $notification->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Notification::where(function($query) {
                $query->where('user_id', Auth::id())
                      ->orWhereNull('user_id');
            })
            ->unread()
            ->update([
                'read' => true,
                'read_at' => now()
            ]);

        return response()->json(['success' => true]);
    }

    /**
     * Get unread count
     */
    public function unreadCount()
    {
        $count = Notification::where(function($query) {
                $query->where('user_id', Auth::id())
                      ->orWhereNull('user_id');
            })
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id()) // Only user's own notifications can be deleted
            ->first();

        if ($notification) {
            $notification->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }
}
