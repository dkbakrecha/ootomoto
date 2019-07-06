<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;
use App\User;

class LoginController extends Controller {
    /*
      |--------------------------------------------------------------------------
      | Login Controller
      |--------------------------------------------------------------------------
      |
      | This controller handles authenticating users for the application and
      | redirecting them to your home screen. The controller uses a trait
      | to conveniently provide its functionality to your applications.
      |
     */

use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request) {
        // Validate the form data
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);
        /* $is_remember = 0;
          if(isset($request->remember)){
          $is_remember = 1;
          } */
        //return $request;
        // Attempt to log the user in
        if (Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password, 'user_type' => [0, 1], 'status' => 1], $request->remember)) {
            // if successful, then redirect to their intended location
            return redirect()->intended('home');
        }
        // if unsuccessful, then redirect back to the login with the form data
        //return redirect()->back()->withInput($request->only('email', 'remember'));
        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request) {
        $errors = [$this->username() => trans('auth.failed')];

        // Load user from database
        $user = User::where($this->username(), $request->{$this->username()})->first();

        // Check if user was successfully loaded, that the password matches
        // and active is not 1. If so, override the default error message.
        if ($user && \Hash::check($request->password, $user->password)) {
            if ($user->status == 3) {
                $errors = [$this->username() => trans('Your account is not approved by admin, please contact support team for more details.')];
            } elseif ($user->status == 0) {
                $errors = [$this->username() => trans('Your shop account is blocked by admin, please contact support team for more details.')];
            }
        }

        /* Block Supervisor concept */
        if ($user->user_type == 1) {
            $shopData = User::where('id', '=', $user->user_type)->first();
            if($shopData->status == 0){
                $errors = [$this->username() => trans('Your shop account is blocked by admin, please contact support team for more details.')];
            }
        }
        //prd($user);
        if (!empty($user)) {
            $errors = ['password' => trans('Your login credentials are invalid.')];
        }
        /* else{
          $errors = [$this->username() => trans('Your login credentials are invalid.')];
          } */

        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }
        return redirect()->back()
                        ->withInput($request->only($this->username(), 'remember'))
                        ->withErrors($errors);
    }
    
    public function logout() {
        Auth::guard('web')->logout();
        return redirect()->route('login');
    }

}
