<?php

namespace App\Http\Controllers;

use Auth;
use App\ShopService;
use App\Service;
use Illuminate\Http\Request;

class ServicesController extends Controller {

    public function __construct() {
        $this->middleware('auth:web');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $servicesObj = ShopService::latest()
                ->with(['category'])
                ->where('shop_id', '=', $this->_shop_id());

        $services = $servicesObj->get();
        $serviceSelected = $servicesObj->pluck('service_id', 'service_id')->toArray();
        $serviceMaster = Service::OrderBy('name', 'ASC')
                ->whereNotIn('id', $serviceSelected)
                ->pluck('name', 'id');
        //prd($services->toArray());
        return view('services.index', compact('services', 'serviceMaster'));
    }

    public function store(Request $request) {
        $shop_id = $this->_shop_id();

        $validator = \Validator::make($request->all(), [
                    'duration' => 'required|integer|between:1,9999',
                    'price' => 'required|integer|between:1,999999',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }


        $_shopService = new ShopService();
        $_shopService->shop_id = $shop_id;
        $_shopService->service_id = $request->service_id;
        $_shopService->unique_id = $request->unique_id;
        $_shopService->category_id = $request->category_id;
        $_shopService->name = $request->name;
        $_shopService->duration = $request->duration;
        $_shopService->price = $request->price;
        $_shopService->save();

        return response()->json(['success' => __('messages.service_successfully_added')]);
    }

    public function update(Request $request) {
        $validator = \Validator::make($request->all(), [
                    'duration' => 'required|integer|between:1,9999',
                    'price' => 'required|integer|between:1,999999',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        $shopService = ShopService::findOrFail($request->id);

        $shopService->price = $request->price;
        $shopService->duration = $request->duration;
        $shopService->save();

        return response()->json(['success' => __('messages.service_successfully_updated')]);
    }

    public function service_delete(Request $request) {
        ShopService::findOrFail($request->id)->delete();

        return redirect('services')->with('success', __('messages.service_successfully_delete'));
    }

    public function getService(Request $request) {
        $serviceId = $request->id;

        $serviceData = Service::findOrFail($serviceId);
        return response()->json(['data' => $serviceData]);
    }

    public function getShopService(Request $request) {
        $shopServiceId = $request->id;

        $serviceData = ShopService::findOrFail($shopServiceId);
        return response()->json(['data' => $serviceData]);
    }

}
