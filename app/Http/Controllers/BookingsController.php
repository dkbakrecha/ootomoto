<?php

namespace App\Http\Controllers;

use Auth;
use App\Booking;
use App\user;
use App\BookingService;
use App\ShopService;
use App\ShopOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Receipt;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingApprove;
use App\Mail\BookingReject;

class BookingsController extends Controller {

    public function __construct() {
        $this->middleware('auth:web');
    }

    public function schedule() {
        return view('bookings.schedule');
    }

    public function index() {
        $bookings = Booking::latest('created_at')
                ->where(function ($query) {
                    $query->where('status', '=', 1)
                    ->orWhere('status', '=', 2)
                    ->orWhere('status', '=', 4)
                    ->orWhere('status', '=', 5);
                })
                ->where('shop_id', '=', $this->_shop_id())
                ->with(['customer'])
                ->orderBy('id', 'DESC')
                ->get();

        return view('bookings.index', compact('bookings'));
    }

    public function reservation() {
        return view('bookings.reservation');
    }

    public function getReservation() {
        $bookings = Booking::Where('status', '!=', 0)
                        ->where('shop_id', '=', $this->_shop_id())
                        ->with(['customer'])->get()->toArray();

        $_bookingRes = array();

        foreach ($bookings as $booking) {
            $_bRes = array();
            $_bRes['id'] = $booking['id'];
            $_bRes['title'] = $booking['customer']['name'];
            $_bRes['start'] = $booking['booking_starttime'];
            $_bRes['end'] = $booking['booking_endtime'];
            $_bRes['allDay'] = false;
            $_bRes['backgroundColor'] = '#00c0ef';
            $_bRes['borderColor'] = '#00c0ef';
            $_bookingRes[] = $_bRes;
            /*  {
              title: 'Lunch',
              : new Date(y, m, d, 12, 0),
              : new Date(y, m, d, 14, 0),
              : false,
              : '#00c0ef', //Info (aqua)
              :  //Info (aqua)
              }

             */
        }

        return response()->json(
                        $_bookingRes
                        , 201);
    }

    public function getSchedules() {
        $_shopId = $this->_shop_id();

        $bookings = Booking::Where(function ($query) {
                    $query->where('status', '=', 2) // Confirmed
                    ->orWhere('status', '=', 3)
                    ->orWhere('status', '=', 1); // Pending
                })
                ->where('shop_id', '=', $_shopId)
                ->pluck('id')
                ->toArray();

        /* Get Booking Service name and Barber name string */
        $bookingServices = BookingService::WhereIn('booking_id', $bookings)
                        ->with(['barber'])
                        ->join('shop_services', function($query) use ($_shopId) {
                            $query->on('shop_services.service_id', '=', 'booking_services.service_id')
                            ->where('shop_services.shop_id', '=', $_shopId);
                        })
                        ->get()->toArray();

        $_bookingRes = array();

        foreach ($bookingServices as $service) {
            $_bRes = array();
            $_bRes['id'] = $service['booking_id'];
            $_bRes['title'] = $service['name'] . "(" . $service['barber']['name'] . ")";
            $_bRes['start'] = $service['starttime'];
            $_bRes['end'] = $service['endtime'];
            $_bRes['allDay'] = false;
            $_bRes['backgroundColor'] = '#00c0ef';
            $_bRes['borderColor'] = '#00c0ef';
            $_bookingRes[] = $_bRes;
        }

        return response()->json(
                        $_bookingRes
                        , 201);
    }

