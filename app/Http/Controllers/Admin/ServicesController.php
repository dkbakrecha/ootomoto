<?php

namespace App\Http\Controllers\Admin;

use App\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServicesController extends Controller {

    public function __construct() {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $services = Service::latest('services.created_at')
                ->get();
        
        return view('admin.services.index', compact('services'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = \Validator::make($request->all(), [
                    'name' => 'required',
                    'description' => 'required',
                    'duration' => 'required|integer|between:1,9999',
                    'price' => 'required|integer|between:1,999999',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }


        $service = new Service();
        $service->name = $request->get('name');
        $service->unique_id = $this->unique_key("SR", "services");
        $service->description = $request->get('description');
        $service->duration = $request->get('duration');
        $service->price = $request->get('price');

        $service->save();

        return response()->json(['success' => __('messages.service_add_success')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request) {
        $validator = \Validator::make($request->all(), [
                    'name' => 'required',
                    'category_id' => 'required',
                    'duration' => 'required|integer|between:1,9999',
                    'price' => 'required|integer|between:1,999999',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        $service = Service::findOrFail($request->id);
        $service->name = $request->get('name');
        $service->category_id = $request->get('category_id');
        $service->duration = $request->get('duration');
        $service->price = $request->get('price');
        $service->save();

        return response()->json(['success' => __('messages.service_update_success')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service) {
        Service::find($service->id)->delete();

        return redirect('admin/services')->with('success', __('messages.service_delete_success'));
    }

    public function getService(Request $request) {
        $serviceID = $request->id;
        $serviceData = Service::findOrFail($serviceID);
        return response()->json(['data' => $serviceData]);
    }

}
