<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Password;
use Auth;
use DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Carbon;


class AdminResetPasswordController extends Controller {
    /*
      |--------------------------------------------------------------------------
      | Password Reset Controller
      |--------------------------------------------------------------------------
      |
      | This controller is responsible for handling password reset requests
      | and uses a simple trait to include this behavior. You're free to
      | explore this trait and override any methods you wish to tweak.
      |
     */

use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest:admin');
    }

    public function showResetForm(Request $request, $token = null) {
        /*$token = DB::table('password_resets')
                ->where('token', '=', $token)
                ->where('created_at', '>', Carbon::now()->subHours(2))
                ->first();
        
        pr($token);*/

        return view('auth.passwords.admin-reset')
                        ->with(['token' => $token, 'email' => $request->email]
        );
    }

    //defining which guard to use in our case, it's the admin guard
    protected function guard() {
        return Auth::guard('admin');
    }

    //defining our password broker function
    protected function broker() {
        return Password::broker('admins');
    }

    protected function resetPassword_oo($user, $password) {
        $user->password = Hash::make($password);

        $user->setRememberToken(Str::random(60));

        $user->save();

        //event(new PasswordReset($user));
        
        return redirect('/admin/login')->with('status', "Password reset successfully");

        //$this->guard()->login($user);
    }

}
