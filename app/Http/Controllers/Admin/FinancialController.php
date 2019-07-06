<?php

namespace App\Http\Controllers\Admin;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;

class FinancialController extends Controller {

    public function __construct() {
        $this->middleware('auth:admin');
    }

    public function index() {
        $statics = array();

        $_totalRefund = \App\Refund::latest()
                ->select(DB::raw('SUM( amount ) as amount'))
                ->first();

        $_totalReceipt = \App\Receipt::latest()
                ->select(DB::raw('SUM( final_amount ) as amount'))
                ->first();

        $_totalRevenue = \App\Booking::latest()
                ->select(DB::raw('SUM(commission_amount) as adminCommission'))
                ->where('status', '=', 1)
                ->first();
        //prd($_totalRevenue);
        /** Last 10 Days Refund */
        $dateRefund = $dateReceipt = $dateRevenue = collect();
        foreach (range(-10, 0) AS $i) {
            $date = Carbon::now()->addDays($i)->format('Y-m-d');
            $dateRefund->put($date, 0);
            $dateReceipt->put($date, 0);
            $dateRevenue->put($date, 0);
        }

// Get the post counts
        $refunds = \App\Refund::where('created_at', '>=', $dateRefund->keys()->first())
                ->groupBy('date')
                ->orderBy('date')
                ->get([
                    DB::raw('DATE( created_at ) as date'),
                    DB::raw('SUM( amount ) as "amount"')
                ])
                ->pluck('amount', 'date');

        $receipts = \App\Receipt::where('created_at', '>=', $dateReceipt->keys()->first())
                ->groupBy('date')
                ->orderBy('date')
                ->get([
                    DB::raw('DATE( created_at ) as date'),
                    DB::raw('SUM( final_amount ) as "amount"')
                ])
                ->pluck('amount', 'date');

        $revenue = \App\Booking::where('created_at', '>=', $dateRevenue->keys()->first())
                ->where('status', '=', 1)
                ->groupBy('date')
                ->orderBy('date')
                ->get([
                    DB::raw('DATE( created_at ) as date'),
                    DB::raw('SUM( commission_amount ) as "amount"')
                ])
                ->pluck('amount', 'date');

// Merge the two collections; any results in `$posts` will overwrite the zero-value in `$dates`
        $dateRefund = $dateRefund->merge($refunds);
        $dateReceipt = $dateReceipt->merge($receipts);
        $dateRevenue = $dateRevenue->merge($revenue);

        $statics['refund_days_graph'] = implode(",", $dateRefund->toArray());
        $statics['receipt_days_graph'] = implode(",", $dateReceipt->toArray());
        $statics['revenue_days_graph'] = implode(",", $dateRevenue->toArray());


        //CAlculate revenue %
        $getRevenueLast10Days = \App\Booking::where('status', '=', 1)
                ->select(DB::raw('SUM(commission_amount) as adminCommission'))
                ->whereDate('booking_date', '>=', date('Y-m-d H:i:s', strtotime('-10 days')))
                ->first();


        $getRevenuePrev10Days = \App\Booking::where('status', '=', 1)
                ->select(DB::raw('SUM(commission_amount) as adminCommission'))
                ->whereDate('booking_date', '>=', date('Y-m-d H:i:s', strtotime('-20 days')))
                ->whereDate('booking_date', '<=', date('Y-m-d H:i:s', strtotime('-10 days')))
                ->first();

        $statics['revenue_persentage'] = 0;
        if ($getRevenuePrev10Days->adminCommission > 0) {
            $statics['revenue_persentage'] = number_format((float) (($getRevenueLast10Days->adminCommission - $getRevenuePrev10Days->adminCommission) / $getRevenuePrev10Days->adminCommission) * 100, 1, '.', '');
        }
        $statics['revenue_prev10days'] = $getRevenuePrev10Days->adminCommission;
        
        
        //CAlculate Refund %
        $getRefundLast10Days = \App\Refund::select(DB::raw('SUM(amount) as refund_amount'))
                ->whereDate('refund_date', '>=', date('Y-m-d H:i:s', strtotime('-10 days')))
                ->first();

        $getRefundPrev10Days = \App\Refund::select(DB::raw('SUM(amount) as refund_amount'))
                ->whereDate('refund_date', '>=', date('Y-m-d H:i:s', strtotime('-20 days')))
                ->whereDate('refund_date', '<=', date('Y-m-d H:i:s', strtotime('-10 days')))
                ->first();
        
        $statics['refund_persentage'] = 0;
        if ($getRefundPrev10Days->refund_amount > 0) {
            $statics['refund_persentage'] = number_format((float) (($getRefundLast10Days->refund_amount - $getRefundPrev10Days->refund_amount) / $getRefundPrev10Days->refund_amount) * 100, 1, '.', '');
        }
        $statics['refund_prev10days'] = $getRefundPrev10Days->refund_amount;
        
        
        //prd($statics);
        $statics['total_refund'] = $_totalRefund->amount;
        $statics['total_receipt'] = $_totalReceipt->amount;
        $statics['total_commission'] = $_totalRevenue->adminCommission;
        //prd($statics);
        // Receipt and Refund MORRIS CHART CODE
        $startDate = new Carbon('first day of this month');
        $endDate = new Carbon('last day of this month');
        // Period over which we want user registration data
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
        // Convert the period to an array of dates
        // $dates = $period->toArray();
        $graphData = [];

        foreach ($period as $key => $date) {
            $graphData[$key]['y'] = $date->format('Y-m-d');
            //DB::enableQueryLog();
            $_g_revenue = \App\Booking::whereDate('created_at', $date)
                            ->where('status', '=', 1)
                            ->get([
                                DB::raw('SUM( commission_amount ) as "amount"')
                            ])->first();
            $revenueSum = (!empty($_g_revenue->amount)) ? $_g_revenue->amount : 0;

            $_g_refund = \App\Refund::whereDate('created_at', $date)
                            ->get([
                                DB::raw('SUM( amount ) as "amount"')
                            ])->first();
            $refundSum = (!empty($_g_refund->amount)) ? $_g_refund->amount : 0;

            // prd(DB::getQueryLog());
            $graphData[$key]['revenue'] = number_format((float) $revenueSum, 2, '.', '');
            $graphData[$key]['refund'] = number_format((float) $refundSum, 2, '.', '');
        }
        //prd($graphData);
        $rrGraph = json_encode($graphData);
        //Receipt and Refund List
        $receipts = \App\Receipt::latest()
                ->with(['shop', 'customer'])
                ->get();

        $refunds = \App\Refund::latest()
                ->with(['shop', 'customer'])
                ->get();

        $revenue = \App\Booking::latest()
                ->with(['shop'])
                ->where('status', '=', 1)
                ->where('unique_id', '!=', null)
                //->groupBy('shop_id')
                ->select([
                    'shop_id',
                    'commission_type',
                    DB::raw('( final_amount ) as "final_amount"'),
                    DB::raw('( commission_amount ) as "admin_commission"')
                ])
                ->get();
        //prd($revenue);
        return view('admin.financial.index', compact('receipts', 'refunds', 'revenue', 'statics', 'rrGraph'));
    }

