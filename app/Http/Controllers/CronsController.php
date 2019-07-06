<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Edujugon\PushNotification\PushNotification;

class CronsController extends Controller {

    // A METHOD TO CHECK USER LAST LOGIN
    // 
    public function checkLastLogin() {

        $currentDateTime = Carbon::now()->toDateTimeString();

        $users = User::all();

        foreach ($users as $user) {
            // Get Date difference between today and user's last login date
            $timeNow = Carbon::createFromFormat('Y-m-d H:s:i', $currentDateTime);
            $from = Carbon::createFromFormat('Y-m-d H:s:i', $user->last_login_date);
            $diff_in_days = $timeNow->diffInDays($from);

            // CHECK IF THE last_login_date is greater than or equal to 14 days
            if ($diff_in_days > 14) {
                $device = User::find($user->id)
                        ->mobileSessions()
                        ->where('status', '=', 1)
                        ->first();

                // Create push notification content
                $body = 'You have not logged in for so long';

                switch ($device['device_type']) {
                    case 'android':
                        // Third Parameter 1 depicts it as a reminder message
                        $this->sendAndroidNotification($device['device_token'], $body, 1);
                        break;

                    case 'ios':
                        // Third Parameter 1 depicts it as a reminder message
                        $this->sendIosNotification($device['device_token'], $body, 1);
                        break;

                    default:
                        break;
                }
            }
        }

        return response()->json(['success' => true], 201);
    }

    /**
     * Reject pending bookings - IF service provider not respond to it.
     */
    public function reject_bookings_test() {
        $settingsList = \App\SiteSetting::where('unique_key', '=', 'BOOKING_APPROVEAL_TIME')->first();

        $addMin = (!empty($settingsList->value) && $settingsList->value > 0) ? $settingsList->value : 0;
        $_now = Carbon::now(env('TIME_ZONE'))->subMinutes($addMin)->format('Y-m-d H:i:s');

        pr($_now);
        $pendingBookings = \App\Booking::where('status', '=', 3)
                ->whereDate('created_at', '<', $_now)
                ->get();

        prd($pendingBookings);
        foreach ($pendingBookings as $booking) {
            $bookingData = \App\Booking::findOrFail($booking->id);
            //$bookingData->status = 5; //Rejected By System
            $bookingData->save();
        }

        return response()->json(['success' => true], 201);
    }

    public function reject_bookings() {
        $settingsList = \App\SiteSetting::where('unique_key', '=', 'BOOKING_APPROVEAL_TIME')->first();

        $addMin = (!empty($settingsList->value) && $settingsList->value > 0) ? $settingsList->value : 0;
        $_now = Carbon::now(env('TIME_ZONE'))->subMinutes($addMin)->format('Y-m-d H:i:s');


        $pendingBookings = \App\Booking::where('status', '=', 3)
                ->where('created_at', '<', $_now)
                ->get();

        //prd($pendingBookings);
        foreach ($pendingBookings as $booking) {
            $bookingData = \App\Booking::findOrFail($booking->id);
            $bookingData->status = 5; //Rejected By System
            $bookingData->save();

            $userData = User::where('id', '=', $bookingData->customer_id)
                    ->first();

            if ($userData->confirmation_alert == 1) {
                // Send Push notification to customer
                $device = User::find($bookingData->customer_id)
                        ->mobileSessions()
                        ->where('status', '=', 1)
                        ->first();

                // Create push notification content
                //$body = 'Your booking has been rejected';
                $body = $this->message("booking_rejected_notifiaction", $bookingData->customer_id);

                switch ($device['device_type']) {
                    case 'android':
                        // Third Parameter 1 depicts it as a reminder message
                        $this->sendAndroidNotification($device['device_token'], $body, 2, $bookingData->id);
                        break;

                    case 'ios':
                        // Third Parameter 1 depicts it as a reminder message
                        $this->sendIosNotification($device['device_token'], $body, 2, $bookingData->id);
                        break;

                    default:
                        break;
                }
            }
        }

        return response()->json(['success' => true], 201);
    }

