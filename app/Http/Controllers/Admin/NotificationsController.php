<?php

namespace App\Http\Controllers\Admin;

use DB;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class NotificationsController extends Controller
{
    //
    public function __construct() {
        $this->middleware('auth:admin');
    }
    
    public function index() {

        $notifications = \App\WebNotification::latest('created_at')
                ->where('notification_for', 1)
                ->with('user')
                ->get();
        
        return view('admin.web_notifications.notification-list', compact('notifications'));
    }

    public function readNotification(Request $request) {
        if($request->ajax()) {
            $id = $request->id;

            $record = \App\WebNotification::find($id);

            if ($record->is_read == 1) {
                $response = [
                    "success" => false,
                    'message' => "Notification already read",
                ];

                return response()->json($response, 200);
            }

            \App\WebNotification::find($id)
                                ->update([
                                    'is_read' => 1
                                ]);

            $response = [
                "success" => true,
                'message' => "Notification status changed",
            ];
            

            return response()->json($response, 200);
        }
    }
}
