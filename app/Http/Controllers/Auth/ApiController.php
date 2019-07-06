<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Service;
use App\ShopFavorite;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Mail;
use App\Mail\Api\RegisterToken;
use Auth;
use DB;
use Intervention\Image\ImageManagerStatic as Image;
use URL;
use App\SiteContent;
use App\SiteSetting;
use App\ShopReview;
use App\SignupRequest;
use \Carbon\Carbon;

class ApiController extends Controller {

    use AuthenticatesUsers;

    protected function createCustomer(array $data, $token) {
        return User::create([
                    'name' => $data['name'],
                    'unique_id' => $this->unique_key("CUS", "users"),
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'isAdmin' => 2, // 2 = Customer
                    'user_type' => 2, // 2 = Customer
                    'status' => 3, // 3 pending
                    'gender' => !empty($data['gender'])?$data['gender']:'m',
                    'phone' => $data['phone'],
                    'token' => $token,
                    'confirmation_alert' => 1 //Auto On alert
        ]);
    }

    public function register(Request $request) {
        // Here the request is validated. The validator method is located
        // inside the RegisterController, and makes sure the name, email
        // password and password_confirmation fields are required.
        //$validator = $this->validator($request->all());
        // CHECK IF THIS USER HAS ALREADY ATTEMPTED TO REGISTER BUT DIDNT VERIFY THEIR PHONE
        $userExist = User::where('phone', '=', $request->phone)
                ->where('is_phone_verified', '=', 0)
                ->first();

        if (!empty($userExist)) {

            $token = mt_rand(1000, 9999);

            $userExist->update([
                'token' => $token
            ]);

            //$sms_response = $this->sendSMS($request->phone, $token)->toArray();

            $userData = $userExist->toArray();
            $removeKeys = [
                'address', 'map', 'lat', 'long', 'incharge_name', 'owner_name', 'owner_phone',
                'crn', 'lincense', 'comment',
            ];
            foreach ($removeKeys as $key) {
                unset($userData[$key]);
            }

            return response()->json(['success' => true, 'data' => $userData], 201);
        }

        $validator = Validator::make($request->all(), [
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users',
                    'phone' => 'required|min:10|max:15|unique:users',
                    'password' => 'required|string|min:6|max:12',
                    'gender' => 'required|in:m,f',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }


        // A Registered event is created and will trigger any relevant
        // observers, such as sending a confirmation email or any 
        // code that needs to be run as soon as the user is created.
        //event(new Registered($user = $this->create($request->all())));
        $token = mt_rand(1000, 9999);

        $user = $this->createCustomer($request->all(), $token);

        /*
          SEND OTP TO THE USER THROUGH AWS SNS
         */
        //$sms_response = $this->sendSMS($request->phone, $token)->toArray();

        // Currently Token send on email
        Mail::to($request->email)->send(new RegisterToken($token));

        // CHECK IF WE RECEIVED DEVICE TOKEN IN THE REQUEST
        if ($request->device_token != null) {
            $this->addDeviceToken($request, $user);
        }

        //To do  -- Need to send SMS verify
        // After the user is created, he's logged in.
        $this->guard()->login($user);

        // And finally this is the hook that we want. If there is no
        // registered() method or it returns null, redirect him to
        // some other URL. In our case, we just need to implement
        // that method to return the correct response.
        return $this->registered($request, $user) ?: redirect($this->redirectPath());
    }

    public function user_verify(Request $request) {
        // Here the request is validated. The validator method is located
        // inside the RegisterController, and makes sure the name, email
        // password and password_confirmation fields are required.
        //$validator = $this->validator($request->all());
        // CHECK IF THIS USER HAS ALREADY ATTEMPTED TO REGISTER BUT DIDNT VERIFY THEIR PHONE
        $userExist = User::where('phone', '=', $request->phone)
                ->where('is_phone_verified', '=', 0)
                ->first();

        if (!empty($userExist)) {

            $token = mt_rand(1000, 9999);

            $msg = "It seam your account already exit. Please login or forgat passowrd.";
            

            return response()->json(['success' => true, 'msg' => $msg], 201);
        }else{
            $reqExist = SignupRequest::where('phone', '=', $request->phone)
                ->first();
            $token = mt_rand(1000, 9999);               

            if(!empty($reqExist)){
                $reqExist->otp = $token;
                $reqExist->save();
            }else{
                $snReq = new SignupRequest();
                $snReq->phone = $request->get('phone');
                $snReq->email = $request->get('email');
                $snReq->otp = $token;
                $snReq->is_registered = 0;
                $snReq->save();
            }

            $userData = array();
            $userData['token'] = $token;
                

                return response()->json(['success' => true, 'data' => $userData], 201);
        }

        prd($token);

        $validator = Validator::make($request->all(), [
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users',
                    'phone' => 'required|min:10|max:15|unique:users',
                    'password' => 'required|string|min:6|max:12',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }


        // A Registered event is created and will trigger any relevant
        // observers, such as sending a confirmation email or any 
        // code that needs to be run as soon as the user is created.
        //event(new Registered($user = $this->create($request->all())));
        $token = mt_rand(1000, 9999);

        $user = $this->createCustomer($request->all(), $token);

        /*
          SEND OTP TO THE USER THROUGH AWS SNS
         */
        //$sms_response = $this->sendSMS($request->phone, $token)->toArray();

        // Currently Token send on email
        Mail::to($request->email)->send(new RegisterToken($token));

        // CHECK IF WE RECEIVED DEVICE TOKEN IN THE REQUEST
        if ($request->device_token != null) {
            $this->addDeviceToken($request, $user);
        }

        //To do  -- Need to send SMS verify
        // After the user is created, he's logged in.
        $this->guard()->login($user);

        // And finally this is the hook that we want. If there is no
        // registered() method or it returns null, redirect him to
        // some other URL. In our case, we just need to implement
        // that method to return the correct response.
        return $this->registered($request, $user) ?: redirect($this->redirectPath());
    }

    protected function registered(Request $request, $user) {
        $deviceToken = "";
        if (!empty($request->device_token)) {
            $deviceToken = $request->device_token;
        }

        $user->generateToken($deviceToken);

        $userData = $user->toArray();
        $removeKeys = [
            'address', 'map', 'lat', 'long', 'incharge_name', 'owner_name', 'owner_phone',
            'crn', 'lincense', 'comment',
        ];
        foreach ($removeKeys as $key) {
            unset($userData[$key]);
        }


        return response()->json(['success' => true, 'data' => $userData], 201);
    }

    //Login Concept HERE API

    public function login(Request $request) {
        //$this->validateLogin($request);

        if (is_numeric($request->get('email'))) {
            // API check Phone validation && password
            $validator = Validator::make($request->all(), [
                        'email' => 'required|digits:10',
                        'password' => 'required|string',
            ]);

            $userInfo = User::select('users.id', 'users.email', 'users.phone', 'users.status', 'users.is_phone_verified')
                            ->where('phone', '=', $request->email)->first();
        } else {
            // API check Email address validation && password
            $validator = Validator::make($request->all(), [
                        'email' => 'required|string|email',
                        'password' => 'required|string',
            ]);

            $userInfo = User::select('users.id', 'users.email', 'users.phone', 'users.status', 'users.is_phone_verified')
                            ->where('email', '=', $request->email)->first();
        }

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }



        if ($this->attemptLogin($request)) {
            /* Message To customer if his account is block due to suspended */
            $_cancelNumber = \App\SiteSetting::where('unique_key', '=', 'CANCELLATION_NUMBER')->first();
            $cancelNumber = (!empty($_cancelNumber->value) && $_cancelNumber->value > 0) ? $_cancelNumber->value : 3; // Default value 3
            
            if ($userInfo->no_show_count > $cancelNumber) {
                return $this->sendError("Your account is suspended by admin, please contact support team for more details.", null, 400);
            }

            /* Message To customer if his account is block normally */
            if ($userInfo->status == 0) {
                return $this->sendError("Your account is blocked by admin, please contact support team for more details.", null, 400);
            }

            $user = $this->guard()->user();

            // CHECK IF WE RECEIVED DEVICE TOKEN IN THE REQUEST
            if ($request->device_token != null) {
                $this->addDeviceToken($request, $user);
            }

            $deviceToken = "";
            if (!empty($request->device_token)) {
                $deviceToken = $request->device_token;
            }

            $user->generateToken($deviceToken);

            $userData = $user->toArray();
            $removeKeys = [
                'address', 'map', 'lat', 'long', 'incharge_name', 'owner_name', 'owner_phone',
                'crn', 'lincense', 'comment', 'token', 'accept_payment', 'man', 'women', 'kid',
                'auto_approve', 'shop_id', 'profession', 'commission'
            ];
            foreach ($removeKeys as $key) {
                unset($userData[$key]);
            }

            return response()->json([
                        "success" => true,
                        'data' => $userData,
            ]);
        }

        //prd($this->sendFailedLoginResponse($request));

        if (!empty($userInfo['id'])) {
            //Email/Phone exist in system
            return $this->sendError('Login credentials are incorrect.');
        } else {
            return $this->sendError('apimsgs.login_error');
        }
    }

    public function logout(Request $request) {
        $user = Auth::guard('api')->user();

        if ($user) {
            $user->api_token = null;
            $user->device_token = null;
            $user->save();
        }

        return response()->json(["success" => true, 'message' => 'User logged out.'], 200);
    }

    /** API to update device token menually */
    public function device_token(Request $request) {
        $user = Auth::guard('api')->user();

        $validator = Validator::make($request->all(), [
                    'device_token' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }

        /* if ($user) {
          $user->device_token = $request->device_token;
          $user->save();
          } */

        if ($user == null) {
            return $this->sendError('User not defined', null, 400);
        }

        $this->addDeviceToken($request, $user);

        return response()->json(["success" => true, 'data' => 'Device token successfully added.'], 200);
    }

    protected function credentials(Request $request) {
        if (is_numeric($request->get('email'))) {
            return ['phone' => $request->get('email'), 'password' => $request->get('password')];
        }
        return $request->only($this->username(), 'password');
        //return $request->only($this->username(), 'password');
    }

    public function services(Request $request) {
        $services = Service::all();

        return response()->json([
                    "success" => true,
                    'data' => $services->toArray(),
                        ], 200);
    }

    public function vehicle_company(Request $request) {
        $_vehicle_company = \App\VehicleCompany::all();

        return response()->json([
                    "success" => true,
                    'data' => $_vehicle_company->toArray(),
                        ], 200);
    }

    public function vehicle_model(Request $request) {
        If (!empty($request->company_id)) {
            $_vehicle_model = \App\VehicleModel::where('vehicle_company_id', '=', $request->company_id)->get();
        } else {
            $_vehicle_model = \App\VehicleModel::all();
        }

        return response()->json([
                    "success" => true,
                    'data' => $_vehicle_model->toArray(),
                        ], 200);
    }

    

    

    public function shoplists(Request $request) {
        If (!empty($request->page)) {
            $page = $request->page;
        } else {
            $page = "1";
        }
        $perpage = "10";

        $offset = ($page - 1) * $perpage;

        if (!empty($request->distance)) {
            $max_distance = $request->distance;
        }
        if (!empty($request->lat)) {
            $lat = $request->lat;
        }

        if (!empty($request->long)) {
            $lng = $request->long;
        }

        $query = DB::table('users');

        $query->select('users.id', 'users.unique_id', 'users.name', 'users.address', 'users.lat', 'users.long', 'users.accept_payment', 'users.status');
        //$users = User::select('users.id', 'users.unique_id', 'users.name', 'users.address', 'users.lat', 'users.long', 'users.accept_payment', 'users.status');
        // For location distance filter
        //.000621371192
        if (!empty($lat) && !empty($lng) && !empty($request->distance)) {
            //$query->addSelect(DB::raw("ST_Distance_Sphere(point(users.lat, users.long),point($lat, $lng)) * .001 as dis"));
            $query->whereRaw("
       ST_Distance_Sphere(
            point(users.lat, users.long),
            point(?, ?)
        ) * .001 < $max_distance
    ", [
                $lat,
                $lng,
            ]);
        }

        if (!empty($request->search_term)) {
            //$query->where('name', '=', $request->search_term);
            //$query->Where('users.name', 'like', '%' . $request->search_term . '%');
            /* $query->where(function ($query) use ($request) {
              $query->where('users.name', 'like', '%' . $request->search_term . '%')
              ->orWhere('users.address', 'like', '%' . $request->search_term . '%');
              }); */

            $query->Join('areas', 'users.area_id', '=', 'areas.id')
                    ->where(function ($query) use ($request) {
                        $query->where('areas.name', 'like', '%' . $request->search_term . '%')
                        ->orWhere('users.name', 'like', '%' . $request->search_term . '%');
                    });
            //  ->where('areas.name', 'like', '%' . $request->search_term . '%');
        }
        if (!empty($request->kid)) {
            $query->Where('users.kid', '=', $request->kid);
        }

        if (!empty($request->men)) {
            $query->Where('users.man', '=', $request->men);
        }

        if (!empty($request->women)) {
            $query->Where('users.women', '=', $request->women);
        }

        if (!empty($request->accept_payment)) {
            $query->Where('users.accept_payment', '=', $request->accept_payment);
        }

        if (!empty($request->services)) {
            $serviceArr = explode(',', $request->services);
            /* $query->Join('shop_services', function ($join) {
              $join->on('users.id', '=', 'shop_services.shop_id')
              ->whereIn('shop_services.sevice_id', array(2, 3))
              ->groupBy('shop_services.shop_id');
              }); */

            //->addSelect(DB::raw('shop_services.shop_id as shop_id'))
            $query->Join('shop_services', 'users.id', '=', 'shop_services.shop_id')
                    ->whereIn('shop_services.service_id', $serviceArr);
        }

        if (!empty($request->day) && !empty($request->time)) {
            //$query->addSelect(DB::raw('shop_working_hours.is_open as is_open'))
            $query->Join('shop_working_hours', 'users.id', '=', 'shop_working_hours.shop_id')
                    ->where('shop_working_hours.shop_weekday', '=', $request->day)
                    ->where('shop_working_hours.is_open', '=', 1)
                    ->where(function ($query) use ($request) {
                        $query->where('shop_working_hours.shop_starttime', '<=', $request->time);
                        $query->where('shop_working_hours.shop_closetime', '>', $request->time);
                    });
            //->whereBetween($request->time, ['shop_working_hours.shop_starttime', 'shop_working_hours.shop_closetime']);
        }

        if (Auth::guard('api')->check()) {
            //When User Login
            $currentUserID = Auth::guard('api')->id();

            /*
              CODE TO UPDATE USER'S LAST LOGIN DATE AND TIME
             */
            $user = User::find($currentUserID)
                    ->update([
                'last_login_date' => Carbon::now()->toDateTimeString()
            ]);

            /*
              $favRow = DB::table('shop_favorites')
              ->select('shop_favorites.provider_id', DB::raw('IFNULL(shop_favorites.id, 0) as is_favorite'))
              ->where('shop_favorites.user_id', '=', $currentUserID);

              $query->addSelect('shopfavorites.is_favorite')
              ->leftJoinSub($favRow, 'shopfavorites', function ($join) {
              $join->on('users.id', '=', 'shopfavorites.provider_id');
              }); */

            $query->addSelect(DB::raw('IFNULL((SELECT shop_favorites.id FROM shop_favorites'
                            . ' WHERE users.id = shop_favorites.provider_id AND shop_favorites.user_id = ' . $currentUserID . '), "0") as is_favorite'));


            /* $query->addSelect(DB::raw('IFNULL(shop_favorites.id, 0) as is_favorite'))
              ->leftJoin('shop_favorites', function ($join) {
              $currentUserID = Auth::guard('api')->id();
              $join->on('users.id', '=', 'shop_favorites.provider_id')
              ->where('shop_favorites.user_id', '=', $currentUserID);
              }); */
        }
        //$users->where('user_type', '=', 0)->get();

        $query->where('user_type', '=', 0);
        $query->where('status', '=', 1);

        if (!empty($request->services)) {
            $query->groupBy('users.id');
        }

        $shopList = $query->skip($offset)
                ->take($perpage)
                ->get();

        foreach ($shopList as $shop) {

            $shop->average_rating = ShopReview::where('shop_id', '=', $shop->id)
                    ->where('status', '=', 1)
                    ->avg('rating');

            $shop->total_reviews = ShopReview::where('shop_id', '=', $shop->id)
                            ->where('status', '=', 1)
                            ->whereNotNull('review_text')->count();
        }


        //return $result;
        //return $users;
        return response()->json([
                    "success" => true,
                    'data' => $shopList->toArray(),
                        ], 200);
    }

    public function shopfavorite(Request $request) {
        $validator = Validator::make($request->all(), [
                    'provider_id' => 'required|integer',
                    'is_favorite' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }
        $currentUserID = Auth::guard('api')->id();

        $favShop = ShopFavorite::where([
                    ['user_id', '=', $currentUserID],
                    ['provider_id', '=', $request->provider_id],
                ])->first();

        if ($request->is_favorite == 1) {
            if (empty($favShop)) {
                // Add to favotire List
                ShopFavorite::create([
                    'user_id' => $currentUserID,
                    'provider_id' => $request->provider_id
                ]);

                return response()->json([
                            "success" => true,
                            'message' => $this->message("favorite_success", $currentUserID),
                                ], 200);
            } else {
                return response()->json([
                            "success" => true,
                            'message' => $this->message("favorite_exist", $currentUserID),
                                ], 200);
            }
        } else {
            if (!empty($favShop)) {
                // Remove from favotire List
                ShopFavorite::find($favShop->id)->delete();
            }

            return response()->json([
                        "success" => true,
                        'message' => $this->message("favorite_removed", $currentUserID),
                            ], 200);
        }
    }

    public function updatesetting(Request $request) {
        $validator = Validator::make($request->all(), [
                    'preferred_language' => 'in:en,ar',
                    'confirmation_alert' => 'in:0,1'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }
        
        $currentUserID = Auth::guard('api')->id();
        $userData = Auth::guard('api')->user();

        if (!empty($request->preferred_language)) {
            $userData->preferred_language = $request->preferred_language;
        }
        if (isset($request->confirmation_alert)) {
            $userData->confirmation_alert = $request->confirmation_alert;
        }
        $userData->save();

        return response()->json([
                    "success" => true,
                    'message' => $this->message("settings_updated", $currentUserID),
                        ], 200);
    }

    /**
     * Get List of shops which are added by Users as favorite
     */
    public function favorite(Request $request) {
        If (!empty($request->page)) {
            $page = $request->page;
        } else {
            $page = "1";
        }
        $perpage = "10";

        $offset = ($page - 1) * $perpage;

        $currentUserID = Auth::guard('api')->id();

        $favShops = ShopFavorite::where([
                    ['user_id', '=', $currentUserID],
                    ['users.status', '=', 1],
                ])
                ->Join('users', 'shop_favorites.provider_id', '=', 'users.id')
                /* ->join("users", function($query) {
                  $query->on('shop_favorites.provider_id', '=', 'users.id')
                  ->on("users.status", "=", "1");
                  }) */
                ->select('users.id', 'users.unique_id', 'users.name', 'users.address', 'users.lat', 'users.long', 'users.accept_payment', 'users.status')
                ->skip($offset)
                ->take($perpage)
                ->get();

        foreach ($favShops as $shop) {
            $shop->average_rating = ShopReview::where('shop_id', '=', $shop->id)
                    ->where('status', '=', 1)
                    ->avg('rating');

            $shop->total_reviews = ShopReview::where('shop_id', '=', $shop->id)
                            ->where('status', '=', 1)
                            ->whereNotNull('review_text')->count();
        }

        return response()->json([
                    "success" => true,
                    'data' => $favShops->toArray(),
                        ], 200);
    }

    public function profile() {
        $currentUserID = Auth::guard('api')->id();
        $userData = User::where([
                    ['id', '=', $currentUserID],
                ])
                ->select('users.id', 'users.unique_id', 'users.name', 'users.profile_image', 'users.phone', 'users.email', 'users.gender', 'users.preferred_language', 'users.confirmation_alert', 'users.address', 'users.status')
                ->first();

        $data = $userData->toArray();

        //$data['profile_image'] = public_path() . '/images/profile/' . $data['profile_image'];
        if (!empty($data['profile_image'])) {
            $data['profile_image'] = URL::to('/') . '/images/profile/' . $data['profile_image'];
        }
        return response()->json([
                    "success" => true,
                    'data' => $data,
                        ], 200);
    }

    public function update_profile(Request $request) {
        $currentUserID = Auth::guard('api')->id();

        $validator = Validator::make($request->all(), [
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users,email,' . $currentUserID,
                    'gender' => 'required|in:m,f',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }

        $user = User::findOrFail($currentUserID);
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->gender = $request->get('gender');
        if (!empty($request->get('address'))) {
            $user->address = $request->get('address');
        }

        if ($request->hasFile('profile_image')) {
            // $file = $request->file('profile_image');
            $file = $request->profile_image;

            $_filename = "profile_" . $currentUserID . time() . ".png";
            $destinationPath = public_path() . '/images/profile/';
            //$path = public_path() . '/images/profile/' . $_filename;
            //Image::make(file_get_contents($request->get('profile_image')))->save($path);
            $file->move($destinationPath, $_filename);

            $user->profile_image = $_filename;
            // -- TO DO Need to delete previous Images
            //return $user->profile_image;
            //return $file;
        }
        //return $request;
        /* if (!empty($request->get('profile_image'))) {
          return $request->get('profile_image');
          $_filename = "profile_" . $currentUserID . ".png";
          $path = public_path() . '/images/profile/' . $_filename;

          Image::make(file_get_contents($request->get('profile_image')))->save($path);

          $user->profile_image = $_filename;
          } */

        if (!empty($request->get('is_remove_profile')) && $request->get('is_remove_profile') == 1) {
            $user->profile_image = null;
        }

        $user->save();

        return response()->json([
                    "success" => true,
                    'message' => $this->message("profile_updated", $currentUserID),
                        ], 200);
    }

    public function change_password(Request $request) {
        $currentUserID = Auth::guard('api')->id();

        $validator = Validator::make($request->all(), [
                    'password' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }

        $user = User::findOrFail($currentUserID);
        $user->password = Hash::make($request->get('password'));
        $user->save();

        return response()->json([
                    "success" => true,
                    'message' => $this->message("password_updated", $currentUserID),
                        ], 200);
    }

    public function content(Request $request) {
        $validator = Validator::make($request->all(), [
                    'key' => 'required|in:ABOUTUS,CANCELLATION',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }

        $content = SiteContent::where([
                    ['unique_key', '=', $request->key],
                ])
                ->first();

        return response()->json([
                    "success" => true,
                    'data' => $content->toArray(),
                        ], 200);
    }

    public function setting(Request $request) {
        $settings = SiteSetting::get();
        $settingsList = SiteSetting::whereIn('unique_key', ['HELPEMAIL', 'HELPCONTACT', 'TIMESHLOT'])->pluck('value', 'unique_key');

        //$settingsList = SiteSetting::where('unique_key', '=', 'HELPEMAIL')->pluck('value', 'unique_key');
        //return $settingsList;
        return response()->json([
                    "success" => true,
                    'data' => $settingsList->toArray(),
                        ], 200);
    }

    /*
      VERIFY USER OTP DURING REGISTRATION
     */

    public function verifyOTP(Request $request) {
        $validator = Validator::make($request->all(), [
                    'token' => 'required|string|digits:4',
                    'phone' => 'required|min:10|max:15',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }

        $content = User::where([
                    ['phone', '=', $request->phone],
                ])
                ->first();

        if ($content == null) {
            return $this->sendError('Phone not found', null, 400);
        }

        if ($content->token != $request->token) {
            return $this->sendError('Invalid code', null, 400);
        }

        $content->is_phone_verified = 1;
        $content->status = 1;

        $content->save();

        return response()->json([
                    "success" => true,
                    'data' => $content->toArray(),
                        ], 200);
    }

    protected function addDeviceToken($request, $user) {

        if ($request == null || $user == null) {
            return $this->sendError('Invalid function call in ApiController => addDeviceToken()', null, 400);
        }

        $user->find($user->id)
                ->mobileSessions()
                ->delete();

        $mobile_session = new \App\MobileSessions([
            'user_id' => $user->id,
            'device_token' => $request->device_token,
            'device_type' => $request->device_type,
            'app_version' => $request->app_version,
            'status' => 1,
        ]);

        $user->mobileSessions()->save($mobile_session);
    }

    protected function addDeviceToken_old($request, $user) {

        if ($request == null || $user == null) {
            return $this->sendError('Invalid function call in ApiController => addDeviceToken()', null, 400);
        }



        // DB::enableQueryLog();
        // NOW CHECK IF THE DEVICE TOKEN ALREADY EXISTS IN THE DB
        $oldMobileSessions = $user->find($user->id)
                ->mobileSessions()
                ->where([
                    ['device_token', '=', $request->device_token],
                    ['status', '=', 1],
                ])
                ->get()
                ->toArray();

        // dd(DB::getQueryLog());
        // IF THIS DEVICE TOKEN IS NOT REGISTERED IN OUR SYSTEM
        // THEN FIRST UPDATE STATUS OF PREVIOUS DEVICE TOKEN TO 0
        //  THEN ADD IT TO mobile_sessions TABLE
        if (count($oldMobileSessions) == 0) {

            // \App\MobileSessions::where('user_id', '=', $user->id)
            // ->update(['status' => 0]);


            $user->find($user->id)
                    ->mobileSessions()
                    ->where('status', '=', 1)
                    ->update(['status' => 0]);

            $mobile_session = new \App\MobileSessions([
                'user_id' => $user->id,
                'device_token' => $request->device_token,
                'device_type' => $request->device_type,
                'app_version' => $request->app_version,
                'status' => 1,
            ]);
            $user->mobileSessions()->save($mobile_session);
        }
    }

    public function resendOTP(Request $request) {
        $validator = Validator::make($request->all(), [
                    'phone' => 'required|min:10|max:15',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }

        $user = User::where([
                    ['phone', '=', $request->phone],
                ])
                ->first();

        if ($user == null) {
            return $this->sendError('Phone number not found', null, 400);
        }

        $token = mt_rand(1000, 9999);

        $user->update([
            'token' => $token
        ]);
        /*
          SEND OTP TO THE USER THROUGH AWS SNS
         */
        $sms_response = $this->sendSMS($request->phone, $token)->toArray();

        return response()->json([
                    "success" => true,
                    'message' => 'OTP sent succesfully',
                        ], 200);
    }

}
