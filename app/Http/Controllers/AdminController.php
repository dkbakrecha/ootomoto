<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\User;
use App\ShopOffer;
use App\Service;
use App\Area;
use App\SiteSetting;
use App\Booking;
use Illuminate\Support\Carbon;

class AdminController extends Controller {

    public function __construct() {
        //$this->middleware('auth:admin');
        $this->middleware('auth:admin')->except(['create', 'store']);
    }

    /**
     * Dashboard Functionalities of Admin
     */
    public function index() {
        $services = \App\BookingService::where('status', '=', 1)
                ->with(['service'])
                ->orderBy('count', 'DESC')
                ->groupBy('service_id')
                ->select('service_id', DB::raw('SUM( price ) as price'), DB::raw('COUNT( * ) as "count"'))
                ->get();
        //prd($services);

        $customer = User::where('status', '!=', 2)->where('user_type', '=', 2)->count();
        //return $customer;
        $getUser24Hrs = User::where('status', '!=', 2)
                        ->whereDate('created_at', '>=', date('Y-m-d H:i:s', strtotime('-1 days')))->count();

        $getUserLast10Days = User::where('status', '!=', 2)
                        ->whereDate('created_at', '>=', date('Y-m-d H:i:s', strtotime('-10 days')))->count();

        $getUserPrev10Days = User::where('status', '!=', 2)
                ->whereDate('created_at', '>=', date('Y-m-d H:i:s', strtotime('-20 days')))
                ->whereDate('created_at', '<=', date('Y-m-d H:i:s', strtotime('-10 days')))
                ->count();

        $getUserPrev10Days = ($getUserPrev10Days === 0) ? 1 : $getUserPrev10Days;

        $user_percentage = number_format((float) (($getUserLast10Days - $getUserPrev10Days) / $getUserPrev10Days) * 100, 1, '.', '');

        /* Last 10 days statistics */
        // Build an array of the dates we want to show, oldest first
        $dates = collect();
        foreach (range(-10, 0) AS $i) {
            $date = Carbon::now()->addDays($i)->format('Y-m-d');
            $dates->put($date, 0);
        }

// Get the post counts
        $users = User::where('created_at', '>=', $dates->keys()->first())
                ->groupBy('date')
                ->orderBy('date')
                ->get([
                    DB::raw('DATE( created_at ) as date'),
                    DB::raw('COUNT( * ) as "count"')
                ])
                ->pluck('count', 'date');

// Merge the two collections; any results in `$posts` will overwrite the zero-value in `$dates`
        $dates = $dates->merge($users);

        $comma_separated = implode(",", $dates->toArray());


        /**
         * Booking Related Data
         */
        $bookingTile = array();
        $bookingTile['totalBooking'] = Booking::where('status', '!=', 0)
                ->count();

        $getBookingLast10Days = Booking::where('status', '!=', 0)
                        ->whereDate('created_at', '>=', date('Y-m-d H:i:s', strtotime('-10 days')))->count();

        $getBookingPrev10Days = User::where('status', '!=', 0)
                ->whereDate('created_at', '>=', date('Y-m-d H:i:s', strtotime('-20 days')))
                ->whereDate('created_at', '<=', date('Y-m-d H:i:s', strtotime('-10 days')))
                ->count();

        $bookingTile['Prev10Days'] = $getBookingPrev10Days;

        // Handle division by 0 error        
        $getBookingPrev10Days = ($getBookingPrev10Days === 0) ? 1 : $getBookingPrev10Days;
        $bookingTile['bookingPresentage'] = number_format((float) (($getBookingLast10Days - $getBookingPrev10Days) / $getBookingPrev10Days) * 100, 1, '.', '');

        $datesBooking = collect();
        foreach (range(-10, 0) AS $i) {
            $date = Carbon::now()->addDays($i)->format('Y-m-d');
            $datesBooking->put($date, 0);
        }

// Get the post counts
        $bookings = Booking::where('created_at', '>=', $datesBooking->keys()->first())
                ->where('status', '!=', 0)
                ->groupBy('date')
                ->orderBy('date')
                ->get([
                    DB::raw('DATE( created_at ) as date'),
                    DB::raw('COUNT( * ) as "count"')
                ])
                ->pluck('count', 'date');

// Merge the two collections; any results in `$posts` will overwrite the zero-value in `$dates`
        $datesBooking = $datesBooking->merge($bookings);
        //prd($datesBooking);
        $bookingTile['bookingGraph'] = implode(",", $datesBooking->toArray());
        /** Booking Tile end here */
        /**
         * Earning Related Data
         */
        $bookingTile['totalEarning'] = Booking::where('status', '!=', 0)
                ->sum('commission_amount');

        $getEarningLast10Days = Booking::where('status', '!=', 0)
                        ->whereDate('created_at', '>=', date('Y-m-d H:i:s', strtotime('-10 days')))->sum('commission_amount');

        $getEarningPrev10Days = Booking::where('status', '!=', 0)
                ->whereDate('created_at', '>=', date('Y-m-d H:i:s', strtotime('-20 days')))
                ->whereDate('created_at', '<=', date('Y-m-d H:i:s', strtotime('-10 days')))
                ->sum('commission_amount');

        $bookingTile['EarningPrev10Days'] = $getEarningPrev10Days;

        // Handle division by 0 error        
        $getEarningPrev10Days = ($getEarningPrev10Days === 0) ? 1 : $getEarningPrev10Days;

        //if ($getEarningPrev10Days != 0) {
            $bookingTile['earningPresentage'] = number_format((float) (($getEarningLast10Days - $getEarningPrev10Days) / $getEarningPrev10Days) * 100, 1, '.', '');
        //} else {
        //    $bookingTile['earningPresentage'] = 0;
        //}

        $datesEarning = collect();
        foreach (range(-10, 0) AS $i) {
            $date = Carbon::now()->addDays($i)->format('Y-m-d');
            $datesEarning->put($date, 0);
        }

// Get the post counts
        $earnings = Booking::where('created_at', '>=', $datesBooking->keys()->first())
                ->where('status', '!=', 0)
                ->groupBy('date')
                ->orderBy('date')
                ->get([
                    DB::raw('DATE( created_at ) as date'),
                    DB::raw('SUM( commission_amount ) as amount')
                ])
                ->pluck('amount', 'date');

// Merge the two collections; any results in `$posts` will overwrite the zero-value in `$dates`
        $datesEarning = $datesEarning->merge($earnings);
        //prd($datesBooking);
        $bookingTile['earningGraph'] = implode(",", $datesEarning->toArray());
        /** Booking Tile end here */
        /* $areas = Area::leftJoin('bookings', function($join) {
          $join->on('areas.id', '=', 'bookings.area_id');
          })
          ->get([
          DB::raw('SUM( final_amount ) as total_amount'),
          DB::raw('COUNT( bookings.id ) as "count"')
          ]); */
        $areas = Area::with(['bookings'])
            /*->whereHas('bookings', function ($query) {
                    $query->where('bookings.status', '=', 1); // 1 = Complete, 2 = Confirmed
                })*/
            ->get();
        //prd($areas);
        $areasmap = Area::skip(0)->take(4)->get();

        //$this->cronOfferExpire();
        // USER REGISTRATION MORRIS CHART CODE
        $startDate = new Carbon('first day of this month');
        $endDate = new Carbon('last day of this month');

        // Period over which we want user registration data
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

        // Convert the period to an array of dates
        // $dates = $period->toArray();
        $userData = [];

        foreach ($period as $key => $date) {
            // echo $date->format('Y-m-d');

            $userData[$key]['y'] = $date->format('Y-m-d');

            DB::enableQueryLog();

            $usersCount = User::whereDate('created_at', $date)
                    ->get()
                    ->count();
            // prd(DB::getQueryLog());


            $userData[$key]['usersCount'] = $usersCount;
        }

        // BOOKINGS MORRIS CHART CODE
        $startDate = new Carbon('first day of this month');
        $endDate = new Carbon('last day of this month');

        // Period over which we want user registration data
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

        // Convert the period to an array of dates
        // $dates = $period->toArray();
        $bookingData = [];

        foreach ($period as $key => $date) {
            // echo $date->format('Y-m-d');

            $bookingData[$key]['y'] = $date->format('Y-m-d');

            $bookingsCount = Booking::whereDate('created_at', $date)
                    ->get()
                    ->count();


            $bookingData[$key]['bookingsCount'] = $bookingsCount;
        }

        $waitingBookings = Booking::where('status', '=', 3)->count();

        //return $getUser24Hrs;
        return view('admin.dashboard', ['user24hrs' => $getUser24Hrs,
            'services' => $services,
            'customer' => $customer,
            'getUserLast10Days' => $getUserLast10Days,
            'getUserPrev10Days' => $getUserPrev10Days,
            'user_percentage' => $user_percentage,
            'dates' => $comma_separated,
            'bookingTile' => $bookingTile,
            'areas' => $areas,
            'areasmap' => $areasmap,
            'userData' => json_encode($userData),
            'bookingData' => json_encode($bookingData),
            'waitingBookings' => $waitingBookings
        ]);
    }

