<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $limit = (int) $request->integer('limit', 10);
        $limit = min(max($limit, 1), 50);

        $totalUnread = $user->notifications()->unread()->count();

        $notifications = $user->notifications()
            ->latest()
            ->take($limit)
            ->get();

        $important = $user->notifications()
            ->unread()
            ->important()
            ->latest()
            ->first();

        return response()->json([
            'unread_count'  => $totalUnread,
            'notifications' => $notifications->map->toFrontendPayload()->values(),
            'important'     => $important?->toFrontendPayload(),
        ]);
    }

    public function markAsRead(UserNotification $notification, Request $request): Response
    {
        $user = $request->user();
        $this->assertOwnsNotification($notification, $user?->id);

        $notification->markAsRead();

        $unreadCount = UserNotification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();

        if ($request->expectsJson()) {
            return response()->json([
                'success'      => true,
                'notification' => $notification->toFrontendPayload(),
                'unread_count' => $unreadCount,
            ]);
        }

        return redirect()->back();
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = $request->user();
        $now = now();

        $affected = UserNotification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->update([
                'read_at'    => $now,
                'updated_at' => $now,
            ]);

        return response()->json([
            'success'       => true,
            'cleared_count' => $affected,
            'unread_count'  => 0,
        ]);
    }

    private function assertOwnsNotification(UserNotification $notification, ?int $userId): void
    {
        if (!$userId || $notification->user_id !== $userId) {
            abort(403);
        }
    }
}