    public function getBooking(Request $request) {
        $bookingId = $request->id;
        $bookingData = Booking::Where('id', '=', $bookingId)
                ->with(['customer', 'shop'])
                ->first();

        $shop_id = $bookingData->shop_id;

        /* Get Booking Service name and Barber name string */
        $bookingServices = BookingService::Where('booking_id', '=', $bookingId)
                ->with(['barber'])
                ->join('shop_services', function($query) use ($shop_id) {
                    $query->on('shop_services.service_id', '=', 'booking_services.service_id')
                    ->where('shop_services.shop_id', '=', $shop_id);
                })
                /* ->select(['booking_services.booking_id', 'booking_services.service_id', 'shop_services.name', 'barber.name']) */
                ->get();

        $serviceArr = array();
        $barberArr = array();
        foreach ($bookingServices as $_service) {
            $serviceArr[] = $_service->name;
            $barberArr[] = (!empty($_service->barber->name)) ? $_service->barber->name : '-';
        }
        $serviceStr = implode(", ", $serviceArr);

        $barberArr = array_unique($barberArr);
        $barberStr = implode(", ", $barberArr);
        /* Get Booking Service name string end */

        $bookingData->sub_total = $bookingData->sub_total . " SAR";
        $bookingData->final_amount = $bookingData->final_amount . " SAR";
        $bookingData->booking_date = date('d/m/Y', strtotime($bookingData->booking_date));
        $bookingData->booking_starttime = date('h:i A', strtotime($bookingData->booking_starttime)) . ' - ' . date('h:i A', strtotime($bookingData->booking_endtime));
        $bookingData->payment_method = ($bookingData->payment_method == 1) ? __("messages.cash") : __("messages.card");

        if ($bookingData->status == 1) {
            $bookingData->booking_status = __("messages.complete");
            $bookingData->booking_class = __("booking-status-complete");
        } elseif ($bookingData->status == 2) {
            $bookingData->booking_status = __("messages.confirmed");
            $bookingData->booking_class = __("booking-status-confirmed");
        } elseif ($bookingData->status == 3) {
            $bookingData->booking_status = __("messages.pending");
            $bookingData->booking_class = __("booking-status-pending");
        } elseif ($bookingData->status == 4) {
            $bookingData->booking_status = __("messages.cancelled");
            $bookingData->booking_class = __("booking-status-cancelled");
        } elseif ($bookingData->status == 5) {
            $bookingData->booking_status = __("messages.rejected");
            $bookingData->booking_class = __("booking-status-rejected");
        }

        return response()->json(['data' => $bookingData, 'services' => $serviceStr, 'barber' => $barberStr]);
    }

    public function walking_store(Request $request) {
        $_shop_id = $this->_shop_id();

        $this->validate($request, [
            'name' => 'required',
            'email' => ['required', 'string', 'email', 'max:100'],
            'phone' => 'required|digits:10',
            'services' => 'required',
        ]);

        $services = $request->services;
        $customer_id = $this->getCustomerID($request);

        $_ssObj = ShopService::WhereIn('service_id', $services)
                ->select('id', 'service_id', 'name', 'price', 'duration')
                ->where('shop_id', '=', $_shop_id);

        $shopServices = $_ssObj->get()->toArray();
        $servicesDuration = $_ssObj->sum('duration');
        $servicesPrice = $_ssObj->sum('price');

        $_currentTime = Carbon::now('Asia/Kolkata');

        $_start_time = date("Y-m-d H:i:s", strtotime($_currentTime));
        $_end_time = date("Y-m-d H:i:s", strtotime($_start_time . "+" . $servicesDuration . " minutes"));

        //Assign barbers to services
        $_i = 0;
        $serviceEmpty = 0;
        foreach ($shopServices as $_service) {
            $barberServices = \App\BarberService::where("shop_id", "=", $_shop_id)
                            ->where('service_id', '=', $_service['service_id'])
                            ->pluck('barber_id')->toArray();

            if (empty($barberServices)) {
                $serviceEmpty = 1;
            }

            $shopServices[$_i]['barbers'] = $barberServices;
            $_i++;
        }

        if ($serviceEmpty == 1) {
            return redirect("/home")->with("error", __('messages.nobarber_to_service'));
        }
        $shopInfo = User::where('id', '=', $_shop_id)
                ->first();

        //Save Booking Here
        $booking = new Booking();
        $booking->unique_id = $this->unique_key("BO", "bookings");
        $booking->payment_method = 1;
        $booking->booking_mode = 1;
        $booking->customer_id = $customer_id;
        $booking->shop_id = $_shop_id;
        $booking->booking_date = $_start_time;
        $booking->booking_starttime = date("Y-m-d H:i:s", strtotime($_start_time));
        $booking->booking_endtime = date("Y-m-d H:i:s", strtotime($_end_time));
        $booking->sub_total = (String) $servicesPrice;
        $booking->vat_amount = "0";
        $booking->offer_amount = "0";
        $booking->final_amount = (String) $servicesPrice;
        $booking->updated_at = $_currentTime;
        $booking->checkout_time = $_currentTime;

        $commission = $shopInfo->commission;
        if (!empty($commission)) {
            if ($shopInfo->commission_type == 1) {
                //Fixed Commission
                $booking->commission_type = 1;
                $booking->commission_amount = $commission;
            } else {
                //Persentage Commission
                $_comm_amo = (($commission / 100) * $booking->final_amount);
                $booking->commission_amount = $_comm_amo;
            }
        }

        $booking->status = 1; //1 = Walking Always Complete

        $shop = User::where('id', '=', $_shop_id)->first();
        if (!empty($shop->area_id)) {
            $booking->area_id = $shop->area_id;
        }

        $booking->save();
        $this->create_receipt($booking, "WALKIN");

        //Save Booking Services
        $_serviceStartTime = $_start_time;
        foreach ($shopServices as $_services) {
            $_serviceEndTime = date("Y-m-d H:i:s", strtotime($_serviceStartTime . "+" . $_services['duration'] . " minutes"));

            $bookingService = new BookingService();
            $bookingService->booking_id = $booking->id;
            $bookingService->service_id = $_services['service_id'];
            $bookingService->shop_id = $this->_shop_id();
            $bookingService->barber_id = $_services['barbers'][0];
            $bookingService->starttime = $_serviceStartTime;
            $bookingService->endtime = $_serviceEndTime;
            $bookingService->status = 1;
            $bookingService->price = $_services['price'];
            $bookingService->save();

            $_serviceStartTime = date("Y-m-d H:i:s", strtotime($_serviceEndTime . "1 minutes"));
        }

        return redirect("/home")->with("success", __('messages.walking_success'));
    }

