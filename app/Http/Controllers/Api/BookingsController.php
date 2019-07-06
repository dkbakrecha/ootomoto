<?php

namespace App\Http\Controllers\Api;

use DB;
use URL;
use Auth;
use App\Booking;
use App\BookingService;
use App\ShopService;
use App\CouponCode;
use App\ShopWorkingHour;
use Illuminate\Http\Request;
use App\ShopOffer;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\FlaggedAccount;

class BookingsController extends Controller {

    public function store(Request $request) {
        $customer_id = Auth::guard('api')->id();

        $validator = Validator::make($request->all(), [
                    'shop_id' => 'required',
                    'services' => 'required',
                    'booking_date' => 'required',
                    'booking_time' => 'required',
                    'tz' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }

        /* Find previous inactiveBooking And delete it */
        $inactiveBooking = Booking::Where('customer_id', '=', $customer_id)
                        ->where('status', '=', 0)->first();

        if (!empty($inactiveBooking->id)) {
            BookingService::Where('booking_id', '=', $inactiveBooking->id)->delete();
            Booking::Where('customer_id', '=', $customer_id)
                    ->where('status', '=', 0)->delete();
        }
        /* End Find previous booking */

        $services = explode(',', $request->services);
        $barbers = explode(',', $request->barbers);

        /* Find Shop services */
        $_ssObj = ShopService::WhereIn('service_id', $services)
                ->select('id', 'service_id', 'name', 'price', 'duration')
                ->where('shop_id', '=', $request->shop_id);

        $shopServices = $_ssObj->get()->toArray();
        $servicesDuration = $_ssObj->sum('duration');
        $servicesPrice = $_ssObj->sum('price');
        /* End Find Shop services */

        $_start_time = date("Y-m-d H:i:s", strtotime($request->booking_date . " " . $request->booking_time));
        $_end_time = date("Y-m-d H:i:s", strtotime($_start_time . "+" . $servicesDuration . " minutes"));

        /* Check to prevent generating previous booking. */
        if (Carbon::parse($_start_time, $request->tz)->lt(Carbon::now($request->tz))) {
            return $this->sendError($this->message("past_booking_time", $customer_id), null, 400);
        }

        /* Find Shop timing - is shop open for booking time */
        $_bookingDay = Carbon::parse($_start_time, $request->tz);
        $_booking_endtime = date("H:i", strtotime($_end_time));

        $shopOpen = ShopWorkingHour::Where('shop_weekday', '=', $_bookingDay->format('D'))
                ->where('shop_id', '=', $request->shop_id)
                ->where('is_open', '=', 1)
                ->where('shop_starttime', '<=', date("H:i", strtotime($request->booking_time)))
                ->where('shop_closetime', '>=', date("H:i", strtotime($_end_time)))
                ->first();

        //prd($shopOpen);
        if (empty($shopOpen)) {
            return $this->sendError($this->message("invalid_booking_time", $customer_id), null, 400);
        }

        /* Find Offers - If any offer apply on booking */
        $_shop_offers = ShopOffer::Where('shop_id', '=', $request->shop_id)
                ->where('status', '=', 1)
                ->get();
        $_offerValue = 0;
        //pr($services);
        foreach ($_shop_offers as $_offer) {
            $_off_services = explode(',', $_offer->services);
            $_res = array_diff($_off_services, $services);
            //pr($_res);
            if (empty($_res)) {
                //All Services match with offer's services
                $offerApplied = $_offer;
            } else {
                /* if (in_array($_res[0], $services)) {

                  } */
            }
        }
        //pr($_shop_offers->toArray());
        //prd("asd");
        if (!empty($offerApplied)) {
            //pr($offerApplied->services);
            $servicesOfferSum = \App\ShopService::WhereIn('service_id', explode(',', $offerApplied->services))
                            ->where('shop_id', '=', $request->shop_id)->sum('price');
            //pr($servicesOfferSum);
            $_offerValue = $servicesOfferSum - $offerApplied->price;
        }

        /* Find Booking timing - is Another barber is conflit */
        $_i = 0;
        $serviceEmpty = 0;
        $_serviceStartTime = $_start_time;
        foreach ($shopServices as $_service) {
            $_serviceEndTime = date("Y-m-d H:i:s", strtotime($_serviceStartTime . "+" . $_service['duration'] . " minutes"));

            $barberServices = \App\BarberService::where("shop_id", "=", $request->shop_id)
                            ->where('service_id', '=', $_service['service_id'])
                            ->pluck('barber_id')->toArray();


            //pr($barberServices);

            $_freeBarbers = array();
            if (!empty($barberServices)) {
                foreach ($barberServices as $barber) {
                    if ($this->_chkBarberSchedule($barber, $_serviceStartTime, $_serviceEndTime) == 1) {
                        $_freeBarbers[] = $barber;
                    }
                }
            }

            if (empty($_freeBarbers)) {
                $serviceEmpty = 1;
            }

            $shopServices[$_i]['barbers'] = $_freeBarbers;
            $shopServices[$_i]['service_starttime'] = $_serviceStartTime;
            $shopServices[$_i]['service_endtime'] = $_serviceEndTime;

            $_serviceStartTime = date("Y-m-d H:i:s", strtotime($_serviceEndTime . "1 minutes"));
            $_i++;
        }
//prd($shopServices);
        if ($serviceEmpty == 1) {
            return $this->sendError($this->message("nobarber_to_service", $customer_id), null, 400);
        }

        //prd($offerApplied);
        $_finalAmount = (!empty($_offerValue)) ? $servicesPrice - $_offerValue : $servicesPrice;

        //$shop = User::where('id', '=', $booking->shop_id)->first();
        //Save Booking Here
        $booking = new Booking();
        $booking->customer_id = $customer_id;
        $booking->shop_id = $request->shop_id;
        $booking->booking_date = $_start_time;
        $booking->booking_starttime = date("Y-m-d H:i:s", strtotime($_start_time));
        $booking->booking_endtime = date("Y-m-d H:i:s", strtotime($_end_time));
        $booking->sub_total = (String) $servicesPrice;
        $booking->vat_amount = "0";
        $booking->offer_amount = (String) $_offerValue;
        $booking->final_amount = (String) $_finalAmount;
        $booking->updated_at = Carbon::now($request->tz);
        $booking->created_at = Carbon::now($request->tz);

        $booking->status = 0; //0 =Inactfileive for system
        //prd($booking);

        $booking->save();

        //Save Booking Services
        $_serviceStartTime = $_start_time;
        foreach ($shopServices as $_services) {
            $_serviceEndTime = date("Y-m-d H:i:s", strtotime($_serviceStartTime . "+" . $_services['duration'] . " minutes"));

            $bookingService = new BookingService();
            $bookingService->booking_id = $booking->id;
            $bookingService->shop_id = $request->shop_id;
            $bookingService->service_id = $_services['service_id'];
            $bookingService->barber_id = $_services['barbers'][0];
            $bookingService->starttime = $_serviceStartTime;
            $bookingService->endtime = $_serviceEndTime;
            $bookingService->price = $_services['price'];
            $bookingService->status = 2; // Inactive
            $bookingService->save();

            $_serviceStartTime = date("Y-m-d H:i:s", strtotime($_serviceEndTime . "1 minutes"));
        }

        $booking->services = $shopServices;
        unset($booking->created_at);
        //unset($booking->updated_at);

        return response()->json([
                    "success" => true,
                    'data' => $booking->toArray(),
                    'message' => $this->message("booking_success", $customer_id),
                        ], 200);
    }

    public function _chkBarberSchedule_old($barber_id, $start_time, $endtime) {
        pr($barber_id);
        pr($start_time);
        pr($endtime);
        DB::enableQueryLog();
        $barberScheduled = BookingService::where('barber_id', '=', $barber_id)
                ->where('status', '!=', 2) //2 When booking marked as calcelled or rejected or inactive
                ->whereDate('starttime', '=', date('Y-m-d', strtotime($start_time)))
                ->where(function ($query) use($start_time) {
                    $query->where('starttime', '<=', $start_time)
                    ->where('endtime', '>=', $start_time);
                })->orWhere(function ($query) use ($endtime) {
                    $query->where('starttime', '<=', $endtime)
                    ->where('endtime', '>=', $endtime);
                })
                ->first();
        pr(DB::getQueryLog());
        pr($barberScheduled);
        return (empty($barberScheduled)) ? 1 : 0;
    }

    public function _chkBarberSchedule($barber_id, $start_time, $endtime) {
        //pr($barber_id);
        //pr($start_time);
        //pr($endtime);
        //DB::enableQueryLog();
        $barberScheduled = BookingService::where('barber_id', '=', $barber_id)
                ->where('status', '!=', 2) //2 When booking marked as calcelled or rejected or inactive
                ->whereDate('starttime', '=', date('Y-m-d', strtotime($start_time)))
                ->where(function ($query) use($start_time, $endtime) {
                    $query->where(function ($query) use($start_time) {
                        $query->where('starttime', '<=', $start_time)
                        ->where('endtime', '>=', $start_time);
                    })->orWhere(function ($query) use ($endtime) {
                        $query->where('starttime', '<=', $endtime)
                        ->where('endtime', '>=', $endtime);
                    });
                })
                ->first();
        //pr(DB::getQueryLog());
        //pr($barberScheduled);
        return (empty($barberScheduled)) ? 1 : 0;
    }

    public function checkout(Request $request) {
        $customer_id = Auth::guard('api')->id();

        $validator = Validator::make($request->all(), [
                    'booking_id' => 'required',
                    'payment_method' => 'required',
                    'tz' => 'required',
        ]);

        if (!empty($request->coupon_code)) {
            $couponData = CouponCode::Where('coupon_code', '=', $request->coupon_code)
                    ->where('status', '=', 1)
                    ->first();

            if (empty($couponData)) {
                return $this->sendError($this->message("coupon_invalid", $customer_id), null, 400);
            } else {
                //Check coupon already use by User or Not
                $chkUsedCoupon = Booking::where('customer_id', '=', $customer_id)
                        ->where('status', '!=', 0)
                        ->where('promocode', '=', $request->coupon_code)
                        ->first();

                if (!empty($chkUsedCoupon)) {
                    return $this->sendError($this->message("coupon_already_used", $customer_id), null, 400);
                }
            }
        }

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }

        $bookingInfo = Booking::findOrFail($request->booking_id);
        
        $_start_time = $bookingInfo->booking_date;
        /* Check to prevent generating previous booking. */
        if (Carbon::parse($_start_time, $request->tz)->lt(Carbon::now($request->tz))) {
            return $this->sendError($this->message("past_booking_time", $customer_id), null, 400);
        }
        
        $bookingInfo->unique_id = $this->unique_key("B", "bookings");
        $bookingInfo->payment_method = $request->payment_method;

        if (!empty($couponData)) {
            $_couponCode = $couponData->coupon_code;
            $_couponType = $couponData->coupon_type;
            $_couponVal = $couponData->coupon_amount;


            $bookingInfo->promocode = $_couponCode; //Coupon Code Apply

            if ($_couponType == 1) {
                //Coupon %amount Apply
                $_couponVal = (($_couponVal * $bookingInfo->final_amount) / 100);
                $bookingInfo->promo_amount = $_couponVal; //Coupon Code Apply
            } else {
                //Coupon fixed amount Apply
                $bookingInfo->promo_amount = $_couponVal; //Coupon Code Apply
            }

            $bookingInfo->final_amount = $bookingInfo->final_amount - $bookingInfo->promo_amount;
            $bookingInfo->final_amount = ($bookingInfo->final_amount > 0) ? $bookingInfo->final_amount : 0;
        }


        /* Check Shop for auto approve */
        $shopInfo = User::where('id', '=', $bookingInfo->shop_id)
                ->first();

        $commission = $shopInfo->commission;
        if (!empty($commission)) {
            if ($shopInfo->commission_type == 1) {
                //Fixed Commission
                $bookingInfo->commission_type = 1;
                $bookingInfo->commission_amount = $commission;
            } else {
                //Persentage Commission
                $_comm_amo = (($commission / 100) * $bookingInfo->final_amount);
                $bookingInfo->commission_amount = $_comm_amo;
            }
        }

        //If Payment Method  == 1 Cash
        if ($request->payment_method == 1) {
            if ($shopInfo->auto_approve == 0) {
                $bookingInfo->status = 2; //Booking in Confirmed Mode
            } else {
                $bookingInfo->status = 3; //Booking in Pending Mode
            }
        }

        $bookingInfo->updated_at = Carbon::now($request->tz);
        $bookingInfo->checkout_time = Carbon::now($request->tz);

        $shop = User::where('id', '=', $bookingInfo->shop_id)->first();
        if (!empty($shop->area_id)) {
            $bookingInfo->area_id = $shop->area_id;
        }

        $bookingInfo->save();

        /** update Booking services status to 0 when booking is accept */
        $bServices = \App\BookingService::where('booking_id', '=', $request->booking_id)
                ->get();

        foreach ($bServices as $bService) {
            $bsData = \App\BookingService::where('id', '=', $bService->id)->first();

            $bsData->status = 0;
            $bsData->save();
        }
        /* Updateing Booking Services to accept */

        // Send Web notification
        if ($bookingInfo->status == 3) {
            $shop_id = $bookingInfo->shop_id;

            // SAVE BOOKING AS WEB NOTIFICATION FOR SERVICE PROVIDER AND THEIR SUPERVISORS
            // Get customer user details
            $user = \App\User::find($customer_id);

            // Save notification for Service Provider
            \App\WebNotification::create([
                'notification_for' => $shop_id,
                'user_id' => $customer_id,
                'event_type' => 1, // Booking created for the shop
                'event' => 'New booking generated by ' . $user->name,
            ]);

            // Save web notification for all the supervisors under this shop
            // Find all supervisors under this shop
            $supervisors = \App\User::where([
                        ['shop_id', '=', $shop_id],
                        ['isAdmin', '=', 1],
                        ['user_type', '=', 1],
                    ])
                    ->get()
                    ->toArray();

            foreach ($supervisors as $supervisor) {
                // Save notification for Service Provider
                \App\WebNotification::create([
                    'notification_for' => $supervisor['id'],
                    'user_id' => $customer_id,
                    'event_type' => 1, // Booking created for the shop
                    'event' => 'New booking generated by ' . $user->name,
                ]);
            }
        }

        //Auto approve notification when booking confirm
        if ($bookingInfo->status == 2) {
            $user = \App\User::find($customer_id);

            if ($user->confirmation_alert == 1) {
                // Send Push notification to customer
                $device = User::find($bookingInfo->customer_id)
                        ->mobileSessions()
                        ->where('status', '=', 1)
                        ->first();

                // Create push notification content
                //$body = 'Your booking has been approved';
                $body = $this->message("booking_approve_notifiaction", $bookingInfo->customer_id);

                switch ($device['device_type']) {
                    case 'android':
                        // Third Parameter 1 depicts it as a reminder message
                        $this->sendAndroidNotification($device['device_token'], $body, 2, $bookingInfo->id);
                        break;

                    case 'ios':
                        // Third Parameter 1 depicts it as a reminder message
                        $this->sendIosNotification($device['device_token'], $body, 2, $bookingInfo->id);
                        break;

                    default:
                        break;
                }
            }
        }

        $retData = array();
        if (!empty($couponData)) {
            $retData['success'] = true;
            $retData['message'] = $this->message("booking_placed_success_with_coupon", $customer_id);
            $retData['data'] = ['final_amount' => (String) $bookingInfo->final_amount, 'unique_id' => (String) $bookingInfo->unique_id];
        } else {
            $retData['success'] = true;
            $retData['message'] = $this->message("booking_placed_success", $customer_id);
            $retData['data'] = ['unique_id' => (String) $bookingInfo->unique_id];
        }

        return response()->json($retData, 200);
    }

