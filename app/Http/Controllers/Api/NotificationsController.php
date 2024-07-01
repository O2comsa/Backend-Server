<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notifications = auth('api')->user()->notifications()->get();

        return ApiHelper::output($notifications);
    }

    public function markAsRead(Request $request, $notificationId)
    {
        $user = auth('api')->user();

        $user->notifications()->where('id', $notificationId)->get()?->markAsRead();

        return ApiHelper::output(['message' => 'success']);
    }

    public function markAllAsRead(Request $request)
    {
        $user = auth('api')->user();

        $user->unreadNotifications->markAsRead();

        return ApiHelper::output(['message' => 'success']);
    }
}
