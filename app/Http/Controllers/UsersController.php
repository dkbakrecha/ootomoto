<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller {

    public function __construct() {
        $this->middleware('auth:web');
    }

    public function getChangePassword() {
        return view('users.change_password');
    }

    public function updatePassword(Request $request) {
        Validator::extendImplicit('current_password', function($attribute, $value, $parameters, $validator) {
            return \Hash::check($value, auth()->user()->password);
        });

        $this->validate($request, [
            'current_password' => 'required|current_password',
            'new_password' => 'required|between:8,12',
            'password_confirmation' => 'required|same:new_password',
                ], [
        ]);


        $user = Auth::user();
        $user->password = bcrypt($request->get('new_password'));
        $user->save();
        return redirect("/home")->with("success", __('messages.password_success_change'));
    }
    
    public function viewCustomer(Request $request) {
        $customerId = $request->id;
        $customerData = User::Where('id', '=', $customerId)->with(['area'])->first();
        return response()->json(['data' => $customerData]);
    }

}