    public function getReceipt(Request $request) {
        $receiptData = \App\Receipt::where('id', '=', $request->id)->with(['shop', 'customer', 'booking'])->first();
        $receiptData->date = date('d/m/Y', strtotime($receiptData->receipt_date));
        $receiptData->time = date('h:i A', strtotime($receiptData->receipt_date));
        $receiptData->final_amount = $receiptData->final_amount . " (SAR)";
        $receiptData->payment_method = ($receiptData->payment_method == 1) ? __("messages.cash") : __("messages.card");
        $receiptData->booking_id = $receiptData->booking->unique_id;
        return response()->json(['data' => $receiptData]);
    }

    public function getRefund(Request $request) {
        $receiptData = \App\Refund::where('id', '=', $request->id)->with(['shop', 'customer', 'booking'])->first();
        $receiptData->date = date('d/m/Y', strtotime($receiptData->refund_date));
        $receiptData->time = date('h:i A', strtotime($receiptData->refund_date));
        $receiptData->amount = $receiptData->amount . " (SAR)";
        $receiptData->booking_id = $receiptData->booking->unique_id;
        return response()->json(['data' => $receiptData]);
    }

    // AJAX FUNCTION TO FETCH Refund and revenue DATA FOR MORRIS CHART
    public function getrrChartData(Request $request) {
        $duration = $request->duration;
        //prd($request->all());
        // Convert the period to an array of dates
        // $dates = $period->toArray();
        $graphData = [];

        if ($duration == 'lastMonth') {    // fetch data for the last month 
            $startDate = new Carbon('first day of last month');
            $endDate = new Carbon('last day of last month');
            // Period over which we want user registration data
            $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
            // Convert the period to an array of dates
            // $dates = $period->toArray();
            $graphData = [];

            foreach ($period as $key => $date) {
                $graphData[$key]['y'] = $date->format('Y-m-d');
                //DB::enableQueryLog();
                $_g_revenue = \App\Booking::whereDate('created_at', $date)
                                ->where('status', '=', 1)
                                ->get([
                                    DB::raw('SUM( commission_amount ) as "amount"')
                                ])->first();
                $revenueSum = (!empty($_g_revenue->amount)) ? $_g_revenue->amount : 0;

                $_g_refund = \App\Refund::whereDate('created_at', $date)
                                ->get([
                                    DB::raw('SUM( amount ) as "amount"')
                                ])->first();
                $refundSum = (!empty($_g_refund->amount)) ? $_g_refund->amount : 0;


                $graphData[$key]['revenue'] = number_format((float) $revenueSum, 2, '.', '');
                $graphData[$key]['refund'] = number_format((float) $refundSum, 2, '.', '');
            }
        } else if ($duration == 'last6Months') {    // fetch data for the last 6 months month 
            $now = Carbon::now();
            $nowString = $now->format('Y-m-d');
            $sixMonthAgo = $now->subMonths(6);
            $sixMonthAgoString = $sixMonthAgo->format('Y-m-d');

            $period = \Carbon\CarbonPeriod::create($sixMonthAgoString, '1 month', $nowString);

            foreach ($period as $key => $date) {
                $graphData[$key]['y'] = $date->format('Y-m-d');
                //DB::enableQueryLog();
                $_g_revenue = \App\Booking::whereMonth('created_at', $date->format('m'))
                                ->whereYear('created_at', $date->format('Y'))
                                ->where('status', '=', 1)
                                ->get([
                                    DB::raw('SUM( commission_amount ) as "amount"')
                                ])->first();
                $revenueSum = (!empty($_g_revenue->amount)) ? $_g_revenue->amount : 0;

                $_g_refund = \App\Refund::whereMonth('created_at', $date->format('m'))
                                ->whereYear('created_at', $date->format('Y'))
                                ->get([
                                    DB::raw('SUM( amount ) as "amount"')
                                ])->first();
                $refundSum = (!empty($_g_refund->amount)) ? $_g_refund->amount : 0;

                $graphData[$key]['revenue'] = number_format((float) $revenueSum, 2, '.', '');
                $graphData[$key]['refund'] = number_format((float) $refundSum, 2, '.', '');
            }
        } else if ($duration == 'last1Year') {    // fetch data for the last year 
            $now = Carbon::now();
            $nowString = $now->format('Y-m-d');
            $oneYearAgo = $now->subYear();
            $oneYearAgoString = $oneYearAgo->format('Y-m-d');

            $period = \Carbon\CarbonPeriod::create($oneYearAgoString, '1 month', $nowString);

            foreach ($period as $key => $date) {
                $graphData[$key]['y'] = $date->format('Y-m-d');
                //DB::enableQueryLog();
                $_g_revenue = \App\Booking::whereMonth('created_at', $date->format('m'))
                                ->whereYear('created_at', $date->format('Y'))
                                ->where('status', '=', 1)
                                ->get([
                                    DB::raw('SUM( commission_amount ) as "amount"')
                                ])->first();
                $revenueSum = (!empty($_g_revenue->amount)) ? $_g_revenue->amount : 0;

                $_g_refund = \App\Refund::whereMonth('created_at', $date->format('m'))
                                ->whereYear('created_at', $date->format('Y'))
                                ->get([
                                    DB::raw('SUM( amount ) as "amount"')
                                ])->first();
                $refundSum = (!empty($_g_refund->amount)) ? $_g_refund->amount : 0;

                // prd(DB::getQueryLog());
                $graphData[$key]['revenue'] = number_format((float) $revenueSum, 2, '.', '');
                $graphData[$key]['refund'] = number_format((float) $refundSum, 2, '.', '');
            }
        } else if ($duration == 'currentMonth') {    // fetch data for the current month 
            $startDate = new Carbon('first day of this month');
            $endDate = new Carbon('last day of this month');
            // Period over which we want user registration data
            $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
            // Convert the period to an array of dates
            // $dates = $period->toArray();
            $graphData = [];

            foreach ($period as $key => $date) {
                $graphData[$key]['y'] = $date->format('Y-m-d');
                //DB::enableQueryLog();
                $_g_revenue = \App\Booking::whereDate('created_at', $date)
                                ->where('status', '=', 1)
                                ->get([
                                    DB::raw('SUM( commission_amount ) as "amount"')
                                ])->first();
                $revenueSum = (!empty($_g_revenue->amount)) ? $_g_revenue->amount : 0;

                $_g_refund = \App\Refund::whereDate('created_at', $date)
                                ->get([
                                    DB::raw('SUM( amount ) as "amount"')
                                ])->first();
                $refundSum = (!empty($_g_refund->amount)) ? $_g_refund->amount : 0;

                // prd(DB::getQueryLog());
                $graphData[$key]['revenue'] = number_format((float) $revenueSum, 2, '.', '');
                $graphData[$key]['refund'] = number_format((float) $refundSum, 2, '.', '');
            }
        }

        return json_encode($graphData);
    }

}
