<?php

namespace App\Http\Controllers;

use Auth;
use App\ShopOffer;
use Illuminate\Http\Request;

class OffersController extends Controller {

    public function __construct() {
        $this->middleware('auth:web');
    }

    public function index() {
        $offers = ShopOffer::latest('shop_offers.created_at')
                ->where('shop_id', '=', $this->_shop_id())
                ->where(function ($query) {
                    $query->where('status', '=', 1) //Active
                    ->orWhere('status', '=', 3) //Pending
                    ->orWhere('status', '=', 2) //Inactive
                    ->orWhere('status', '=', 0); //Rejected
                })
                ->paginate(20);
        //prd($services);
        return view('offers.index', compact('offers'))
                        ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function store(Request $request) {
        $validator = \Validator::make($request->all(), [
                    'title' => 'required|max:50',
                    'description' => 'required',
                    'services' => 'required',
                    'price' => 'required|numeric',
                    'days' => 'required|numeric|between:1,365',
                        ], [
                    'offer_image.mimes' => __('messages.image_format_validate'),
                    'offer_image.max' => __('messages.image_size_validate'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        $shopOffer = new ShopOffer();
        $shopOffer->unique_id = $this->unique_key("OF", "shop_offers");
        $shopOffer->shop_id = $this->_shop_id();
        $shopOffer->title = $request->get('title');
        $shopOffer->description = $request->get('description');
        $shopOffer->services = implode(",", $request->get('services'));
        $shopOffer->price = $request->get('price');
        $shopOffer->days = $request->get('days');
        $shopOffer->status = 3; // Pending
        $shopOffer->save();

            // Send Offer create Web Notification to Admin User
            $shop_id = $this->_shop_id();
            $admin_user_id = $this->_admin_id();

            // SAVE BOOKING AS WEB NOTIFICATION FOR SERVICE PROVIDER AND THEIR SUPERVISORS
            // Get Shop user details
            $shop = \App\User::find($shop_id);

            // Save notification for Admin USer
            \App\WebNotification::create([
                'notification_for' => $admin_user_id,
                'user_id' => $shop_id,
                'event_type' => 4,  // Offer created by shop
                'event' => 'Offer created by '. $shop->name,
            ]);

        if ($request->hasFile('offer_image')) {
            $file = $request->offer_image;
            $_filename = "offer_" . $shopOffer->id . ".png";
            $destinationPath = public_path() . '/images/offer/';
            $file->move($destinationPath, $_filename);

            $shopOffer->offer_image = $_filename;
            $shopOffer->save();
        }

        return response()->json(['success' => __('messages.offer_success_create')]);
    }

    public function update(Request $request) {
        $validator = \Validator::make($request->all(), [
                    'title' => 'required|max:50',
                    'description' => 'required',
                    'services' => 'required',
                    'price' => 'required|numeric',
                    'days' => 'required|numeric|between:1,365',
                        ], [
                    'offer_image.mimes' => __('messages.image_format_validate'),
                    'offer_image.max' => __('messages.image_size_validate'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        $shopOffer = ShopOffer::findOrFail($request->id);

        if ($shopOffer->status != 3) {
            return response()->json(['errors' => [__('messages.offer_edit_validate')]]);
        }

        $shopOffer->title = $request->get('title');
        $shopOffer->description = $request->get('description');
        $shopOffer->services = implode(",", $request->get('services'));

        if ($request->hasFile('offer_image')) {
            $file = $request->offer_image;
            $_filename = "offer_" . $request->id . ".png";
            $destinationPath = public_path() . '/images/offer/';
            $file->move($destinationPath, $_filename);

            $shopOffer->offer_image = $_filename;
        }

        $shopOffer->price = $request->get('price');
        $shopOffer->days = $request->get('days');
        $shopOffer->save();

        return response()->json(['success' => __('messages.offer_success_update')]);
    }

    public function getOffer(Request $request) {
        $offerId = $request->id;
        $offerData = ShopOffer::findOrFail($offerId);

        $services = \App\Service::whereIn('id', explode(',', $offerData->services))
                        ->select('name')
                        ->get()->toArray();

        $offerData->services = explode(',', $offerData->services);



        return response()->json(['data' => $offerData, 'services' => $services]);
    }

    public function getOfferPrice(Request $request) {
        $service_Ids = $request->ids;

        $servicesData = \App\ShopService::WhereIn('service_id', $service_Ids)
                        ->where('shop_id', '=', $this->_shop_id())->sum('price');

        return $servicesData;
    }

    public function offer_delete(Request $request) {
        $shopOffer = ShopOffer::findOrFail($request->offer_id);

        if ($shopOffer->status != 3) {
            return redirect('offers')->with('error', __('messages.offer_delete_validate'));
        }

        ShopOffer::find($request->offer_id)->delete();
        return redirect('offers')->with('success', __('messages.offer_success_delete'));
    }

}
