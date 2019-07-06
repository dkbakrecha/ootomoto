@extends('admin.layouts.app')

@section('sectionTitle', __('messages.dashboard'))

@section('content')
<section class="content-header">
    <h1>
        {{ __('messages.overview') }}
    </h1>
    @if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
    @endif

    <?php /* <p>Welcome Mr./Mst : <strong>{{ Auth::user()->name}}</strong></p>
      <p>Your joined  : {{ Auth::user()->created_at->diffForHumans() }} </p>
      <p>Language : <strong>{{ Auth::user()->is_arabic }}</strong></p>
     */ ?>
</section>

<div class="row">
    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="info-box">

            <div class="info-box-title">
                <h4>{{ __('messages.total_users') }}</h4>
            </div>
            <div class="info-box-content">
                <div class="info-block">
                    <span class="info-box-number">{{ $customer }}</span>

                    @if ($getUserPrev10Days > 0)

                    @if($user_percentage > 0)
                    <span class="info-box-description text-green">
                        <i class="fa fa-arrow-up"></i>
                        {{ $user_percentage }}%
                    </span>
                    @else
                    <span class="info-box-description text-danger">
                        <i class="fa fa-arrow-down"></i>
                        {{ $user_percentage }}%
                    </span>
                    @endif

                    @endif
                </div>


                <div class="info-box-graph">
                    <div class="sparkbar pad" data-color="#55D8FE" data-width="9">{{ $dates }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="info-box">
            <div class="info-box-title">
                <h4>{{ __('messages.booking') }}</h4>
            </div>
            <div class="info-box-content">
                <div class="info-block">
                    <span class="info-box-number">{{ $bookingTile['totalBooking'] }}</span>

                    @if ($bookingTile['Prev10Days'] > 0)

                    @if($bookingTile['bookingPresentage'] > 0)
                    <span class="info-box-description text-green">
                        <i class="fa fa-arrow-up"></i>
                        {{ $bookingTile['bookingPresentage'] }}%
                    </span>
                    @else
                    <span class="info-box-description text-danger">
                        <i class="fa fa-arrow-down"></i>
                        {{ $bookingTile['bookingPresentage'] }}%
                    </span>
                    @endif

                    @else
                    <span class="info-box-description text-green">
                        {{ $bookingTile['bookingPresentage'] }}%
                    </span>
                    @endif
                </div>


                <div class="info-box-graph">
                    <div class="sparkbar pad" data-color="#A3A0FB" data-width="9">{{ $bookingTile['bookingGraph'] }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="info-box">
            <div class="info-box-title">
                <h4>{{ __('messages.total_earnings') }}</h4>
            </div>
            <div class="info-box-content">
                <div class="info-block">
                    <span class="info-box-number">{{ $bookingTile['totalEarning'] }}</span>
                    <?php //pr($bookingTile); ?>
                    @if ($bookingTile['EarningPrev10Days'] > 0)

                    @if($bookingTile['earningPresentage'] > 0)
                    <span class="info-box-description text-green">
                        <i class="fa fa-arrow-up"></i>
                        {{ $bookingTile['earningPresentage'] }}%
                    </span>
                    @else
                    <span class="info-box-description text-danger">
                        <i class="fa fa-arrow-down"></i>
                        {{ $bookingTile['earningPresentage'] }}%
                    </span>
                    @endif

                    @else
                    <span class="info-box-description text-green">
                        0
                    </span>

                    @endif
                </div>


                <div class="info-box-graph">
                    <div class="sparkbar pad" data-color="#3CC480" data-width="9">{{ $bookingTile['earningGraph'] }}
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row">
    <div class="col-md-12 graph-content">
        <div class="panel panel-default">
            <div class="row dash-graph-header">
                <div class="col-xs-7 col-sm-8 col-md-9 col-lg-10 pull-left">
                    <div class="panel-heading">
                        <h3>{{ __('messages.user_statistics') }}</h3>
                    </div>
                </div>

                <div class="col-xs-5 col-sm-4 col-md-3 col-lg-2 pull-right">
                    <select name="userChartStats" id="userChartStats" class="form-control filter pull-right">
                        <option value="currentMonth">{{ __('messages.current_month') }}</option>
                        <option value="lastMonth">{{ __('messages.last_month') }}</option>
                        <option value="last6Months">{{ __('messages.last_6_month') }}</option>
                        <option value="last1Year">{{ __('messages.last_year') }}</option>
                    </select>
                </div>
            </div>

            <div class="panel-body">
                <div class="chart tab-pane active" id="user-statistics-chart" style="position: relative; height: 300px;">
                </div>
                <div class="shapes">
                    <div>
                        <div class="rectangle-two">
                        </div>
                        <span>{{ __('messages.users') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 graph-content">
        <div class="panel panel-default">
            <div class="row dash-graph-header">
                <div class="col-xs-7 col-sm-8 col-md-9 col-lg-10 pull-left">
                    <div class="panel-heading">
                        <h3> {{ __('messages.booking_statistics') }}</h3>
                    </div>
                </div>

                <div class="col-xs-5 col-sm-4 col-md-3 col-lg-2  pull-right">
                    <select name="bookingChartStats" id="bookingChartStats" class="form-control">
                        <option value="currentMonth">{{ __('messages.current_month') }}</option>
                        <option value="lastMonth">{{ __('messages.last_month') }}</option>
                        <option value="last6Months">{{ __('messages.last_6_month') }}</option>
                        <option value="last1Year">{{ __('messages.last_year') }}</option>
                    </select>
                </div>
            </div>
            <div class="panel-body">
                <div class="chart tab-pane active" id="bookings-statistics-chart" style="position: relative; height: 300px;">
                </div>
                <div class="shapes">
                    <div>
                        <div class="rectangle-one">
                        </div>
                        <span>{{ __('messages.bookings') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row report-tables dash-tables">
        <div class="col-md-7 col-lg-8 location-stats">
            <div class="panel panel-default seperator top-dash-tables">
                <div class="row line-sep dash-location">
                    <div class="col-xs-12 pull-left panel-heading">
                        <h3>{{ __('messages.location_statistics') }}</h3>
                    </div>
                </div>
                <div class="panel-body">
                    <table class="table table-bordered flair-datatable">
                        <thead>
                            <tr>
                                <th width="220px">{{ __('messages.location') }}</th>
                                <th width="100px">{{ __('messages.views') }}</th>
                                <th width="100px">{{ __('messages.admin_commission') }}</th>
                                <th width="100px">{{ __('messages.total_sale') }} SAR</th>
                            </tr>
                        </thead>
                        @foreach ($areas as $area)
                        <tr>
                            <td>{{ $area->name }}</td>
                            <td>{{ $area->bookings->count() }}</td>
                            <td>{{ $area->bookings->sum('commission_amount') }}</td>
                            <td>{{ $area->bookings->sum('final_amount') }}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-5 col-lg-4">
            <div class="panel panel-default seperator map-details">
                <div class="row line-sep">
                    <div class="col-xs-12 pull-left panel-heading">
                        <h3> {{ __('messages.details_on_map') }}</h3>
                    </div>
                </div>

                <div class="panel-body">
                    <div class="add-user dash-add-user">
                        @php
                        $colorClass = array('circle-purple','circle-green','circle-orange','circle-pink','circle-blue','circle-red');
                        @endphp

                        @foreach ($areas as $area)
                        <div>
                            <i class="fa fa-circle-o {{ $colorClass[array_rand($colorClass)] }}"></i>
                            <span class="user-actions">
                                {{ $area->name }}
                            </span>
                            <div class="pull-right user-data-number">{{ $area->bookings->sum('commission_amount') }}</div>
                        </div>

                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>


    <div class="row report-tables top-dash-tables">
        <div class="col-md-7 col-lg-8 location-stats">
            <div class="panel panel-default seperator ">
                <div class="row line-sep">
                    <div class="col-xs-7 pull-left panel-heading">
                        <h3> {{ __('messages.top_services') }}</h3>
                    </div>
                </div>
                <div class="panel-body">
                    <table class="table table-bordered flair-datatable">
                        <thead>
                            <tr>
                                <th width="450px">{{ __('messages.service') }}</th>
                                <th width="100px">{{ __('messages.sale_count') }}</th>
                                <th width="80px">{{ __('messages.total') }} (SAR)</th>
                            </tr>
                        </thead>
                        @foreach ($services as $service)
                        <tr>
                            <td>{{ $service->service->name }}</td>
                            <td align='center'>{{ $service->count }}</td>
                            <td align='center'>{{ $service->price }}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-5 col-lg-4">
            <div class="panel panel-default seperator map-details quick">
                <div class="row line-sep">
                    <div class="col-xs-7 pull-left panel-heading">
                        <h3> {{ __('messages.quick_details') }}
                        </h3>
                    </div>
                </div>
                <div class="panel-body" style="padding-left:0;">
                    <div class="add-user dash-add-user">
                        <div>
                            <img src="{{ url('images') }}/add-user.png" width="40" height="40" alt="user">
                            <span class="user-actions dash-actions">{{ __('messages.last24hours') }}
                            </span>
                        </div>
                        <div class="pull-right user-data">{{ $user24hrs }} {{ __('messages.new_customers') }}</div>
                    </div>
                    <div class="add-user dash-add-user">
                        <div>
                            <img src="{{ url('images') }}/refresh.png" width="40" height="40" alt="user">
                            <span class="user-actions dash-actions">{{ __('messages.awaiting_orders') }} </span>
                        </div>
                        <div class="pull-right user-data">
                            {{ $waitingBookings }} {{ __('messages.orders') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('page-js-script')
<script>
    $(function() {

    $.ajaxSetup({
    headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    });
    // User Registraion chart shown on page load. By default current month data is shown
    if ($("#user-statistics-chart").length != 0) {
    var userGraph = new Morris.Area({
    element: 'user-statistics-chart',
            resize: true,
            data: {!!$userData!!},
            xkey: 'y',
            ykeys: ['usersCount'],
            labels: ['Users Registered'],
            lineColors: ['#54D8FF'],
            hideHover: 'auto'
    });
    // Fetch User Registraion Data based on duration set by admin
    $('#userChartStats').change(function(e) {
    jQuery.ajax({
    url: "{{ route('admin.getUserRegistrationChartData') }}",
            method: 'post',
            dataType: "json",
            data: {
            'duration': $('#userChartStats').val()
            },
            success: function(res) {
            userGraph.setData(res);
            userGraph.redraw();
            }
    });
    });
    }


    // Bookings chart shown on page load. By default current month data is shown
    if ($("#bookings-statistics-chart").length != 0) {
    var bookingGraph = new Morris.Area({
    element: 'bookings-statistics-chart',
            resize: true,
            data: {!!$bookingData!!},
            xkey: 'y',
            ykeys: ['bookingsCount'],
            labels: ['Bookings Done'],
            lineColors: ['#A3A1FB'],
            hideHover: 'auto'
    });
    // Fetch Bookings Data based on duration set by admin
    $('#bookingChartStats').change(function(e) {
    jQuery.ajax({
    url: "{{ route('admin.getBookingsChartData') }}",
            method: 'post',
            dataType: "json",
            data: {
            'duration': $('#bookingChartStats').val()
            },
            success: function(res) {
            bookingGraph.setData(res);
            bookingGraph.redraw();
            }
    });
    });
    }

    });
</script>
@endsection