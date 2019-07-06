<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\ShopService;
use App\ShopImage;
use App\ShopWorkingHour;
use App\Service;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

/* For mail */
use Illuminate\Support\Facades\Mail;
use App\Mail\Admin\ServiceProviderApprove;
use App\Mail\BlockAccount;

class ProviderController extends Controller {

    use AuthenticatesUsers;

    public function __construct() {
        $this->middleware('auth:admin')->except(['sp_login']);
    }

    /**
     * $type : 0 = Create, 1 = Update
     */
    protected function createServiceProvider($request, $type = 0) {
        if ($type == 0) {
            $user = new User();
            $user->email = $request->get('email');
            $user->phone = $request->get('phone');
            $user->password = Hash::make('123456');
            $user->unique_id = $this->unique_key("SP", "users");
            $user->isAdmin = 1;
            $user->gender = 'm';
            $user->user_type = 0;
            $user->status = 3;
        } else {
            $user = User::findOrFail($request->id);
            $user->email = $request->get('email');
            $user->phone = $request->get('phone');
        }

        $user->name = $request->get('name');
        $user->area_id = (!empty($request->get('area_id')) ? $request->get('area_id') : 0);
        $user->address = $request->get('address');
        $user->incharge_name = $request->get('incharge_name');

        /* First  preg_match check url contain latlong or not
         * Second preg_match Get the latlong to variable
         */
        $mapLocation = $this->_mapUrlLatLong($request->get('map'));

        $user->map = $mapLocation['map'];
        $user->lat = $mapLocation['lat'];
        $user->long = $mapLocation['long'];

        $user->comment = (!empty($request->get('comment')) ? $request->get('comment') : "");
        $user->owner_name = $request->get('owner_name');
        $user->owner_phone = $request->get('owner_phone');
        $user->crn = (!empty($request->get('crn')) ? $request->get('crn') : "");
        $user->lincense = (!empty($request->get('lincense')) ? $request->get('lincense') : "");

        $user->man = $user->women = $user->kid = 0;

        if ($request->get('service_mw') == 'man') {
            $user->man = 1;
        }

        if ($request->get('service_mw') == 'women') {
            $user->women = 1;
        }

        if ($request->get('kid') == 'on') {
            $user->kid = 1;
        }

        $user->accept_payment = (!empty($request->get('accept_payment')) ? $request->get('accept_payment') : 0);
        $user->auto_approve = (!empty($request->get('auto_approve') && $request->get('auto_approve') == 1) ? 1 : 0);
        $user->commission_type = (!empty($request->get('commission_type') && $request->get('commission_type') == 1) ? 1 : 0);
        $user->commission = (!empty($request->get('commission')) ? $request->get('commission') : "");

        $user->save();

        return $user->id;
    }

    /**
     * //Update Services selection of shop
     */
    protected function createUpdateServices($request, $shop_id) {
        if (!empty($request->services)) {
            foreach ($request->services as $service_id) {
                $shopService = ShopService::where([
                            ['shop_id', '=', $shop_id],
                            ['service_id', '=', $service_id],
                        ])->first();

                if (!empty($shopService)) {
                    ShopService::find($shopService->id)->delete();
                }

                $serviceData = Service::findOrFail($service_id);

                $_shopService = new ShopService();
                $_shopService->shop_id = $shop_id;
                $_shopService->service_id = $serviceData->id;
                $_shopService->unique_id = $serviceData->unique_id;
                $_shopService->category_id = $serviceData->category_id;
                $_shopService->name = $serviceData->name;
                $_shopService->category_id = $serviceData->category_id;
                $_shopService->duration = $serviceData->duration;
                $_shopService->price = $serviceData->price;
                $_shopService->save();
            }
        }
    }

    /**
     * Upload shop images if exist
     */
    protected function createUpdateImages($request, $shop_id) {
        /* Insert Shop Images */
        if ($request->hasFile('images')) {
            $destinationPath = public_path() . '/images/shop';
            //echo $destinationPath;

            foreach ($request->file('images') as $key => $image) {
                $extension = $image->getClientOriginalExtension();
                $image_name = "Shop" . $shop_id . "_" . time() . mt_rand(1000, 9999) . "." . $extension;
                //$image_name = $image->getClientOriginalName();
                $image->move($destinationPath, $image_name);

                $_shopImage = new ShopImage();
                $_shopImage->shop_id = $shop_id;
                $_shopImage->filename = $image_name;
                $_shopImage->save();
            }
        }
    }

