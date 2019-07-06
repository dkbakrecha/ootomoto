<?php

namespace App\Http\Controllers\Api;

use URL;
use Auth;
use App\ShopOffer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OffersController extends Controller {

    public function index(Request $request) {
        $currentUserID = Auth::guard('api')->id();
        if (empty($currentUserID)) {
            $currentUserID = 0;
        }
        
        If (!empty($request->input('page'))) {
            $page = $request->input('page');
        } else {
            $page = "1";
        }
        $perpage = "10";

        $offset = ($page - 1) * $perpage;

        $offerData = ShopOffer::where('status', '=', 1)
                        ->with(['shop'])
                        ->whereHas('shop', function($query) {
                            $query->where('status', '=', 1);
                        })
                        ->skip($offset)
                        ->take($perpage)
                        ->orderBy('id', 'DESC')
                        ->get()->toArray();

        if (!empty($offerData)) {
            //prd($reviewData);
            $resMessages = array();

            foreach ($offerData as $offer) {
                $messages['id'] = $offer['id'];
                $messages['title'] = $offer['title'];
                $messages['description'] = $offer['description'];

                if (!empty($offer['offer_image'])) {
                    $messages['offer_image'] = URL::to('/') . '/images/offers/' . $offer['offer_image'];
                } else {
                    $messages['offer_image'] = null;
                }
                $messages['expire_date'] = $offer['expire_date'];
                $messages['price'] = $offer['price'];
                $messages['shop_id'] = $offer['shop_id'];
                $messages['shop_name'] = $offer['shop']['name'];
                $resMessages[] = $messages;
            }

            return response()->json([
                        "success" => true,
                        "data" => $resMessages,
                            ], 200);
        } else {
            return response()->json([
                        "success" => true,
                        "data" => [],
                        "message" => $this->message("offer_empty", $currentUserID),
                            ], 206);
        }
    }

}
