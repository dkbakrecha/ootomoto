<?php

namespace App\Http\Controllers\Api;

use URL;
use Auth;
use App\ShopReview;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use DB;

class ReviewsController extends Controller {

    public function index(Request $request) {
        If (!empty($request->input('page'))) {
            $page = $request->input('page');
        } else {
            $page = "1";
        }
        $perpage = "10";

        $offset = ($page - 1) * $perpage;

        $validator = Validator::make($request->all(), [
                    'shop_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }

        $reviewData = ShopReview::where('shop_id', '=', $request->get('shop_id'))
                        ->whereNotNull('review_text')
                        ->where('status', '=', 1)
                        ->with(['customer'])
                        ->skip($offset)
                        ->take($perpage)
                        ->orderBy('id', 'DESC')
                        ->get()->toArray();

        if (!empty($reviewData)) {
            //prd($reviewData);
            $resMessages = array();

            foreach ($reviewData as $review) {
                $messages['id'] = $review['id'];
                $messages['rating'] = $review['rating'];
                $messages['review_text'] = $review['review_text'];
                $messages['customer_name'] = $review['customer']['name'];
                if (!empty($review['customer']['profile_image'])) {
                    $messages['customer_profile'] = URL::to('/') . '/images/profile/' . $review['customer']['profile_image'];
                } else {
                    $messages['customer_profile'] = $review['customer']['profile_image'];
                }
                $messages['created_at'] = $review['created_at'];
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
                        "message" => "No shop reviews found now",
                            ], 206);
        }
    }

    public function store(Request $request) {
        $customer_id = Auth::guard('api')->id();

        $validator = Validator::make($request->all(), [
                    'shop_id' => 'required',
                    'rating' => 'required',
                    'review_text' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }

        $shop_id = $request->get('shop_id');

        $review = new ShopReview();
        $review->shop_id = $shop_id;
        $review->customer_id = $customer_id;
        $review->rating = $request->get('rating');
        $review->review_text = (!empty($request->get('review_text'))) ? $request->get('review_text') : "";
        $review->status = 1; //Auto approved review.
        $review->save();

        // SAVE REVIEW AS WEB NOTIFICATION FOR SERVICE PROVIDER AND THEIR SUPERVISORS
            // Get customer user details
            $user = \App\User::find($customer_id);

            // Save notification for Service Provider
            \App\WebNotification::create([
                'notification_for' => $shop_id,
                'user_id' => $customer_id,
                'event_type' => 2,  // Review recieved about the shop
                'event' => 'New Review Given by '. $user->name,
            ]);

            // Save web notification for all the supervisors under this shop

            // DB::enableQueryLog();

            // Find all supervisors under this shop
            $supervisors = \App\User::where([
                ['shop_id', '=', $shop_id],
                ['isAdmin', '=', 1],
                ['user_type', '=', 1],
            ])
            ->get()
            ->toArray();

            // dd(DB::getQueryLog());

            foreach($supervisors as $supervisor) {
                // Save notification for Service Provider
                \App\WebNotification::create([
                    'notification_for' => $supervisor['id'],
                    'user_id' => $customer_id,
                    'event_type' => 2,  // Review recieved about the shop
                    'event' => 'New Review Given by '. $user->name,
                ]);
            }

        return response()->json([
                    "success" => true,
                    'message' => $this->message("review_submit_success", $customer_id),
                        ], 200);
    }

}
