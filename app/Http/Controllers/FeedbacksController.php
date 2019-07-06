<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\User;
use App\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeedbacksController extends Controller {

    public function __construct() {
        $this->middleware('auth:web');
    }

    public function index(Request $request) {
        $sub = Feedback::Select(DB::raw('MAX(id) as id'))
                        ->groupBy('parent_id')->get();

        $feedbackData = Feedback::where(function ($query) {
                    $currentUserID = Auth::guard('web')->id();
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

        $feedbackData->appends(array(
            'q' => $request->q
        ));

        $query = "";
        if (!empty($request->q)) {
            $query = $request->q;
        }
        return view('feedbacks.index', ['feedbackData' => $feedbackData, 'q' => $query])
                        ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function getMessages(Request $request) {
        $feedbackData = Feedback::where('parent_id', '=', $request->id)
                        ->get()->toArray();

        $userData = User::select(["id", "unique_id", "name", "profile_image", "isAdmin", "status", "user_type"])
                        ->where('id', '=', $request->user_id)->first();


        return view('feedbacks.get_messages', ['feedbackData' => $feedbackData, 'userData' => $userData]);
    }

    public function feedbackReply(Request $request) {
        $validator = Validator::make($request->all(), [
                    'id' => 'required',
                    'replay_message' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 200);
        }

        $message = new Feedback();
        $message->message = $request->get('replay_message');
        $message->to_id = $request->user_id;
        $message->from_id = Auth::guard('web')->id();
        $message->parent_id = $message->findParent($request->user_id, Auth::guard('web')->id());
        $message->save();

        return response()->json([
                    "success" => true,
                    'message' => __('messages.feedback_reply_successfully_sent'),
                        ], 200);
    }

    public function create(Request $request) {

        $_loggedInUser = Auth::guard('web')->user();
        if ($_loggedInUser->user_type == 0) {
            // If shop owner login
            $users = User::where(function ($query) {
                        $query->where('user_type', '=', 3)
                        ->orWhere([
                            ['user_type', '=', 1],
                            ['isAdmin', '=', 1],
                            ['shop_id', '=', $this->_shop_id()]
                        ]);
                    })
                    ->pluck('name', 'id');
        } else {
            // If shop staff login
            $users = User::where(function ($query) {
                        $query->where('user_type', '=', 3)
                        ->orWhere([
                            ['user_type', '=', 0],
                            ['id', '=', $this->_shop_id()]
                        ]);
                    })
                    ->pluck('name', 'id');
        }


        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                        'broadcast' => 'required_without:users_id',
                        'users_id' => 'required_without:broadcast',
                        'message' => 'required',
            ]);

            if ($validator->fails()) {
//return $this->sendError($validator->errors()->first(), null, 200);
                return view('feedbacks.create', ['users' => $users])->withErrors($validator);
            }
        }

        /** Message for selected users */
        if (!empty($request->users_id)) {
            foreach ($request->users_id as $_user) {
                $message = new Feedback();
                $message->message = $request->get('message');
                $message->to_id = $_user;
                $message->from_id = Auth::guard('web')->id();
                $message->parent_id = $message->findParent($_user, Auth::guard('web')->id());
                $message->save();

                if ($message->parent_id == 0) {
                    $message->parent_id = $message->findParent($_user, Auth::guard('web')->id());
                    $message->save();
                }
            }

            return redirect('/feedbacks')->with('success', __('messages.messages_sent_success'));
        }


        return view('feedbacks.create', ['users' => $users]);
    }

}
