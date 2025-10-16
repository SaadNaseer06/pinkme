<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientNotificationController extends Controller
{
    public function markAsRead(UserNotification $notification, Request $request)
    {
        $user = $request->user();

        if (!$user || $notification->user_id !== $user->id) {
            abort(403);
        }

        $notification->markAsRead();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back();
    }
}
