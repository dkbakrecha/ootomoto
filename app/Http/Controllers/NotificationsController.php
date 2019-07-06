<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class NotificationsController extends Controller
{
    //
    public function __construct() {
        $this->middleware('auth:web');
    }
    
    public function index() {
        $currentUserID = Auth::guard('web')->id();

        $notifications = \App\WebNotification::latest('created_at')
                ->where('notification_for', $currentUserID)
                ->with('user')
                ->get();
        
        // prd($notifications->toArray());
        
        return view('web_notifications.notification-list', compact('notifications'));
    }
    
    public function readNotification(Request $request) {
        if($request->ajax()) {
            $id = $request->id;

            $record = \App\WebNotification::find($id);

            if ($record->is_read == 1) {
                $response = [
                    "success" => false,
                    'message' => __('messages.notification_already_read'),
                ];

                return response()->json($response, 200);
            }

            \App\WebNotification::find($id)
                                ->update([
                                    'is_read' => 1
                                ]);

            $response = [
                "success" => true,
                'message' => __('messages.notification_successfully_read'),
            ];
            

            return response()->json($response, 200);
        }
    }
}