    public function getCustomerID(Request $request) {
        $userData = User::where(function ($query) use($request) {
                    $query->where('email', '=', $request->email)
                    ->orWhere('phone', '=', $request->phone);
                })
                ->where('user_type', '=', 2)
                ->first();

        if (empty($userData)) {
            //Create new customer
            $userData = User::create([
                        'name' => $request->name,
                        'unique_id' => $this->unique_key("CUS", "users"),
                        'email' => $request->email,
                        'password' => Hash::make("#Demo1234"),
                        'isAdmin' => 2, // 2 = Customer
                        'user_type' => 2, // 2 = Customer
                        'status' => 4, // 3 Walking
                        'gender' => 'm',
                        'phone' => $request->phone,
                        'token' => "",
                        'confirmation_alert' => 0 //Auto On alert
            ]);
        }

        return $userData->id;
    }

    /** Status Rejected When SP/Supervisor approve booking */
    public function booking_reject(Request $request) {
        $bookingData = Booking::findOrFail($request->booking_id);
        $bookingData->status = 5; //Rejected - When SP approve booking
// Send Push notification to customer

        $userData = User::where('id', '=', $bookingData->customer_id)
                ->first();
        //prd($userData);
        if ($bookingData->save()) {
            //Reject Mail to customer
            Mail::to($userData->email)->send(new BookingReject($userData, $bookingData));

            //Get User Data
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
            return redirect('/home')->with('success', __("messages.booking_rejected_success"));
        } else {
            return redirect('/home')->with('error', 'Some error occur. booking cannot be rejected');
        }
    }

