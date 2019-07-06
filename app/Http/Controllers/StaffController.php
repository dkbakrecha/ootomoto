<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\BarberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\StaffCredential;

class StaffController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $users = User::latest()
                ->where('name', 'like', "%" . $request->q . "%")
                ->where('user_type', '=', 1)
                ->where('status', '!=', 2)
                ->where('shop_id', '=', $this->_shop_id())
                ->paginate(8);

        $users->appends(array(
            'q' => $request->q
        ));

        $query = "";
        if (!empty($request->q)) {
            $query = $request->q;
        }

        //prd($users->count());
        return view('provider.staff.index', ['users' => $users, 'q' => $query])
                        ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Store a newly created resource in storage.
     *
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
                    'name' => 'required|string|max:50',
                    'email' => 'required|string|email|max:255|unique:users',
                    'phone' => 'required|digits:10|unique:users',
                    'profession' => 'required',
                    'profile_image' => 'mimes:jpg,jpeg,png,bmp|max:1024',
                        ], [
                    'profile_image.mimes' => __('messages.image_format_validate'),
                    'profile_image.max' => __('messages.image_size_validate'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }



        $staff = new User();
        $staff->name = $request->get('name');
        $staff->gender = 'm';
        $staff->email = $request->get('email');
        $staff->phone = $request->get('phone');
        $staff->profession = $request->get('profession');
        $staff->area_id = 0;
        $staff->password = Hash::make('#DemoStaff132');
        $staff->unique_id = $this->unique_key("STF", "users");
        $staff->isAdmin = ($request->get('isAdmin') == 'on') ? 1 : 0;
        $staff->user_type = 1;
        $staff->status = 3; //Not Activated
        $staff->shop_id = $this->_shop_id();

        if ($request->hasFile('profile_image')) {
            $file = $request->profile_image;
            $_filename = "profile_" . time() . ".png";
            $destinationPath = public_path() . '/images/profile/';
            $file->move($destinationPath, $_filename);

            $staff->profile_image = $_filename;
        }

        $staff->save();

        if ($staff->isAdmin == 1) {
            $this->_staff_credentials($staff->id);
        }

        if (!empty($request->service)) {
            BarberService::where([
                ['shop_id', '=', $this->_shop_id()],
                ['barber_id', '=', $staff->id]
            ])->delete();

            foreach ($request->service as $category_id => $services) {
                foreach ($services as $service_id => $val) {
                    $barber_service = new BarberService();
                    $barber_service->shop_id = $this->_shop_id();
                    $barber_service->barber_id = $staff->id;
                    $barber_service->category_id = $category_id;
                    $barber_service->service_id = $service_id;
                    $barber_service->save();
                }
            }
        }

        return response()->json(['success' => __('messages.staff_successfully_added')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
                    'name' => 'required|string|max:50',
                    'email' => 'required|string|email|max:255|unique:users,email,' . $request->id,
                    //'phone' => 'required|digits:10|unique:users',
                    'profession' => 'required',
                    'profile_image' => 'mimes:jpg,jpeg,png,bmp|max:1024',
                        ], [
                    'profile_image.mimes' => __('messages.image_format_validate'),
                    'profile_image.max' => __('messages.image_size_validate'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        $staff = User::findOrFail($request->id);
        $staff->name = $request->get('name');
        $staff->email = $request->get('email');
        $staff->isAdmin = ($request->get('isAdmin') == 'on') ? 1 : 0;
        $staff->profession = $request->get('profession');

        if ($request->hasFile('profile_image')) {
            $file = $request->profile_image;
            $_filename = "profile_" . time() . ".png";
            $destinationPath = public_path() . '/images/profile/';
            $file->move($destinationPath, $_filename);

            $staff->profile_image = $_filename;
        }

        $staff->save();

        if (!empty($request->service)) {
            BarberService::where([
                ['shop_id', '=', $this->_shop_id()],
                ['barber_id', '=', $request->id]
            ])->delete();

            foreach ($request->service as $category_id => $services) {
                foreach ($services as $service_id => $val) {
                    $barber_service = new BarberService();
                    $barber_service->shop_id = $this->_shop_id();
                    $barber_service->barber_id = $request->id;
                    $barber_service->category_id = $category_id;
                    $barber_service->service_id = $service_id;
                    $barber_service->save();
                }
            }
        }

        return response()->json(['success' => __('messages.staff_successfully_updated')]);
    }

    public function delete(Request $request) {
        $staffData = User::find($request->staff_id);
        $staffData->status = 2;
        $staffData->save();
        return redirect('staff')->with('success', __('messages.staff_successfully_deleted'));
    }

    public function send_credentials(Request $request) {
        $this->_staff_credentials($request->staff_id);
        return redirect('staff')->with('success', __('messages.new_credentials_sent'));
    }

    public function _staff_credentials($staff_id) {
        $staffData = User::find($staff_id);
        
        $_password = "@stf#" . mt_rand(1000, 9999);
        $staffData->password = Hash::make($_password);
        $staffData->status = 1; //Activated
        $staffData->save();

        $staff = array();
        $staff['name'] = $staffData->name;
        $staff['email'] = $staffData->email;
        $staff['password'] = $_password;

        Mail::to($staffData->email)->send(new StaffCredential($staff));
    }

    public function getStaff(Request $request) {
        $providerId = $request->id;
        $providerData = User::findOrFail($providerId);

        $barberService = BarberService::where([
                    ['shop_id', '=', $this->_shop_id()],
                    ['barber_id', '=', $request->id]
                ])->get();
        $_bs = [];
        foreach ($barberService as $bs) {
            $_bs[] = "service[$bs->category_id][$bs->service_id]";
        }


        return response()->json(['data' => $providerData, 'barberService' => $_bs]);
    }

}
