<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications;
        $notifications = $notifications->sortByDesc('created_at');
    
        return inertia('Notifications/Index', compact('notifications'));
    }

    public function markAsRead(Request $request,$id)
    {
        $notification = auth()->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
        if($request->wantsJson()){
            return response()->json([
                'message' => 'Notification marked as read',
                'notification' => $notification,
            ]);
        }
        return redirect()->back();
    }

    public function destroy(Request $request,$id){
        $notification = auth()->user()->notifications()->find($id);
        if ($notification) {
            $notification->delete();
        }
        if($request->wantsJson()){
            return response()->json([
                'message' => 'Notification deleted',
                'notification' => $notification,
            ]);
        }
        return redirect()->back();
    }
}
