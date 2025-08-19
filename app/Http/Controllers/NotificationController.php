<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{


    public function myNotifications(Request $request)
    {
        $user = $request->user();

        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get(['title', 'body', 'created_at']);

        return response()->json([
            'status'        => 'success',
            'notifications' => $notifications,
        ]);
    }}