    public function booking_chk(Request $request) {
        $customer_id = Auth::guard('api')->id();

        $validator = Validator::make($request->all(), [
                    'booking_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }

        $bookingInfo = Booking::Where('id', '=', $request->booking_id)
                        ->with(['bookingservices'])->first();

        /** check if booking in pending or approved */
        if (!in_array($bookingInfo->status, [2, 3])) {
            return $this->sendError($this->message("invalid_reschedule_request", $customer_id), null, 400);
        }

        $msg = $this->message("valid_reschedule_request", $customer_id);


        return response()->json([
                    "success" => true,
                    'message' => $msg,
                        ], 200);
    }

    public function booking_token(Request $request) {
        $customer_id = Auth::guard('api')->id();
        $validator = Validator::make($request->all(), [
                    'booking_id' => 'required',
                    'telr_token' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }

        $bookingInfo = Booking::Where('id', '=', $request->booking_id)->first();
        $bookingInfo->telr_token = $request->telr_token;
        /* Check Shop for auto approve */
        $shopInfo = User::where('id', '=', $bookingInfo->shop_id)
                ->first();

        //If Payment Method  == 2 Card
        if ($shopInfo->auto_approve == 0) {
            $bookingInfo->status = 2; //Booking in Confirmed Mode
        } else {
            $bookingInfo->status = 3; //Booking in Pending Mode
        }

        $bookingInfo->save();

        //Payment Detuct if Booking confirm
        if ($bookingInfo->status == 2) {
            $bookingDescription = "Payment for amount " . $bookingInfo->final_amount . " for booking " . $bookingInfo->unique_id;
            $telrTransaction = $this->telr_pay($bookingInfo->unique_id, $bookingDescription, $bookingInfo->final_amount, $request->telr_token);

            if ($telrTransaction == false) {
                //Cancel (Reject 5) Booking in case of payment not capture..
                $bookingInfo->status = 5; //Booking in cancellation
                $bookingInfo->cancel_reason = "Booking Rejected by Service Provider due to not captureing payment";
                $bookingInfo->cancel_date = Carbon::now($request->tz);
                $bookingInfo->telr_token = "";
                $bookingInfo->save();
            } else {
                // If Payment capture by telr then Update telr token in Booking and make a receipt
                $bookingInfo->telr_token = $telrTransaction;
                $bookingInfo->save();
                $this->create_receipt($bookingInfo, $telrTransaction);
            }


            //Auto approve notification when booking confirm
        
            $user = \App\User::find($customer_id);

            if ($user->confirmation_alert == 1) {
                // Send Push notification to customer
                $device = User::find($bookingInfo->customer_id)
                        ->mobileSessions()
                        ->where('status', '=', 1)
                        ->first();

                // Create push notification content
                //$body = 'Your booking has been approved';
                $body = $this->message("booking_approve_notifiaction", $bookingInfo->customer_id);

                switch ($device['device_type']) {
                    case 'android':
                        // Third Parameter 1 depicts it as a reminder message
                        $this->sendAndroidNotification($device['device_token'], $body, 2, $bookingInfo->id);
                        break;

                    case 'ios':
                        // Third Parameter 1 depicts it as a reminder message
                        $this->sendIosNotification($device['device_token'], $body, 2, $bookingInfo->id);
                        break;

                    default:
                        break;
                }
            }
        }

        return response()->json([
                    "success" => true,
                        ], 200);
    }

    public function reschedule(Request $request) {
        $customer_id = Auth::guard('api')->id();
        $validator = Validator::make($request->all(), [
                    'booking_id' => 'required',
                    'booking_date' => 'required',
                    'booking_time' => 'required',
                    'tz' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }

        $bookingInfo = Booking::Where('id', '=', $request->booking_id)
                        ->with(['bookingservices'])->first();
//prd($bookingInfo);
        /** check if booking in pending or approved */
        if ($bookingInfo->is_reschedule >= 2) {
            return $this->sendError($this->message("invalid_reschedule_request", $customer_id), null, 400);
        }

        /** check if booking in pending or approved */
        if (!in_array($bookingInfo->status, [2, 3])) {
            return $this->sendError($this->message("invalid_reschedule_request", $customer_id), null, 400);
        }

        $_shopId = $bookingInfo->shop_id;

        $bookingServices = BookingService::Where('booking_id', '=', $request->booking_id)
                ->select('booking_services.*', 'shop_services.unique_id', 'shop_services.name', 'shop_services.category_id', 'shop_services.duration')
                ->with(['barber'])
                ->leftJoin('shop_services', function($query) use ($_shopId) {
                    $query->on('shop_services.service_id', '=', 'booking_services.service_id')
                    ->where('shop_services.shop_id', '=', $_shopId);
                })
                ->get();
//prd($bookingServices);
        $serviceArr = array();
        $servicesDuration = 0;
        $servicesPrice = 0;

        foreach ($bookingServices as $_service) {
            $_s = array();
            $_s['name'] = $_service->name;
            $_s['price'] = $_service->price;
            $_s['duration'] = $_service->duration;
            $_s['service_id'] = $_service->service_id;
            $servicesDuration = $servicesDuration + $_service->duration;
            $servicesPrice = $servicesPrice + $_service->price;
            $serviceArr[] = $_s;
        }

        $_start_time = date("Y-m-d H:i:s", strtotime($request->booking_date . " " . $request->booking_time));
        $_end_time = date("Y-m-d H:i:s", strtotime($_start_time . "+" . $servicesDuration . " minutes"));

        /* Check to prevent generating previous booking. */
        if (Carbon::parse($_start_time, $request->tz)->lt(Carbon::now($request->tz))) {
            return $this->sendError($this->message("past_booking_time", $customer_id), null, 400);
        }

        /* Find Shop timing - is shop open for booking time */
        $_bookingDay = Carbon::parse($_start_time, $request->tz);
        $_booking_endtime = date("H:i", strtotime($_end_time));

        $shopOpen = ShopWorkingHour::Where('shop_weekday', '=', $_bookingDay->format('D'))
                ->where('shop_id', '=', $_shopId)
                ->where('is_open', '=', 1)
                ->where('shop_starttime', '<=', $request->booking_time)
                ->where('shop_closetime', '>=', $_booking_endtime)
                ->first();

        if (empty($shopOpen)) {
            return $this->sendError($this->message("invalid_booking_time", $customer_id), null, 400);
        }

        /* Find Booking timing - is Another barber is conflit */
        $_i = 0;
        $serviceEmpty = 0;
        $_serviceStartTime = $_start_time;
        foreach ($serviceArr as $_service) {
            $_serviceEndTime = date("Y-m-d H:i:s", strtotime($_serviceStartTime . "+" . $_service['duration'] . " minutes"));

            $barberServices = \App\BarberService::where("shop_id", "=", $_shopId)
                            ->where('service_id', '=', $_service['service_id'])
                            ->pluck('barber_id')->toArray();


            //pr($barberServices);

            $_freeBarbers = array();
            if (!empty($barberServices)) {
                foreach ($barberServices as $barber) {
                    if ($this->_chkBarberSchedule($barber, $_serviceStartTime, $_serviceEndTime) == 1) {
                        $_freeBarbers[] = $barber;
                    }
                }
            }

            if (empty($_freeBarbers)) {
                $serviceEmpty = 1;
            }

            $shopServices[$_i]['barbers'] = $_freeBarbers;
            $shopServices[$_i]['service_starttime'] = $_serviceStartTime;
            $shopServices[$_i]['service_endtime'] = $_serviceEndTime;

            $_serviceStartTime = date("Y-m-d H:i:s", strtotime($_serviceEndTime . "1 minutes"));
            $_i++;
        }
//prd($shopServices);
        if ($serviceEmpty == 1) {
            return $this->sendError($this->message("nobarber_to_service", $customer_id), null, 400);
        }

        $_reschedule_amount = 0;
        //If services updated and price is higher then reschedule_amount is 
        // difference that client need to pay.
        if ($servicesPrice > $bookingInfo->sub_total) {
            $_reschedule_amount = $servicesPrice - $bookingInfo->final_amount;
        }

        $bookingInfo->booking_date = $_start_time;
        $bookingInfo->booking_starttime = date("Y-m-d H:i:s", strtotime($_start_time));
        $bookingInfo->booking_endtime = date("Y-m-d H:i:s", strtotime($_end_time));
        $bookingInfo->sub_total = (String) $servicesPrice;
        $bookingInfo->vat_amount = "0";
        $bookingInfo->offer_amount = (String) 0;
        $bookingInfo->final_amount = (String) $servicesPrice;
        $bookingInfo->updated_at = Carbon::now($request->tz);
        $bookingInfo->checkout_time = Carbon::now($request->tz);
        //$bookingInfo->status = 3; //Booking in Pending Mode
        $bookingInfo->is_reschedule = $bookingInfo->is_reschedule + 1; //Booking in Pending Mode
        $bookingInfo->reschedule_amount = (String) $_reschedule_amount; //Booking in Pending Mode

        $shopInfo = User::where('id', '=', $bookingInfo->shop_id)
                ->first();

        if ($shopInfo->auto_approve == 0) {
          $bookingInfo->status = 2; //Booking in Approved Mode
        } else {
          $bookingInfo->status = 3; //Booking in Pending Mode
        }

        //prd($bookingInfo);
        $bookingInfo->save();

        //Auto approve notification when booking confirm
        if ($bookingInfo->status == 2) {
            $user = \App\User::find($customer_id);

            if ($user->confirmation_alert == 1) {
                // Send Push notification to customer
                $device = User::find($bookingInfo->customer_id)
                        ->mobileSessions()
                        ->where('status', '=', 1)
                        ->first();

                // Create push notification content
                //$body = 'Your booking has been approved';
                $body = $this->message("booking_approve_notifiaction", $bookingInfo->customer_id);

                switch ($device['device_type']) {
                    case 'android':
                        // Third Parameter 1 depicts it as a reminder message
                        $this->sendAndroidNotification($device['device_token'], $body, 2, $bookingInfo->id);
                        break;

                    case 'ios':
                        // Third Parameter 1 depicts it as a reminder message
                        $this->sendIosNotification($device['device_token'], $body, 2, $bookingInfo->id);
                        break;

                    default:
                        break;
                }
            }
        }

        //Update Schedule Time
        $_serviceStartTime = $_start_time;

        foreach ($bookingServices as $_service) {

            $_serviceEndTime = date("Y-m-d H:i:s", strtotime($_serviceStartTime . "+" . $_service->duration . " minutes"));
            //pr($_service->id);
            $bookingService = BookingService::where('id', '=', $_service->id)->first();
            //prd($bookingService);
            //$bookingService->barber_id = $_services['barbers'][0];
            //--- To DO -- If barber need to change in reschduling
            $bookingService->starttime = $_serviceStartTime;
            $bookingService->endtime = $_serviceEndTime;
            $bookingService->save();
//prd($_serviceEndTime);
            $_serviceStartTime = date("Y-m-d H:i:s", strtotime($_serviceEndTime . "1 minutes"));
        }

        if ($bookingInfo->reschedule_amount > 0) {
            $msg = $this->message("booking_reschedule_amount_update", $customer_id, ['reschedule_amount' => $bookingInfo->reschedule_amount, 'final_amount' => $bookingInfo->final_amount]);
        } else {
            $msg = $this->message("booking_reschedule_success", $customer_id);
        }

        $resData = array(
            'final_amount' => $bookingInfo->final_amount,
            'reschedule_amount' => $bookingInfo->reschedule_amount
        );

        return response()->json([
                    "success" => true,
                    'message' => $msg,
                    'data' => $resData
                        ], 200);
    }

    public function reservations(Request $request) {
        $customer_id = Auth::guard('api')->id();
        if (!empty($request->input('page'))) {
            $page = $request->input('page');
        } else {
            $page = "1";
        }
        $perpage = "10";

        $offset = ($page - 1) * $perpage;

        $resArray = array();

        $_bookingOb = Booking::where('customer_id', '=', $customer_id)
                ->where('status', '!=', 0)
                ->select('id', 'shop_id', 'unique_id', 'booking_date', 'updated_at', 'status', 'no_show')
                ->orderBy('id', 'DESC')
                //->orderByRaw('FIELD(status,3,2,1,4,5,0)')
                ->with(['shop', 'shop.shopImages']);

        $count = $_bookingOb->count();
        $bookings = $_bookingOb->skip($offset)
                        ->take($perpage)->get();

        foreach ($bookings as $booking) {
            $_book = array();
            $_book['id'] = $booking->id;
            $_book['unique_id'] = $booking->unique_id;
            $_book['booking_date'] = $booking->booking_date;
            $_book['status'] = $booking->status;
            $_book['shop_name'] = $booking->shop->name;
            $_book['no_show'] = $booking->no_show;
            if (!empty($booking->shop->shopImages[0])) {
                $_book['shop_image'] = URL::to('/') . '/images/shop/' . $booking->shop->shopImages[0]->filename;
            }
            $resArray[] = $_book;
        }
        //prd($resArray);

        return response()->json([
                    "success" => true,
                    'total_count' => $count,
                    'data' => $resArray
                        ], 200);
    }

    public function details(Request $request) {
        $validator = Validator::make($request->all(), [
                    'booking_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }


        $bookingInfo = Booking::Where('id', '=', $request->booking_id)
                ->with(['shop'])
                ->select('booking_date', 'shop_id', 'unique_id', 'payment_method', 'offer_amount', 'final_amount', 'checkout_time', 'status', 'no_show')
                ->first();

        //$bookingInfo->checkout_time = $bookingInfo->updated_at;
        //unset($bookingInfo->updated_at);
        $_shopId = $bookingInfo->shop_id;
        $bookingInfo->offer_amount = (string) $bookingInfo->offer_amount;
        $bookingInfo->final_amount = (string) $bookingInfo->final_amount;
        /* Get Booking Service name and Barber name string */
        $bookingServices = BookingService::Where('booking_id', '=', $request->booking_id)
                ->with(['barber'])
                ->join('shop_services', function($query) use ($_shopId) {
                    $query->on('shop_services.service_id', '=', 'booking_services.service_id')
                    ->where('shop_services.shop_id', '=', $_shopId);
                })
                ->get();

        $serviceArr = array();
        foreach ($bookingServices as $_service) {
            $_s = array();
            $_s['name'] = $_service->name;
            $_s['price'] = (string) $_service->price;
            $serviceArr[] = $_s;
        }

        $bookingInfo->services = $serviceArr;

        return response()->json([
                    "success" => true,
                    'data' => $bookingInfo
                        ], 200);
    }

    public function cancel(Request $request) {
        $customer_id = Auth::guard('api')->id();

        $validator = Validator::make($request->all(), [
                    'booking_id' => 'required',
                    'tz' => 'required',
//                    'reason' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), null, 400);
        }

        $bookingInfo = Booking::where('id', '=', $request->booking_id)
                ->where(function ($query) {
                    $query->where('status', '=', 2) // Confirmed
                    ->orWhere('status', '=', 3); // Pending
                })
                ->first();

        if (empty($bookingInfo)) {
            return $this->sendError($this->message("invalid_booking_cancel_request", $customer_id), null, 400);
        }
        $transactionStatus = true;
        $_cancelPersentage = \App\SiteSetting::where('unique_key', '=', 'CANCELLATION_PERSENTAGE')->first();
        $_cancelNumber = \App\SiteSetting::where('unique_key', '=', 'CANCELLATION_NUMBER')->first();
        $cancelPersentage = (!empty($_cancelPersentage->value) && $_cancelPersentage->value > 0) ? $_cancelPersentage->value : 30; //Default value 30
        $cancelNumber = (!empty($_cancelNumber->value) && $_cancelNumber->value > 0) ? $_cancelNumber->value : 3; // Default value 3

        $_bookginRefundAmount = 0;
        /** If booking is confirmed with card payment and cancel then refund */
        if ($bookingInfo->status == 2 && $bookingInfo->payment_method == 2) {

            if (Carbon::parse($bookingInfo->booking_date, $request->tz)->gt(Carbon::now($request->tz))) {
                //If request booking date is not pass Then make full refund.
                $_bookginRefundAmount = $bookingInfo->final_amount;

                $bookingDescription = "Refund for amount " . $_bookginRefundAmount . " for booking " . $bookingInfo->unique_id;
                
                $telrTransaction = $this->telr_pay($bookingInfo->unique_id, $bookingDescription, $_bookginRefundAmount, $bookingInfo->telr_token, 'refund');
            } else {
                //If request booking date passed Then make partial refund.
                //According to term and services 70% amount refund
                $refundedCharge = (($cancelPersentage / 100) * $bookingInfo->final_amount);
                $_bookginRefundAmount = $bookingInfo->final_amount - $refundedCharge;
                
                $bookingDescription = "Refund for amount " . $_bookginRefundAmount . " for booking " . $bookingInfo->unique_id;
                
                $telrTransaction = $this->telr_pay($bookingInfo->unique_id, $bookingDescription, $_bookginRefundAmount, $bookingInfo->telr_token, 'refund');
            }

            if ($telrTransaction == false) {
                $transactionStatus = false;
            } else {
                $this->create_refund($bookingInfo, $telrTransaction, $_bookginRefundAmount, (!empty($request->reason)) ? $request->reason : "");
            }
        }

        if ($transactionStatus == true) {
            $bookingInfo->status = 4; //Booking in cancellation
            $bookingInfo->cancel_reason = (!empty($request->reason)) ? $request->reason : ""; //Booking in cancellation
            $bookingInfo->cancel_date = Carbon::now($request->tz);
            $bookingInfo->cancellation_amount = $_bookginRefundAmount;
            $bookingInfo->telr_token = "";
            $bookingInfo->save();

            /** update Booking services status to 2 when booking is cancel/reject */
            $bServices = \App\BookingService::where('booking_id', '=', $bookingInfo->id)
                    ->get();

            foreach ($bServices as $bService) {
                $bsData = \App\BookingService::where('id', '=', $bService->id)->first();

                $bsData->status = 2;
                $bsData->save();
            }
            /* Updation end */

            //Update No show status in customer table
            $customerData = User::where('id', '=', $customer_id)->first();
            if ($customerData->no_show_count >= $cancelNumber) {
                //Mail to customer as flagged/block due to cancel booking more then 3 times
                $customerData->status = 0; // O == Block/ Inactive
                $customerData->api_token = null;

                //Mail to block customer
                Mail::to($customerData->email)->send(new FlaggedAccount($customerData));
            } else {
                $customerData->no_show_count = $customerData->no_show_count + 1;
            }
            $customerData->save();
        } else {
            return $this->sendError($this->message("booking_cancel_tech_error", $customer_id), null, 400);
        }

        return response()->json([
                    "success" => true,
                    'message' => $this->message("booking_cancel_success", $customer_id),
                        ], 200);
    }

}
