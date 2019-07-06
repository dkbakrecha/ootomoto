<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Feedback;
use App\User;
use URL;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class FeedbacksController extends Controller {

    public function index(Request $request) {
        $currentUserID = Auth::guard('api')->id();
        If (!empty($request->input('page'))) {
            $page = $request->input('page');
        } else {
            $page = "1";
        }
        $perpage = "10";

        $offset = ($page - 1) * $perpage;

        $feedbackData = Feedback::where(function ($query) {
                            $currentUserID = Auth::guard('api')->id();
                            $query->where('from_id', '=', $currentUserID)
                            ->orWhere('to_id', '=', $currentUserID);
                        })
                        ->skip($offset)
                        ->take($perpage)
                        ->orderBy('id', 'DESC')
                        ->get()->toArray();

        if (!empty($feedbackData)) {
            $resMessages = array();

            foreach ($feedbackData as $feedback) {
                $messages['id'] = $feedback['id'];
                $messages['from_id'] = $feedback['from_id'];
                $messages['to_id'] = $feedback['to_id'];
                if ($feedback['message_type'] == 1) {
                    $messages['message'] = URL::to('/') . '/images/feedback/' . $feedback['message'];
                } else {
                    $messages['message'] = $feedback['message'];
                }
                $messages['time'] = $feedback['created_at'];
                $messages['type'] = $feedback['message_type'];

                $resMessages[] = $messages;
            }

            return response()->json([
                        "success" => true,
                        "data" => $resMessages,
                            ], 200);
        } else {
            return response()->json([
                        "success" => true,
                        "data" => [],
                        "message" => $this->message("no_more_messages", $currentUserID),
                            ], 206);
        }
    }

    public function store(Request $request) {
        $currentUserID = Auth::guard('api')->id();
        $admin = User::Where('user_type', '=', 3)->first();

        $validator = Validator::make($request->all(), [
                    'message' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }

        $message = new Feedback();

        if ($request->hasFile('message')) {
            // $file = $request->file('profile_image');
            $file = $request->message;

            $_filename = "feedback_" . $currentUserID . time() . ".png";
            $destinationPath = public_path() . '/images/feedback/';
            //$path = public_path() . '/images/profile/' . $_filename;
            //Image::make(file_get_contents($request->get('profile_image')))->save($path);
            $file->move($destinationPath, $_filename);

            $message->message = $_filename;
            $message->message_type = 1;
        } else {
            $message->message = $request->get('message');
        }

        $message->to_id = $admin->id;
        $message->from_id = $currentUserID;
        $message->parent_id = $message->findParent($currentUserID, $admin->id);
        $message->save();

        if ($message->parent_id == 0) {
            $message->parent_id = $message->findParent($currentUserID, $admin->id);
            $message->save();
        }

        $this->auto_reply();

        return response()->json([
                    "success" => true,
                    'message' => $this->message("feedback_sent_successfully", $currentUserID),
                        ], 200);
    }

    public function auto_reply() {
        $admin = User::Where('user_type', '=', 3)->first();
        $currentUserID = Auth::guard('api')->id();

        $message = new Feedback();
        $message->message = $this->message("auto_reply_feedback", $currentUserID);
        $message->to_id = $currentUserID;
        $message->from_id = $admin->id;
        $message->parent_id = $message->findParent($admin->id, $currentUserID);
        $message->save();

        // Send push notification

        $device = \App\MobileSessions::where('user_id', '=', $currentUserID)
                        ->where('status', '=', 1)->first();

        // Create push notification content
        $body = $this->message("auto_reply_feedback", $currentUserID);

        switch ($device['device_type']) {
            case 'android':
                // Third Parameter 3 depicts it as a chat message               
                $this->sendAndroidNotification($device['device_token'], $body, 3);
                break;

            case 'ios':
                // Third Parameter 3 depicts it as a chat message
                $this->sendIosNotification($device['device_token'], $body, 3);
                break;

            default:
                break;
        }

        return true;
    }

}
