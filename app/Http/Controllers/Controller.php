<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Edujugon\PushNotification\PushNotification;
use App\User;
use Auth;
use AWS;
use Illuminate\Support\Carbon;

class Controller extends BaseController {

    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests;

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404) {
        $response = [
            'success' => false,
            'message' => __($error),
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

    public function unique_key($str = "SR", $table) {
        $_tbl = \DB::table($table)->get();
        $_tblCount = $_tbl->count();
        $key = $str . date('md') . sprintf("%02d", mt_rand(01, 99)) . ($_tblCount + 1);
        return $key;
    }

    public function message($msgTxtKey, $user_id, $data = array()) {
        $userData = User::Where('id', '=', $user_id)->first();
        if(!empty($userData)){
            app()->setLocale($userData->preferred_language);    
        }else{
            app()->setLocale('en');
        }
        

        return __("apimsgs." . $msgTxtKey, $data);
        //favorite_success
    }

    public function _shop_id() {
        $_loggedInUser = Auth::guard('web')->user();
        $_shop_id = Auth::guard('web')->id();
        if ($_loggedInUser->user_type == 1) {
            $_shop_id = $_loggedInUser->shop_id;
        }

        return $_shop_id;
    }

    // Sample action to send Android push notification
    protected function sendAndroidNotification($device_token, $body, $type, $booking_id = null) {
        $push = new PushNotification('fcm');

        $push->setMessage([
                    'data' => [
                        'body' => '"' . $body . '"',
                        'type' => $type,
                        'booking_id' => $booking_id
                    ]
                ])
                ->setDevicesToken($device_token)
                ->send();

        return 'success';
    }

    // Sample action to send Ios push notification
    protected function sendIosNotification($device_token, $body, $type, $booking_id = null) {

        $push = new PushNotification('apn');

        $push->setMessage([
                    'aps' => [
                        'alert' => $body,
                        'sound' => 'default',
                        'type' => $type,
                        'booking_id' => $booking_id
                    ]
                ])
                ->setDevicesToken($device_token)
                ->send();

        return 'success';
    }

    protected function _admin_id() {
        // Fetch Admin user's user id
        $admin_user = User::where('user_type', '=', 3)
                ->first();

        return $admin_user->id;
    }

    /*
      Function to Send SMS through AWS SNS
     */

    protected function sendSMS($phone_number, $token) {
        /* $sms = AWS::createClient('sns');
        $_countryCode = env('COUNTRY_CODE');
        $message = 'Never share your OTP with anyone. Here is your OTP : ' . $token;

        return $sms->publish([
                    'Message' => $message,
                    'PhoneNumber' => $_countryCode . $phone_number,
                    'MessageAttributes' => [
                        'AWS.SNS.SMS.SMSType' => [
                            'DataType' => 'String',
                            'StringValue' => 'Transactional',
                        ]
                    ],
        ]); */
    }

    /**
     * Function to make telr payment to system
     * $type = capture, refund
     * $booking_id = Unique booking id for system
     * $bookingDescription = Description
     * $amount = Float Value
     * $transactionReferance = Telr Transaction Reference ID of initial transaction ( 12 Digit )
     */
    protected function telr_pay($booking_id, $bookingDescription, $amount, $transactionReferance, $type = "capture") {
        $params = array(
            'ivp_store' => '21386', //Client Telr Store ID
            'ivp_authkey' => '2CLk~QVhDT^LN8wK', //Client Telr Authentication Key
            'ivp_trantype' => $type, // Capture, Refund -- Followup Type
            'ivp_tranclass' => 'ecom',
            'ivp_desc' => $bookingDescription,
            'ivp_cart' => $booking_id,
            'ivp_currency' => 'SAR',
            'ivp_amount' => $amount,
            'tran_ref' => $transactionReferance, //
            'ivp_test' => '1'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://secure.telr.com/gateway/remote.html");
        curl_setopt($ch, CURLOPT_POST, count($params));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        $results = curl_exec($ch);
        curl_close($ch);

        //Logged Telr Referance
        $paymentLog = new \App\PaymentLog();
        $paymentLog->booking_id = $booking_id;
        $paymentLog->payment_type = ($type == 'refund') ? 1 : 0;
        $paymentLog->payment_log = $results;
        $paymentLog->save();

        $resArray = array();
        foreach (explode('&', $results) as $chunk) {
            $param = explode("=", $chunk);
            if ($param) {
                $resArray[urldecode($param[0])] = urldecode($param[1]);
            }
        }

        /**
         * RES ARRAY
         * Array
          (
          [auth_status] => A
          [auth_code] => 923630
          [auth_message] => Authorised
          [auth_tranref] => 040021058352
          [auth_cvv] => Y
          [auth_avs] => X
          [auth_trace] => 4001/23629/5c9b75fc
          [payment_code] => VC
          [payment_desc] => Visa Credit ending 1111
          [payment_cardl4] => 1111
          [payment_cardl6] => 411111

          )
         */
        if ($resArray['auth_status'] == "A") {
            return $resArray['auth_tranref'];
        } else {
            return false;
        }
    }

    protected function create_receipt($bookingData, $telrTransaction = "") {
        //Entry In receipt Table
        //pr($telrTransaction);
        //prd($bookingData);
        $receiptData = new \App\Receipt();
        $receiptData->unique_id = $this->unique_key("R", "receipts");
        $receiptData->booking_id = $bookingData->id;
        $receiptData->shop_id = $bookingData->shop_id;
        $receiptData->customer_id = $bookingData->customer_id;
        $receiptData->transaction_id = $telrTransaction; // Telr transaction ID
        $receiptData->receipt_date = Carbon::now(env('TIME_ZONE'));
        $receiptData->services = $this->bookingServices($bookingData->id, $bookingData->shop_id);
        $receiptData->final_amount = $bookingData->final_amount;
        $receiptData->payment_method = $bookingData->payment_method;
        $receiptData->barber_id = $bookingData->shop_id;

        $receiptData->save();
    }

    protected function create_refund($bookingData, $telrTransaction = "", $amount, $reason) {
        //Entry In receipt Table
        $refundData = new \App\Refund();
        $refundData->unique_id = $this->unique_key("R", "refunds");
        $refundData->booking_id = $bookingData->id;
        $refundData->shop_id = $bookingData->shop_id;
        $refundData->customer_id = $bookingData->customer_id;
        $refundData->transaction_id = $telrTransaction; // Telr transaction ID
        $refundData->refund_date = Carbon::now(env('TIME_ZONE'));
        $refundData->amount = $amount;
        $refundData->reason = $reason;
        $refundData->save();
    }

    protected function bookingServices($booking_id, $shop_id) {
        /* Get Booking Service name and Barber name string */

        $bookingServices = \App\BookingService::Where('booking_id', '=', $booking_id)
                ->with(['barber'])
                ->join('shop_services', function($query) use ($shop_id) {
                    $query->on('shop_services.service_id', '=', 'booking_services.service_id')
                    ->where('shop_services.shop_id', '=', $shop_id);
                })
                ->get();

        $serviceArr = array();
        $barberArr = array();
        foreach ($bookingServices as $_service) {
            $serviceArr[] = $_service->name;
            $barberArr[] = (!empty($_service->barber->name)) ? $_service->barber->name : '-';
        }
        return $serviceStr = implode(", ", $serviceArr);
    }

    protected function _mapUrlLatLong($mapLink) {
        $_location = array();
        $_location['lat'] = $_location['long'] = "";
        $_location['map'] = $mapLink;

        $ch = curl_init($mapLink);
        curl_setopt_array($ch, array(
            CURLOPT_FOLLOWLOCATION => TRUE, // the magic sauce
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_SSL_VERIFYHOST => FALSE, // suppress certain SSL errors
            CURLOPT_SSL_VERIFYPEER => FALSE,
        ));
        curl_exec($ch);
        $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        //prd($url);
        if (preg_match("/@(-?\d+\.\d+),(-?\d+\.\d+),(\d+\.?\d?)+z/", $url)) {
            preg_match('/@(\-?[0-9]+\.[0-9]+),(\-?[0-9]+\.[0-9]+)/', $url, $latLong);
            $_location['lat'] = (!empty($latLong[1]) ? $latLong[1] : "");
            $_location['long'] = (!empty($latLong[2]) ? $latLong[2] : "");
        }
        
        return $_location;

        //AIzaSyBqXML4sDfOx36eecoEWeZPqlI4W4Nh7QA
        //See me on Google Maps! https://maps.app.goo.gl/5RmF9R1dHzJAwbXf8
    }

}
