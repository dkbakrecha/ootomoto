<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\ShopOffer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;

class OffersController extends Controller {

    public function __construct() {
        $this->middleware('auth:admin');
    }

    public function index() {
        $offers = ShopOffer::latest('shop_offers.created_at')
                ->with(['shop'])
                ->where(function ($query) {
                    $query->where('status', '=', 1)  //Active
                    ->orWhere('status', '=', 2) //Inactive
                    ->orWhere('status', '=', 3) //Pending
                    ->orWhere('status', '=', 0);  //Rejected
                })
                ->get();

        return view('admin.offers.index', compact('offers'));
    }

    public function getOffer(Request $request) {
        $offerId = $request->id;
        $offerData = ShopOffer::findOrFail($offerId);
        $services = \App\Service::whereIn('id', explode(',', $offerData->services))
                        ->select('name')
                        ->get()->toArray();

        //$offerData->services = explode(',', $offerData->services);
        return response()->json(['data' => $offerData, 'services' => $services]);
    }

    /** Status Approve First Time */
    public function offer_approve(Request $request) {
        $offerData = ShopOffer::findOrFail($request->offer_id);
        $offerData->expire_date = Carbon::now()->addDays($offerData->days);
        $offerData->status = 1;

        if ($offerData->save()) {
            return redirect('admin/offers')->with('success', __('messages.offer_activate_success'));
        } else {
            return redirect('admin/offers')->with('error', __('messages.sys_error'));
        }
    }

    /** Status Reject First Time */
    public function offer_reject(Request $request) {
        $offerData = ShopOffer::findOrFail($request->offer_id);
        $offerData->status = 0;

        if ($offerData->save()) {
            return redirect('admin/offers')->with('success', __('messages.offer_reject_success'));
        } else {
            return redirect('admin/offers')->with('error', __('messages.sys_error'));
        }
    }

    /** Status Inactive  Expire date not updated */
    public function offer_inactive(Request $request) {
        $offerData = ShopOffer::findOrFail($request->offer_id);
        $offerData->status = 2;

        if ($offerData->save()) {
            return redirect('admin/offers')->with('success', __('messages.offer_inactive_success'));
        } else {
            return redirect('admin/offers')->with('error', __('messages.sys_error'));
        }
    }

    /** Status Active Expire date not updated */
    public function offer_active(Request $request) {
        $offerData = ShopOffer::findOrFail($request->offer_id);
        $offerData->status = 1;

        if ($offerData->save()) {
            return redirect('admin/offers')->with('success', __('messages.offer_active_success'));
        } else {
            return redirect('admin/offers')->with('error', __('messages.sys_error'));
        }
    }

}
