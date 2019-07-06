<?php

namespace App\Http\Controllers;

use Auth;
use App\ShopWorkingHour;
use Illuminate\Http\Request;

class ProvidersController extends Controller {

    public function __construct() {
        $this->middleware('auth:web');
    }

    public function getWorkingHours() {

        $workingHours = ShopWorkingHour::Where('shop_id', '=', $this->_shop_id())->get()->toArray();

        if (empty($workingHours)) {
            $this->createWorkingHours($this->_shop_id());
            $workingHours = ShopWorkingHour::Where('shop_id', '=', $this->_shop_id())->get()->toArray();
        }

        return view('provider.working_hours', compact('workingHours'));
    }

    public function updateWorkingHours(Request $request) {
        $data = $request->workinghours;
        $shop_id = $this->_shop_id();
        $this->validate($request, [
            'workinghours.*.shop_closetime' => 'after:workinghours.*.shop_starttime',
                ], [
            'workinghours.*.shop_closetime.after' => __('messages.working_hours_validate')
        ]);

        foreach ($data as $key => $value) {
            $workingHour = ShopWorkingHour::Where('shop_id', '=', $shop_id)
                    ->where('shop_weekday', '=', $key)
                    ->first();

            $workingHour->is_open = (isset($value['is_open']) && $value['is_open'] == 'on') ? 1 : 0;
            if ((isset($value['is_open']) && $value['is_open'] == 'on')) {
                $workingHour->shop_starttime = $value['shop_starttime'];
                $workingHour->shop_closetime = $value['shop_closetime'];
            }

            $workingHour->save();
        }

        return redirect()->back()->with("success", __("messages.working_hours_update_success"));
    }

    protected function createWorkingHours($shop_id) {
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

}
