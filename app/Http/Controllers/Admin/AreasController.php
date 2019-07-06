<?php

namespace App\Http\Controllers\Admin;

use App\Area;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AreasController extends Controller {

    public function __construct() {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $areas = Area::latest()
                ->get();
        //prd($areas);
        return view('admin.areas.index', compact('areas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
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
                    'address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        $area = new Area();
        $area->name = $request->get('name');
        $area->unique_id = $this->unique_key("AR", "areas");
        $area->address = $request->get('address');

        $area->save();

        return response()->json(['success' => __('messages.area_create_success')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function show(Area $area) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function edit(Area $area) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request) {
        $validator = \Validator::make($request->all(), [
                    'name' => 'required|max:50',
                    'address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        $area = Area::findOrFail($request->id);
        $area->name = $request->get('name');
        $area->address = $request->get('address');
        $area->save();

        return response()->json(['success' => __('messages.area_update_success')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function destroy(Area $area) {
        $userCount = User::where('area_id', '=', $area->id)->get()->count();

        if ($userCount > 0) {
            return redirect('admin/area')->with('error', __('messages.area_delete_error'));
        } else {
            Area::find($area->id)->delete();
            return redirect('admin/area')->with('success', __('messages.area_delete_success'));
        }
    }

    public function getArea(Request $request) {
        $areaId = $request->id;
        $areaData = Area::findOrFail($areaId);
        return response()->json(['data' => $areaData]);
    }

    public function viewArea(Request $request) {
        $areaId = $request->id;
        $areaData = Area::where('id', '=', $areaId)->with(['bookings'])->first();
        
        $providerCount = User::where([['area_id', '=', $areaId], ['user_type', '=', 0]])->get()->count();
        $customerCount = User::where([['area_id', '=', $areaId], ['user_type', '=', 2]])->get()->count();
        $_income = $areaData->bookings->sum('commission_amount') . " SAR";

        return response()->json(['data' => $areaData, 'providers' => $providerCount, 'customers' => $customerCount, 'income' => $_income]);
    }

}