    public function complete_bookings_test() {
        $_now = \Carbon::now(env('TIME_ZONE'))->format('Y-m-d H:i:s');

        $confirmedBookings = \App\Booking::where('status', '=', 2)
                ->where('no_show', '=', 0)
                ->where('booking_endtime', '<', $_now)
                ->get()->toArray();

        pr($_now);
        prd($confirmedBookings);

        foreach ($confirmedBookings as $booking) {
            $bookingData = \App\Booking::findOrFail($booking->id);
            $bookingData->status = 1; //Complete

            /** update Booking services status to 1 when booking is complete */
            $bServices = \App\BookingService::where('booking_id', '=', $bookingData->id)
                    ->get();

            foreach ($bServices as $bService) {
                $bsData = \App\BookingService::where('id', '=', $bService->id)->first();

                $bsData->status = 1;
                //    $bsData->save();
            }
            //pr($bookingData);
            //$bookingData->save();
            //pr($bookingData);
            //Create Cash Receipt
            if ($bookingData->payment_method == 1) {
                $this->create_receipt($bookingData, "CASH");
            }
        }

        return response()->json(['success' => true], 201);
    }

    /** If Booking is confirmed and not no_show. And Booking end time is over them mark booking as Complete
     * In cash booking also generate recept.
     *  */
    public function complete_bookings() {
        $_now = \Carbon::now(env('TIME_ZONE'))->format('Y-m-d H:i:s');

        $confirmedBookings = \App\Booking::where('status', '=', 2)
                ->where('no_show', '=', 0)
                ->where('booking_endtime', '<', $_now)
                ->get();

        //pr($_now);
        //prd($confirmedBookings);

        foreach ($confirmedBookings as $booking) {
            $bookingData = \App\Booking::findOrFail($booking->id);
            $bookingData->status = 1; //Complete

            /** update Booking services status to 1 when booking is complete */
            $bServices = \App\BookingService::where('booking_id', '=', $bookingData->id)
                    ->get();

            foreach ($bServices as $bService) {
                $bsData = \App\BookingService::where('id', '=', $bService->id)->first();

                $bsData->status = 1;
                $bsData->save();
            }

            $bookingData->save();
            //pr($bookingData);
            //Create Cash Receipt
            if ($bookingData->payment_method == 1) {
                $this->create_receipt($bookingData, "CASH");
            }
        }

        return response()->json(['success' => true], 201);
    }

    public function test_pay() {
        $params = array(
            'ivp_method' => 'create',
            'ivp_store' => '21386',
            'ivp_authkey' => '8tpv4^TV6C-KNQWK',
            'ivp_cart' => '654656',
            'ivp_test' => '1',
            'ivp_amount' => '200.00',
            'ivp_currency' => 'SAR',
            'ivp_desc' => 'Test Flair',
            'bill_custref' => 'CUS1003252',
            'return_auth' => 'http://flair-app.com/qa/test_res',
            'return_can' => 'http://flair-app.com/qa/test_res',
            'return_decl' => 'http://flair-app.com/qa/test_res'
        );


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://secure.telr.com/gateway/order.json");
        curl_setopt($ch, CURLOPT_POST, count($params));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        $results = curl_exec($ch);

        curl_close($ch);
        $results = json_decode($results, true);
        pr($results);
        //$ref = trim($results['order']['ref']);
        //$url = trim($results['order']['url']);
        //pr($ref);
        //pr($url);
        if (empty($ref) || empty($url)) {
# Failed to create order
        }
        prd("=====");
    }

    public function test_res(Request $request) {
        $params = array(
            'ivp_method' => 'check',
            'ivp_store' => '21386',
            'ivp_authkey' => '8tpv4^TV6C-KNQWK',
            'order_ref' => '53DC3AEB95D46730997B6C5E37C3F0271A476F4320F207BCFA614DA5C23546AE',
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://secure.telr.com/gateway/order.json");
        curl_setopt($ch, CURLOPT_POST, count($params));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        $results = curl_exec($ch);

        curl_close($ch);
        $results = json_decode($results, true);
        pr($results);
        //$ref = trim($results['order']['ref']);
        //$url = trim($results['order']['url']);
        //pr($ref);
        //pr($url);
        if (empty($ref) || empty($url)) {
# Failed to create order
        }
        prd("=====");

        prd($request->all());
    }

    /**
     * CRON functionality for Offer Expire
     */
    public function cronOfferExpire() {
        $expireOffers = \App\ShopOffer::where('expire_date', '<', Carbon::now(env('TIME_ZONE')))
                ->where('status', '!=', 4)
                ->get();
        //prd(Carbon::now());
        foreach ($expireOffers as $_offer) {
            $offerData = \App\ShopOffer::findOrFail($_offer->id);
            $offerData->status = 4;
            $offerData->save();
        }
    }

}
