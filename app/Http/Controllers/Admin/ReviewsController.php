<?php

namespace App\Http\Controllers\Admin;

use App\ShopReview;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReviewsController extends Controller {

    /**
     * Status 
     * 3 = Pending -- Now Not used
     * 1 = Approved
     * 2 = Rejected
     */
    public function __construct() {
        $this->middleware('auth:admin');
    }

    /**
     * Only once flagged or rejected reviews listing seen by admin
     * Updated : March 14, 2019
     */
    public function index() {
        $reviews = ShopReview::Where(function ($query) {
                    $query->where('status', '=', 2)
                    ->orWhere('is_flagged', '=', 1);
                })
                ->with(['customer', 'shop'])
                ->orderBy('id', 'DESC')
                ->orderBy('status', 'DESC')
                ->paginate(20);

        return view('admin.reviews.index', compact('reviews'))
                        ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function review_approve(Request $request) {
        $reviewData = ShopReview::findOrFail($request->review_id);
        $reviewData->status = 1; //For Approved

        if ($reviewData->save()) {
            return redirect('admin/reviews')->with('success', __('messages.review_activate_success'));
        } else {
            return redirect('admin/reviews')->with('error', __('messages.sys_error'));
        }
    }

    public function review_reject(Request $request) {
        $reviewData = ShopReview::findOrFail($request->review_id);
        $reviewData->status = 2; //For rejected

        if ($reviewData->save()) {
            return redirect('admin/reviews')->with('success', __('messages.review_reject_success'));
        } else {
            return redirect('admin/reviews')->with('error', __('messages.sys_error'));
        }
    }

}
