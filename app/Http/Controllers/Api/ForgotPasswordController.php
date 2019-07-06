<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use App\Mail\Api\ForgotPassword;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller {

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest');
    }

    //Forgot password=================

    public function sendToken(Request $request) {
        $data = $request->all();

        if (isset($data['email']))
            $data['email'] = strtolower($data['email']);

        if (is_numeric($request->get('email'))) {
            // API check Phone validation && password
            $data['phone'] = $data['email'];
            unset($data['email']);

            $validator = Validator::make($data, [
                        'phone' => 'required|exists:users',
                            ], [
                        'phone.required' => 'Please provide your phone to reset password.',
                        'phone.exists' => 'Seems this phone is not registered with us.'
            ]);
        } else {
            $validator = Validator::make($data, [
                        'email' => 'required|email|exists:users',
                            ], [
                        'email.required' => 'Please provide your email to reset password.',
                        'email.exists' => 'Seems this email is not registered with us.'
            ]);
        }



        if ($validator->fails()) {
            return $this->sendError( $validator->errors()->first(), null, 400);
        }

        $resetCode = mt_rand(1000, 9999);

        if (isset($data['phone']) && !empty($data['phone'])) {
            $user = User::where('phone', '=', $data['phone'])->first();
        } else {
            $user = User::where('email', '=', $data['email'])->first();
        }

        $user->token = $resetCode;
        $user->save();

        /*
          SEND OTP TO THE USER THROUGH AWS SNS
         */
        $sms_response = $this->sendSMS($user->phone, $resetCode)->toArray();
        //Mail::to($request->email)->send(new ForgotPassword($resetCode));
        //Mail::to($user->email)->send(new ForgotPassword($resetCode));

        $resData = [
            'resetCode' => $resetCode
        ];

        return response()->json(['success' => true, 'data' => $resData, 'message' => 'Reset password OTP sent successfully on your registered phone number'], 201);
    }

    //Reset Password =================
    public function reset(Request $request) {
        $data = $request->all();
        
        if (is_numeric($data['email'])) {
            $data['phone'] = $data['email'];
            unset($data['email']);

            $validator = Validator::make($data, [
                        'phone' => 'required|exists:users',
                        'token' => 'required',
                        'password' => 'required|confirmed|between:8,12',
                        'password_confirmation' => 'required',
                            ], [
                        'phone.required' => 'Please provide your phone to reset password.',
                        'phone.exists' => 'Seems this phone is not registered with us.'
            ]);
        } else {
            $validator = Validator::make($data, [
                        'email' => 'required|email',
                        'token' => 'required',
                        'password' => 'required|confirmed|between:8,12',
                        'password_confirmation' => 'required',
                            ], [
            ]);
        }


        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }

        if (isset($data['phone']) && !empty($data['phone'])) {
            $user = User::where([
                        ["token", "=", $request->input('token')],
                        ["phone", "=", $data['phone']],
                    ])->first();
        } else {
            $user = User::where([
                        ["token", "=", $request->input('token')],
                        ["email", "=", $request->input('email')],
                    ])->first();
        }

        if (empty($user->id)) {
            return $this->sendError('Verification code is invalid.', null, 400);
        }

        $user->password = bcrypt($request->input('password'));
        $user->token = null;

        $user->save();

        return response(array("success" => true, "message" => "Password Reset successfully"));
    }

}
