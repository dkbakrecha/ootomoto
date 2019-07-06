<?php

namespace App\Http\Controllers\Api;

use DB;
use URL;
use Auth;
use App\User;
use App\Category;
use App\ShopReview;
use App\ShopService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller {

    public function change_password(Request $request) {
        $currentUserID = Auth::guard('api')->id();

        $data = $request->all();
        Validator::extend('current_password', function ($attribute, $value, $parameters, $validator) {
            $userData = User::find($parameters[0]);

            return $userData && Hash::check($value, $userData->password);
        });

        $validator = Validator::make($data, [
                    'api_token' => 'required',
                    'current_password' => 'required|current_password:' . $currentUserID,
                    'password' => 'required|confirmed|between:8,12',
                    'password_confirmation' => 'required',
                        ], [
                    'current_password' => 'Current password is not match.'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
            //{"success":false,"message":"The old password field is required."}
        }

        $user = User::findOrFail($currentUserID);
        $user->password = Hash::make($request->get('password'));
        $user->save();

        return response()->json([
                    "success" => true,
                    'message' => $this->message("password_updated", $currentUserID),
                        ], 200);
    }

    public function shop_profile(Request $request) {

        $currentUserID = Auth::guard('api')->id();
        if (empty($currentUserID)) {
            $currentUserID = 0;
        }

        $shop_id = $request->get('shop_id');
        /* Shop Information */


        $user = User::where('users.id', '=', $shop_id)
                ->select('users.id', 'users.unique_id', 'users.name', 'users.address', 'users.phone', 'users.lat', 'users.long', 'users.accept_payment', 'users.status', 'users.map')
                ->addSelect(DB::raw('IFNULL((SELECT shop_favorites.id FROM shop_favorites'
                                . ' WHERE users.id = shop_favorites.provider_id AND shop_favorites.user_id = ' . $currentUserID . '), "0") as is_favorite'))
                ->with(['workingHours', 'shopImages'])
                ->first();

        if (empty($user)) {
            return response()->json([
                        "success" => true,
                        "data" => [],
                        "message" => $this->message("shop_empty", $currentUserID),
                            ], 206);
        }

        $retArray = $user->toArray();

        $i = 0;
        foreach ($retArray['shop_images'] as $_image) {

            $retArray['shop_images'][$i]['filename'] = URL::to('/') . '/images/shop/' . $_image['filename'];
            $i++;
        }


        $retArray['average_rating'] = ShopReview::where('shop_id', '=', $shop_id)
                ->where('status', '=', 1)
                ->avg('rating');

        $retArray['total_reviews'] = ShopReview::where('shop_id', '=', $shop_id)
                        ->where('status', '=', 1)
                        ->whereNotNull('review_text')->count();

        /* Shop Services data */
        $_shop_services = array();
        $services = ShopService::where('shop_id', '=', $shop_id)
                        ->get()->toArray();


        foreach ($services as $service) {
            //pr($service['category_id']);
            $_category = Category::where('id', '=', $service['category_id'])
                            ->select('id', 'unique_id', 'name')
                            ->get()->toArray();

            $_barbers = \App\BarberService::where('shop_id', '=', $shop_id)
                            ->with(['barber'])
                            ->select('id', 'shop_id', 'barber_id', 'category_id')
                            ->where('category_id', '=', $service['category_id'])
                            ->groupBy('barber_id')
                            ->get()->toArray();

            $i = 0;
            foreach ($_barbers as $_barber) {
                if (!empty($_barber['barber']['profile_image'])) {
                    $_barbers[$i]['barber']['profile_image'] = URL::to('/') . '/images/profile/' . $_barber['barber']['profile_image'];
                }
                $i++;
            }

            $_shop_services[$service['category_id']]['category'] = $_category[0];
            $_shop_services[$service['category_id']]['services'][] = $service;
            $_shop_services[$service['category_id']]['berbers'] = $_barbers;
        }

        $_s_services = array();

        foreach ($_shop_services as $_skey => $_sval) {
            $_s_services[] = $_sval;
        }

        $retArray['category_list'] = $_s_services;
//prd($retArray);
        return response()->json([
                    "success" => true,
                    'data' => $retArray,
                        ], 200);
    }

}
