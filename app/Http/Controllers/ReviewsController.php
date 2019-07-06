<?php

namespace App\Http\Controllers;

use Auth;
use App\ShopReview;
use Illuminate\Http\Request;

class ReviewsController extends Controller {

    public function __construct() {
        $this->middleware('auth:web');
    }

    public function index() {
        $shop_id = $this->_shop_id();

        $reviews = ShopReview::Where('status', '!=', 2)
                ->where('shop_id', '=', $shop_id)
                ->with(['customer', 'shop'])
                ->orderBy('status')
                ->orderBy('created_at', 'DESC')
                ->paginate(20);

        return view('reviews.index', compact('reviews'))
                        ->with('i', (request()->input('page', 1) - 1) * 5);
    }
    
    public function flagged(Request $request) {
        $reviewData = ShopReview::findOrFail($request->review_id);
        
        $reviewData->is_flagged = 1;

        if ($reviewData->save()) {
            // Send Flagged Review Web Notification to Admin User
            $shop_id = $this->_shop_id();
            $admin_user_id = $this->_admin_id();

            // SAVE BOOKING AS WEB NOTIFICATION FOR SERVICE PROVIDER AND THEIR SUPERVISORS
            // Get Shop user details
            $shop = \App\User::find($shop_id);

            // Save notification for Admin USer
            \App\WebNotification::create([
                'notification_for' => $admin_user_id,
                'user_id' => $shop_id,
                'event_type' => 3,  // Flagged as bad review by Service provider (Shop)
                'event' => 'Review Flagged by '. $shop->name,
            ]);

            return redirect('reviews')->with('success', __('messages.review_successfully_flagged'));
        } else {
            return redirect('reviews')->with('error', 'Some error occur. Review cannot be flagged');
        }
    }

}