    /**
     * CRON functionality for Offer Expire
     */
    public function cronOfferExpire() {
        $expireOffers = ShopOffer::where('expire_date', '<', Carbon::now('Asia/Kolkata'))
                ->where('status', '!=', 4)
                ->get();
//prd(Carbon::now());
        foreach ($expireOffers as $_offer) {
            $offerData = ShopOffer::findOrFail($_offer->id);
            $offerData->status = 4;
            $offerData->save();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('admin.auth.register');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        // validate the data
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);

// store in the database
        $admins = new User();
        $admins->name = $request->name;
        $admins->email = $request->email;
        $admins->password = bcrypt($request->password);
        $admins->user_type = 3; //Admin
        $admins->save();

        return redirect()->route('admin.auth.login');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

    public function getSetting() {

        $settings = DB::table('site_settings')->orderBy('group_id')->get();
        $currentUser = Auth::guard('admin')->user();

        return view('admin.settings.index', ['settings' => $settings]);
    }

    public function updateSetting(Request $request) {
        $data = $request->all();

        //return $data;
        foreach ($data as $key => $setting) {

            if (!in_array($key, array("_token"))) {
                $settingData = SiteSetting::where([
                            ['unique_key', '=', $key],
                        ])
                        ->first();
                $settingData->value = $setting;
                $settingData->save();
            }

            
        }
        return redirect()->route('admin.settings')->with('success', __('messages.setting_update_success'));
    }

    public function search(Request $request) {
        $_searchTerm = $request->q;

        $userData = array();
        $bookingData = array();
        if (!empty($request->q)) {
            $userData = User::Where(function ($query) use ($request) {
                                $query->where('name', 'like', "%" . $request->q . "%")
                                ->orWhere('unique_id', 'like', "%" . $request->q . "%")
                                ->orWhere('phone', 'like', "%" . $request->q . "%");
                            })
                            ->where('status', '!=', 4)
                            ->get()->toArray();

            $bookingData = Booking::With(['customer'])
                            ->where(function ($query) use ($request) {
                                if (!empty($request->q)) {
                                    $query->where('unique_id', 'like', "%" . $request->q . "%")
                                    ->orWhereHas('customer', function($query) use ($request) {
                                        $query->where('name', 'like', "%" . $request->q . "%")
                                        ->orWhere('unique_id', 'like', "%" . $request->q . "%")
                                        ->orWhere('phone', 'like', "%" . $request->q . "%");
                                    });
                                }
                            })
                            ->orderBy('id', 'DESC')
                            ->get()->toArray();
        }
        //prd($bookingData);
        return view('admin.search', [
            'searchTerm' => $_searchTerm,
            'userData' => $userData,
            'bookingData' => $bookingData
        ]);
    }

    protected function createPeriod($duration) {

        return $period;
    }

    // AJAX FUNCTION TO FETCH USER REGISTRATION DATA FOR MORRIS CHART
    public function getUserRegistrationChartData(Request $request) {
        if ($request->ajax()) {
            $duration = $request->duration;

            // Convert the period to an array of dates
            // $dates = $period->toArray();
            $userData = [];

            if ($duration == 'lastMonth') {    // fetch data for the last month 
                $startDate = new Carbon('first day of last month');
                $endDate = new Carbon('last day of last month');

                // Period over which we want user registration data
                $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

                foreach ($period as $key => $date) {
                    // echo $date->format('Y-m-d');

                    $userData[$key]['y'] = $date->format('Y-m-d');

                    DB::enableQueryLog();

                    $usersCount = User::whereDate('created_at', $date)
                            ->get()
                            ->count();
                    // prd(DB::getQueryLog());


                    $userData[$key]['usersCount'] = $usersCount;
                }
            } else if ($duration == 'last6Months') {    // fetch data for the last 6 months month 
                $now = Carbon::now();
                $nowString = $now->format('Y-m-d');
                $sixMonthAgo = $now->subMonths(6);
                $sixMonthAgoString = $sixMonthAgo->format('Y-m-d');

                $period = \Carbon\CarbonPeriod::create($sixMonthAgoString, '1 month', $nowString);

                foreach ($period as $key => $date) {

                    $userData[$key]['y'] = $date->format('Y-m');

                    DB::enableQueryLog();

                    $usersCount = User::whereMonth('created_at', $date->format('m'))
                            ->whereYear('created_at', $date->format('Y'))
                            ->get()
                            ->count();

                    $userData[$key]['usersCount'] = $usersCount;
                }
            } else if ($duration == 'last1Year') {    // fetch data for the last year 
                $now = Carbon::now();
                $nowString = $now->format('Y-m-d');
                $oneYearAgo = $now->subYear();
                $oneYearAgoString = $oneYearAgo->format('Y-m-d');

                $period = \Carbon\CarbonPeriod::create($oneYearAgoString, '1 month', $nowString);

                foreach ($period as $key => $date) {

                    $userData[$key]['y'] = $date->format('Y-m');

                    DB::enableQueryLog();

                    $usersCount = User::whereMonth('created_at', $date->format('m'))
                            ->whereYear('created_at', $date->format('Y'))
                            ->get()
                            ->count();

                    $userData[$key]['usersCount'] = $usersCount;
                }
            } else if ($duration == 'currentMonth') {    // fetch data for the current month 
                $startDate = new Carbon('first day of this month');
                $endDate = new Carbon('last day of this month');

                // Period over which we want user registration data
                $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

                foreach ($period as $key => $date) {

                    $userData[$key]['y'] = $date->format('Y-m-d');

                    DB::enableQueryLog();

                    $usersCount = User::whereDate('created_at', $date)
                            ->get()
                            ->count();

                    $userData[$key]['usersCount'] = $usersCount;
                }
            }

            return json_encode($userData);
        }
    }

    // AJAX FUNCTION TO FETCH BOOKINGS DATA FOR MORRIS CHART
    public function getBookingsChartData(Request $request) {
        if ($request->ajax()) {
            $duration = $request->duration;

            // Convert the period to an array of dates
            // $dates = $period->toArray();
            $bookingData = [];

            if ($duration == 'lastMonth') {    // fetch data for the last month 
                $startDate = new Carbon('first day of last month');
                $endDate = new Carbon('last day of last month');

                // Period over which we want user registration data
                $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

                foreach ($period as $key => $date) {
                    // echo $date->format('Y-m-d');

                    $bookingData[$key]['y'] = $date->format('Y-m-d');

                    DB::enableQueryLog();

                    $bookingsCount = Booking::whereDate('created_at', $date)
                            ->get()
                            ->count();
                    // prd(DB::getQueryLog());


                    $bookingData[$key]['bookingsCount'] = $bookingsCount;
                }
            } else if ($duration == 'last6Months') {    // fetch data for the last 6 months month 
                $now = Carbon::now();
                $nowString = $now->format('Y-m-d');
                $sixMonthAgo = $now->subMonths(6);
                $sixMonthAgoString = $sixMonthAgo->format('Y-m-d');

                $period = \Carbon\CarbonPeriod::create($sixMonthAgoString, '1 month', $nowString);

                foreach ($period as $key => $date) {

                    $bookingData[$key]['y'] = $date->format('Y-m');

                    DB::enableQueryLog();

                    $bookingsCount = Booking::whereMonth('created_at', $date->format('m'))
                            ->whereYear('created_at', $date->format('Y'))
                            ->get()
                            ->count();

                    $bookingData[$key]['bookingsCount'] = $bookingsCount;
                }
            } else if ($duration == 'last1Year') {    // fetch data for the last year 
                $now = Carbon::now();
                $nowString = $now->format('Y-m-d');
                $oneYearAgo = $now->subYear();
                $oneYearAgoString = $oneYearAgo->format('Y-m-d');

                $period = \Carbon\CarbonPeriod::create($oneYearAgoString, '1 month', $nowString);

                foreach ($period as $key => $date) {

                    $bookingData[$key]['y'] = $date->format('Y-m');

                    DB::enableQueryLog();

                    $bookingsCount = Booking::whereMonth('created_at', $date->format('m'))
                            ->whereYear('created_at', $date->format('Y'))
                            ->get()
                            ->count();

                    $bookingData[$key]['bookingsCount'] = $bookingsCount;
                }
            } else if ($duration == 'currentMonth') {    // fetch data for the current month 
                $startDate = new Carbon('first day of this month');
                $endDate = new Carbon('last day of this month');

                // Period over which we want user registration data
                $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

                foreach ($period as $key => $date) {

                    $bookingData[$key]['y'] = $date->format('Y-m-d');

                    DB::enableQueryLog();

                    $bookingsCount = Booking::whereDate('created_at', $date)
                            ->get()
                            ->count();

                    $bookingData[$key]['bookingsCount'] = $bookingsCount;
                }
            }

            return json_encode($bookingData);
        }
    }

}
