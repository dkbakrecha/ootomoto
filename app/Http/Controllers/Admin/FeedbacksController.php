<?php

namespace App\Http\Controllers\Admin;

use DB;
use Auth;
use App\Feedback;
use App\Admin;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class FeedbacksController extends Controller {

    public function __construct() {
        $this->middleware('auth:admin');
    }

    public function index(Request $request) {
        $sub = Feedback::Select(DB::raw('MAX(id) as id'))
                        ->groupBy('parent_id')->get();
        //prd(Auth::guard('admin')->id());
        $feedbackData = Feedback::Where(function ($query) {
                    $currentUserID = Auth::guard('admin')->id();
                    $query->where('feedbacks.from_id', '=', $currentUserID)
                    ->orWhere('feedbacks.to_id', '=', $currentUserID);
                })
                ->whereIn('feedbacks.id', $sub)
                ->with(['sender', 'receiver'])
                ->where(function ($query) use ($request) {
                    if (!empty($request->q)) {
                        $query->where('feedbacks.message', 'like', "%" . $request->q . "%")
                        ->orWhereHas('sender', function($query) use ($request) {
                            $query->where('name', 'like', "%" . $request->q . "%");
                        })->orWhereHas('receiver', function($query) use ($request) {
                            $query->where('name', 'like', "%" . $request->q . "%");
                        });
                    }
                })
                ->orderBy('id', 'DESC')
                ->groupBy('parent_id')
                ->paginate(8);

        //prd($feedbackData->toArray());

        $feedbackData->appends(array(
            'q' => $request->q
        ));

        $query = "";
        if (!empty($request->q)) {
            $query = $request->q;
        }
        return view('admin.feedbacks.index', ['feedbackData' => $feedbackData, 'q' => $query])
                        ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function index_old(Request $request) {
        $feedbackData = Feedback::where(function ($query) {
                    $currentUserID = Auth::guard('admin')->id();
                    $query->where('feedbacks.from_id', '=', $currentUserID)
                    ->orWhere('feedbacks.to_id', '=', $currentUserID);
                })
                ->with(['sender' => function($query) use ($request) {
                        /* if (!empty($request->q)) {
                          $query->where('name', 'like', "%" . $request->q . "%");
                          } */
                    }, 'receiver' => function($query) use ($request) {
                        /* if (!empty($request->q)) {
                          $query->where('name', 'like', "%" . $request->q . "%");
                          } */
                    }])
                ->where(function ($query) use ($request) {
                    if (!empty($request->q)) {
                        $query
                        ->where('feedbacks.message', 'like', "%" . $request->q . "%")
                        ->orWhereHas('sender', function($query) use ($request) {
                            $query->where('name', 'like', "%" . $request->q . "%");
                        })->orWhereHas('receiver', function($query) use ($request) {
                            $query->where('name', 'like', "%" . $request->q . "%");
                        });
                    }
                })
                /* ->where('feedbacks.message', 'like', "%" . $request->q . "%")
                  ->orWhere(function ($query) use ($request) {
                  $query->whereHas('sender', function($query) use ($request) {
                  if (!empty($request->q)) {
                  $query->where('name', 'like', "%" . $request->q . "%");
                  }
                  });
                  $query->whereHas('receiver', function($query) use ($request) {
                  if (!empty($request->q)) {
                  $query->where('name', 'like', "%" . $request->q . "%");
                  }
                  });
                  }) */
                /* ->where('feedbacks.message', 'like', "%" . $request->q . "%")
                  ->whereHas('sender', function($query) use ($request) {
                  if (!empty($request->q)) {
                  $query->where('name', 'like', "%" . $request->q . "%");
                  }
                  })
                  ->whereHas('receiver', function($query) use ($request) {
                  if (!empty($request->q)) {
                  $query->where('name', 'like', "%" . $request->q . "%");
                  }
                  }) */
//->select(DB::raw('*, max(created_at) as created_at'))
//->orderBy('feedbacks.id', 'DESC')
                ->orderBy('created_at', 'DESC')
//->latest('created_at')
//->leftJoin('users as sitesender', 'sitesender.id', '=', 'feedbacks.from_id')
//->leftJoin('users as sitereceiver', 'sitereceiver.id', '=', 'feedbacks.to_id')
                ->groupBy('parent_id')
                ->paginate(10);
//prd($feedbackData->toArray());
        $feedbackData->appends(array(
            'q' => $request->q
        ));

        $query = "";
        if (!empty($request->q)) {
            $query = $request->q;
        }
        return view('admin.feedbacks.index', ['feedbackData' => $feedbackData, 'q' => $query])
                        ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function getMessages(Request $request) {
        /* $feedbackData = Feedback::where(function ($query) use($request) {
          $query->where('from_id', '=', $request->id)
          ->orWhere('to_id', '=', $request->id);
          })
          ->orderBy('id', 'DESC')
          ->get()->toArray(); */

        $feedbackData = Feedback::where('parent_id', '=', $request->id)
//->orderBy('id', 'DESC')
                        ->get()->toArray();

        $userData = User::select(["id", "unique_id", "name", "profile_image", "isAdmin", "status", "user_type"])
                        ->where('id', '=', $request->user_id)->first();


        return view('admin.feedbacks.get_messages', ['feedbackData' => $feedbackData, 'userData' => $userData]);
    }

    public function feedbackReply(Request $request) {
        $admin = User::Where('user_type', '=', 3)->first();

        $validator = Validator::make($request->all(), [
                    'id' => 'required',
                    'replay_message' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 200);
        }

        $message = new Feedback();
        $message->message = $request->get('replay_message');
        $message->to_id = $request->get('user_id');
        $message->from_id = $admin->id;
        $message->parent_id = $message->findParent($admin->id, $request->get('user_id'));
        $message->save();

        // Send push notification
        /*$device = User::find($request->get('user_id'))
                ->mobileSessions()
                ->where('status', '=', 1)
                ->first();*/
        
        $device = \App\MobileSessions::where('user_id', '=', $request->get('user_id'))
                ->where('status', '=', 1)->first();
        
        // Create push notification content
        $body = $request->get('replay_message');

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

        return response()->json([
                    "success" => true,
                    'message' => __('messages.feedback_reply_successfully_sent'),
                        ], 200);
    }

    public function create(Request $request) {
        $admin = User::Where('user_type', '=', 3)->first();

        $users = User::where('user_type', '!=', 3)
                ->pluck('name', 'id');

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                        //'broadcast' => 'required_without:users_id,area_id',
                        //'users_id' => 'required_without:broadcast,area_id',
                        //'area_id' => 'required_without:broadcast,users_id',
                        'message' => 'required',
            ]);

            if ($validator->fails()) {
//return $this->sendError($validator->errors()->first(), null, 200);
                return view('admin.feedbacks.create', ['users' => $users])->withErrors($validator);
            }
        }

        /** Message for selected users */
        if (!empty($request->users_id)) {
            foreach ($request->users_id as $_user) {
                $message = new Feedback();
                $message->message = $request->get('message');
                $message->to_id = $_user;
                $message->from_id = $admin->id;
                $message->parent_id = $message->findParent($_user, $admin->id);
                $message->save();

                if ($message->parent_id == 0) {
                    $message->parent_id = $message->findParent($_user, $admin->id);
                    $message->save();
                }

                // Send push notification
                $device = User::find($_user)
                        ->mobileSessions()
                        ->where('status', '=', 1)
                        ->first();

                // Create push notification content
                $body = $request->get('message');

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
            }

            return redirect('/admin/feedbacks')->with('success', __('messages.messages_sent_success'));
        }

        if (!empty($request->broadcast)) {
            $users = User::whereIn('user_type', $request->broadcast)
                    ->where('status', '=', 1)
                    ->get();

            foreach ($users as $user) {
                $message = new Feedback();
                $message->message = $request->get('message');
                $message->to_id = $user->id;
                $message->from_id = $admin->id;
                $message->parent_id = $message->findParent($user->id, $admin->id);
                $message->save();

                if ($message->parent_id == 0) {
                    $message->parent_id = $message->findParent($user->id, $admin->id);
                    $message->save();
                }

                // Send push notification
                $device = User::find($user->id)
                        ->mobileSessions()
                        ->where('status', '=', 1)
                        ->first();

                // Create push notification content
                $body = $request->get('message');

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
            }
            return redirect('/admin/feedbacks')->with('success', __('messages.messages_sent_success'));
        }

        /** Message for Area id */
        if (!empty($request->area_id)) {
            //Find Users in selected area
            $users = User::where('area_id', '=', $request->area_id)
                    ->where('status', '=', 1)
                    ->select('id')
                    ->get();



            foreach ($users as $_user) {
                $message = new Feedback();
                $message->message = $request->get('message');
                $message->to_id = $_user->id;
                $message->from_id = $admin->id;
                $message->parent_id = $message->findParent($_user->id, $admin->id);
                $message->save();

                if ($message->parent_id == 0) {
                    $message->parent_id = $message->findParent($_user->id, $admin->id);
                    $message->save();
                }

                // Send push notification
                $device = User::find($_user->id)
                        ->mobileSessions()
                        ->where('status', '=', 1)
                        ->first();

                // Create push notification content
                $body = $request->get('message');

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
            }

            return redirect('/admin/feedbacks')->with('success', __('messages.messages_sent_success'));
        }


        return view('admin.feedbacks.create', ['users' => $users]);
    }

}
