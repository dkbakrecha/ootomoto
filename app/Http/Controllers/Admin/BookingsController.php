<?php

namespace App\Http\Controllers\Admin;

use App\Booking;
use App\BookingService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BookingsController extends Controller {

    public function __construct() {
        $this->middleware('auth:admin');
    }

    public function index() {
        $bookings = Booking::latest('created_at')
                ->where('status', '!=', 0)
                ->with(['customer'])
                ->get();

        return view('admin.bookings.index', compact('bookings'));
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

}