    /**
     * Upload shop images if exist
     */
    protected function createWorkingHours($request, $shop_id) {
        //ShopWorkingHour;
        /* Insert Shop Images */
        $dayWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        foreach ($dayWeek as $day) {
            $workingHour = new ShopWorkingHour();
            $workingHour->shop_id = $shop_id;
            $workingHour->is_open = 1;
            $workingHour->shop_weekday = $day;
            $workingHour->shop_starttime = '10:00';
            $workingHour->shop_closetime = '18:00';
            $workingHour->save();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = \Validator::make($request->all(), [
                    'name' => 'required|max:50',
                    'incharge_name' => 'nullable|max:50',
                    'owner_name' => 'nullable|max:50',
                    'owner_phone' => 'nullable|digits:10',
                    'email' => 'required|string|email|max:255|unique:users',
                    'phone' => 'required|digits:10|unique:users',
                    'commission' => 'nullable|numeric|between:1,99.99',
                        ], [
                    'name.regex' => 'The service provider name format is invalid.',
                    'images.*.mimes' => 'Only jpeg, jpg, png, bmp formats are allowed.',
                    'images.*.max' => 'Photos not be grater then 1MB.',
                    'phone.unique' => 'The service provider phone number has already been taken.',
                    'phone.digits' => 'The service provider phone number must be 10 digits.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        $user_id = $this->createServiceProvider($request);
        $this->createWorkingHours($request, $user_id);
        $this->createUpdateImages($request, $user_id);
        $this->createUpdateServices($request, $user_id);

        return response()->json(['success' => __('messages.sp_create_success')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request) {
        $validator = \Validator::make($request->all(), [
                    'name' => 'required|max:50',
                    'incharge_name' => 'max:50',
                    'owner_name' => 'nullable|max:50',
                    'owner_phone' => 'nullable|digits:10',
                    'images.*' => 'mimes:jpg,jpeg,png,gif,bmp',
                    'email' => 'required|string|email|max:255|unique:users,email,' . $request->id,
                    'phone' => 'digits:10|unique:users,phone,' . $request->id,
                    'commission' => 'nullable|numeric|between:0,99.99',
                        ], [
                    'name.regex' => 'The service provider name format is invalid.',
                    'images.*.mimes' => 'Only jpeg, jpg, png, bmp formats are allowed.',
                    'images.*.max' => 'Photos not be grater then 1MB.',
                    'phone.unique' => 'The service provider phone number has already been taken.',
                    'phone.digits' => 'The service provider phone number must be 10 digits.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        $user_id = $this->createServiceProvider($request, 1);
        $this->createUpdateImages($request, $user_id);
        $this->createUpdateServices($request, $user_id);

        return response()->json(['success' => __('messages.sp_update_success')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

    public function getProvider(Request $request) {
        $providerId = $request->id;
        $service = ShopService::where('shop_id', '=', $request->id)
                ->select('service_id')
                ->get();
        $_service = array();
        foreach ($service as $_s) {
            $_service[] = $_s->service_id;
        }

        $images = ShopImage::where('shop_id', '=', $request->id)
                ->select('filename', 'id')
                ->get();

        $providerData = User::findOrFail($providerId);
        return response()->json(['data' => $providerData, 'service' => $_service, 'shop_images' => $images]);
    }

    public function removeShopImage(Request $request) {
        $shopImgId = $request->id;


        $image = ShopImage::where('id', '=', $shopImgId)
                ->select('filename', 'id')
                ->first();

        $file = $image->filename;

        $filename = public_path() . '/images/shop/' . $file;

        \File::delete($filename);

        ShopImage::where('id', '=', $shopImgId)->delete();

        //prd($image);
        return response()->json(['success' => __('messages.shop_image_remove_success')]);
    }

    public function viewProvider(Request $request) {
        $providerId = $request->id;
        $service = ShopService::where('shop_id', '=', $request->id)
                ->select('service_id')
                ->get();
        $_service = array();
        foreach ($service as $_s) {
            $_service[] = $_s->service_id;
        }

        $images = ShopImage::where('shop_id', '=', $request->id)
                ->select('filename')
                ->get();

        $providerData = User::findOrFail($providerId);
        return response()->json(['data' => $providerData, 'service' => $_service, 'shop_images' => $images]);
    }

    public function sp_activate(Request $request) {
        $userData = User::findOrFail($request->user_id);
        $_passcode = 'SP#' . mt_rand(1000, 9999);
        $userData->password = Hash::make($_passcode);
        $userData->status = 1;
        $userData->passcode = $_passcode;

        // Approve mail from admin
        Mail::to($userData->email)->send(new ServiceProviderApprove($userData));
        unset($userData->passcode);
        if ($userData->save()) {
            return redirect('admin/providers')->with('success', __('messages.sp_activate_success'));
        } else {
            return redirect('admin/providers')->with('error', __('messages.sp_activate_error'));
        }
    }

    public function block() {
        $users = User::latest()
                ->join('areas', 'areas.id', '=', 'users.area_id')
                ->select('users.*', 'areas.name as area_name')
                ->where('user_type', '=', 0)
                ->where('status', '=', 0)
                ->get();
        //prd($services);
        return view('admin.users.blockproviders', compact('users'));
    }

    public function providerBlock(Request $request) {
        $userData = User::findOrFail($request->id);
        $userData->status = 0; // O == Block/ Inactive
        $userData->api_token = null;
        $userData->save();

        //Mail to block customer
        Mail::to($userData->email)->send(new BlockAccount($userData));

        return response()->json(['success' => __('messages.sp_block_success')]);
    }

    public function providerUnblock(Request $request) {
        $userData = User::findOrFail($request->id);
        $userData->status = 1; // 1 = active
        $userData->save();
        return response()->json(['success' => __('messages.sp_unblock_success')]);
    }

    /** Function to login admin into service provider account */
    public function sp_login(Request $request) {
        //$userData = User::findOrFail($request->user_id);
        //Auth::login($userData);
        //return redirect()->intended('home');

        Auth::loginUsingId($request->user_id);
        return redirect()->intended('home');
    }

}
