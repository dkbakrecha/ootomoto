<?php

namespace App\Http\Controllers\Admin;

use App\CouponCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CouponCodesController extends Controller {

    public function __construct() {
        $this->middleware('auth:admin');
    }

    public function index() {
        $couponcodes = CouponCode::latest()
                ->get();

        return view('admin.coupon_codes.index', compact('couponcodes'));
    }

    public function store(Request $request) {
        $validator = \Validator::make($request->all(), [
                    'coupon_code' => 'required|max:50',
                    'coupon_type' => 'required',
                    'coupon_amount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        $couponCode = new CouponCode();
        $couponCode->coupon_code = $request->coupon_code;
        $couponCode->coupon_type = $request->coupon_type;
        $couponCode->coupon_amount = $request->coupon_amount;
        $couponCode->save();

        return response()->json(['success' => __('messages.coupon_create_success')]);
    }

    public function getCouponInfo(Request $request) {
        $couponCode = CouponCode::findOrFail($request->id);

        return response()->json(['data' => $couponCode]);
    }

    public function code_active(Request $request) {
        $codeData = CouponCode::findOrFail($request->coupon_id);
        $codeData->status = 1;

        if ($codeData->save()) {
            return redirect('admin/coupon_code')->with('success', __('messages.coupon_activate'));
        } else {
            return redirect('admin/coupon_code')->with('error', __('messages.sys_error'));
        }
    }

    public function code_inactive(Request $request) {
        $codeData = CouponCode::findOrFail($request->coupon_id);
        $codeData->status = 0;

        if ($codeData->save()) {
            return redirect('admin/coupon_code')->with('success', __('messages.coupon_deactivate'));
        } else {
            return redirect('admin/coupon_code')->with('error', __('messages.sys_error'));
        }
    }

}
