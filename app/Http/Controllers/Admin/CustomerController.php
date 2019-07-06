<?php

namespace App\Http\Controllers\Admin;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\BlockAccount;

class CustomerController extends Controller {

    public function __construct() {
        $this->middleware('auth:admin');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
                    'name' => 'required|max:50',
                    'email' => 'required|string|email|max:255|unique:users',
                    'phone' => 'required|digits:10|unique:users',
                    'gender' => 'required|in:m,f',
                    'area_id' => 'required',
                    'address' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        $customer = new User();
        $customer->name = $request->get('name');
        $customer->gender = $request->get('gender');
        $customer->email = $request->get('email');
        $customer->phone = $request->get('phone');
        $customer->area_id = $request->get('area_id');
        $customer->address = $request->get('address');
        $customer->password = Hash::make('123456');
        $customer->unique_id = $this->unique_key("CUS", "users");
        $customer->isAdmin = 2;
        $customer->user_type = 2;
        $customer->status = 1;
        $customer->save();

        return response()->json(['success' => __('messages.customer_add_success')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $validator = \Validator::make($request->all(), [
                    'name' => 'required|max:50',
                    'email' => 'string|email|max:255|unique:users,email,' . $request->id,
                    'phone' => 'digits:10|unique:users,phone,' . $request->id,
                    'area_id' => 'required',
                    'address' => 'required',
                    'gender' => 'required|in:m,f'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        $user = User::findOrFail($request->id);
        $user->name = $request->get('name');
        $user->area_id = $request->get('area_id');
        $user->email = $request->get('email');
        $user->phone = $request->get('phone');
        $user->address = $request->get('address');
        $user->gender = $request->get('gender');
        $user->save();

        return response()->json(['success' => __('messages.customer_update_success')]);
    }

    public function getCustomer(Request $request) {
        $customerId = $request->id;
        $customerData = User::findOrFail($customerId);
        return response()->json(['data' => $customerData]);
    }

    public function viewCustomer(Request $request) {
        $customerId = $request->id;
        $customerData = User::Where('id', '=', $customerId)->with(['area'])->first();

        $_visit = \App\Booking::Where('customer_id', '=', $customerId)
                ->where(function ($query) {
                    $query->where('status', '=', 1);
                })
                ->count();

        $_payments = \App\Booking::Where('customer_id', '=', $customerId)
                        ->where(function ($query) {
                            $query->where('status', '=', 1);
                        })
                        ->get([
                            DB::raw('SUM( final_amount ) as amount'),
                        ])->first();

        $customerData->visit = $_visit;
        $customerData->payments = (!empty($_payments->amount)) ? $_payments->amount : 0;
        $customerData->payments = $customerData->payments . " SAR";

        return response()->json(['data' => $customerData]);
    }

    public function block() {
        $users = User::latest()
                ->where('user_type', '=', 2)
                ->where('status', '=', 0)
                ->get();
        //prd($services);
        return view('admin.customer.block', compact('users'));
    }

    public function customerBlock(Request $request) {
        $userData = User::findOrFail($request->id);
        $userData->status = 0; // O == Block/ Inactive
        $userData->api_token = null;
        $userData->save();

        //Mail to block customer
        Mail::to($userData->email)->send(new BlockAccount($userData));

        return response()->json(['success' => __('messages.cus_block_success')]);
    }

    public function customerUnblock(Request $request) {
        $userData = User::findOrFail($request->id);
        $userData->status = 1; // 1 == Active
        $userData->no_show_count = 0; //Relese suspending consition
        $userData->save();
        return response()->json(['success' => __('messages.cus_unblock_success')]);
    }

}