    /** Status Approved When SP/Supervisor approve booking */
    public function booking_approve(Request $request) {
        $bookingData = Booking::findOrFail($request->booking_id);
        $userData = User::where('id', '=', $bookingData->customer_id)
                ->first();

        $approved = false;
        if ($bookingData->payment_method == 2) {
            $bookingDescription = "Payment for amount " . $bookingData->final_amount . " for booking " . $bookingData->unique_id;
            $telrTransaction = $this->telr_pay($bookingData->unique_id, $bookingDescription, $bookingData->final_amount, $bookingData->telr_token);

            if ($telrTransaction == false) {
                //Cancel (Reject 5) Booking in case of payment not capture..
                $bookingData->status = 5; //Booking in cancellation
                $bookingData->cancel_reason = "Booking Rejected by Service Provider due to not captureing payment";
                $bookingData->cancel_date = Carbon::now();
                $bookingData->telr_token = "";
                $bookingData->save();

                //Reject Mail to customer if Payment not capture
                Mail::to($userData->email)->send(new BookingReject($userData, $bookingData));
            } else {
                // If Payment capture by telr then Update telr token in Booking and make a receipt
                $bookingData->telr_token = $telrTransaction;
                $bookingData->status = 2; //Approved - When SP approve booking When Card
                $bookingData->save();
                $this->create_receipt($bookingData, $telrTransaction);

                //Approve Mail to customer
                Mail::to($userData->email)->send(new BookingApprove($userData, $bookingData));
                $approved = true;
            }
        } else {
            $bookingData->status = 2; //Approved - When SP approve booking When Cash
            $bookingData->save();

            //Approve Mail to customer
            Mail::to($userData->email)->send(new BookingApprove($userData, $bookingData));
            $approved = true;
        }

        if ($approved) {
            if ($userData->confirmation_alert == 1) {
                // Send Push notification to customer
                $device = User::find($bookingData->customer_id)
                        ->mobileSessions()
                        ->where('status', '=', 1)
                        ->first();

                // Create push notification content
                //$body = 'Your booking has been approved';
                $body = $this->message("booking_approve_notifiaction", $bookingData->customer_id);

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


            return redirect('/home')->with('success', __("messages.booking_approve_success"));
        } else {
            return redirect('/home')->with('error', 'Some error occur. booking cannot be approved');
        }
    }

    /** Status Complete When SP/Supervisor manually complete booking */
    public function booking_complete(Request $request) {
        $bookingData = Booking::findOrFail($request->booking_id);
        $bookingData->status = 1; //Complete - When SP approve booking

        /** update Booking services status to 1 when booking is complete */
        $bServices = BookingService::where('booking_id', '=', $request->booking_id)
                ->get();

        foreach ($bServices as $bService) {
            $bsData = BookingService::where('id', '=', $bService->id)->first();
            $bsData->status = 1;
            $bsData->save();
        }

        if ($bookingData->save()) {
            //Create Receipt if payment method is Cash
            $this->create_receipt($bookingData, "CASH");

            return redirect(request()->headers->get('referer'))->with('success', __('messages.booking_successfully_complete'));
        } else {
            return redirect(request()->headers->get('referer'))->with('error', 'Some error occur. Offer cannot be rejected');
        }
    }

    /** Status No Show When SP/Supervisor manually when customer not come */
    public function booking_noshow(Request $request) {
        $bookingData = Booking::findOrFail($request->booking_id);
        //$bookingData->status = 5; //Complete - When SP approve booking
        $bookingData->no_show = 1;

        /** update Booking services status to 1 when booking is complete */
        $bServices = BookingService::where('booking_id', '=', $request->booking_id)
                ->get();

        foreach ($bServices as $bService) {
            $bsData = BookingService::where('id', '=', $bService->id)->first();
            $bsData->status = 2;
            $bsData->save();
        }

        if ($bookingData->save()) {
            //Create Receipt if payment method is Cash
            //$this->create_receipt($bookingData, "CASH");

            return redirect(request()->headers->get('referer'))->with('success', __('messages.booking_successfully_complete'));
        } else {
            return redirect(request()->headers->get('referer'))->with('error', 'Some error occur. Offer cannot be rejected');
        }
    }

    /** Status Cancel When SP/Supervisor cancel booking */
    public function booking_cancel(Request $request) {
        $bookingInfo = Booking::findOrFail($request->booking_id);
        $userData = User::where('id', '=', $bookingInfo->customer_id)
                ->first();

        $transactionStatus = true;
        //If booking is confirmed with card payment and cancel then refund
        if ($bookingInfo->status == 2) {
            //prd($bookingInfo->booking_date);

            if ($bookingInfo->payment_method == 2) {
                if (Carbon::parse($bookingInfo->booking_date, $request->tz)->gt(Carbon::now(env('TIME_ZONE')))) {
                    //If request booking date is not pass Then make full refund.
                    $bookingDescription = "Refund for amount " . $bookingInfo->final_amount . " for booking " . $bookingInfo->unique_id;
                    $telrTransaction = $this->telr_pay($bookingInfo->unique_id, $bookingDescription, $bookingInfo->final_amount, $bookingInfo->telr_token, 'refund');
                } else {
                    //If request booking date passed Then make partial refund.
                    //According to term and services 70% amount refund
                    $refundedAmount = ((30 / 100) * $bookingInfo->final_amount);
                    $bookingDescription = "Refund for amount " . $refundedAmount . " for booking " . $bookingInfo->unique_id;
                    $telrTransaction = $this->telr_pay($bookingInfo->unique_id, $bookingDescription, $refundedAmount, $bookingInfo->telr_token, 'refund');
                }

                if ($telrTransaction == false) {
                    return $this->sendError($this->message("booking_cancel_tech_error", $bookingInfo->customer_id), null, 400);
                } else {
                    $bookingInfo->status = 4; //Booking in cancellation
                    $bookingInfo->cancel_reason = "Cancel by Service Provider"; //Booking in cancellation
                    $bookingInfo->cancel_date = Carbon::now(env('TIME_ZONE'));
                    $bookingInfo->telr_token = "";

                    $this->create_refund($bookingInfo, $telrTransaction, $bookingInfo->final_amount, (!empty($request->reason)) ? $request->reason : "");
                }
            } else {
                //Cash Condition

                $bookingInfo->status = 4; //Booking in cancellation
                $bookingInfo->cancel_reason = "Cancel by Service Provider"; //Booking in cancellation
                $bookingInfo->cancel_date = Carbon::now(env('TIME_ZONE'));
                $bookingInfo->telr_token = "";
            }
        }

        if ($bookingInfo->save()) {
            /** update Booking services status to 2 when booking is cancel/reject */
            $bServices = \App\BookingService::where('booking_id', '=', $bookingInfo->id)
                    ->get();

            foreach ($bServices as $bService) {
                $bsData = \App\BookingService::where('id', '=', $bService->id)->first();

                $bsData->status = 2;
                $bsData->save();
            }
            /* Updation end */

            return redirect(request()->headers->get('referer'))->with('success', __('messages.booking_successfully_cancel'));
        } else {
            return redirect(request()->headers->get('referer'))->with('error', 'Some error occured. booking cannot be cancelled');
        }
    }

}
