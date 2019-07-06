<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Vehicle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;


class VehicleController extends Controller
{
    public function index(Request $request) {
        $customer_id = Auth::guard('api')->id();

        $_vehicles = Vehicle::all();
        
        return response()->json([
                    "success" => true,
                    'data' => $_vehicles->toArray(),
                        ], 200);
    }

    public function store(Request $request) {
        $currentUserID = Auth::guard('api')->id();

        $validator = Validator::make($request->all(), [
                    'reg_no' => 'required',
                    'mfg_year' => 'required',
                    'maker_id' => 'required',
                    'model_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }

        $vehicle = new Vehicle();
        $vehicle->reg_no = $request->get('reg_no');
        $vehicle->mfg_year = $request->get('mfg_year');
        $vehicle->macker_id = $request->get('maker_id');
        $vehicle->model_id = $request->get('model_id');
        $vehicle->vehicle_class = 1;
        $vehicle->user_id = $currentUserID;
       
        $vehicle->save();

        return response()->json([
                    "success" => true,
                    'message' => "Vehicle added successfully.",
                        ], 200);
    }
}
