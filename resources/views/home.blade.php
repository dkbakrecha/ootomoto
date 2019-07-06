@extends('layouts.app')

@section('content')
@include('elements.messages')

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 dashboard-panes">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#tab_1" data-toggle="tab" aria-expanded="true">
                        <div class="walking-block">
                            <h4>{{ __('messages.walk_in') }}</h4>
                            <span>{{ $todayWalkings }} {{ __('messages.today') }}</span>
                        </div>
                    </a>
                </li>
                <li class="">
                    <a href="#tab_2" data-toggle="tab" aria-expanded="false">
                        <div class="waiting-block">
                            <h4>{{ __('messages.waiting_list') }}</h4>
                            <span>{{ $waitingList->count() }} {{ __('messages.waiting') }}</span>
                        </div>
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <b>{{ __('messages.walk_in') }}</b>

                    <form action="{{ route('walking.store') }}" method="post" class="form-horizontal" id="walkingAddForm" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <div class="alert alert-danger" style="display:none"></div>

                            <div class="form-group">
                                <label for="name" class="col-sm-5 col-md-3 control-label">
                                    <b> {{ __('messages.customer_name') }}</b>
                                </label>

                                <div class="col-sm-7 col-md-9">
                                    <input id="name" type="text" placeholder="{{ __('messages.customer_name') }}" class="form-control" name="name" value="{{ old('name') }}" required autofocus>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="phone" class="col-sm-5 col-md-3 control-label"><b>{{ __('messages.phone') }}</b></label>

                                <div class="col-sm-7 col-md-9">
                                    <input id="phone" type="text" placeholder="{{ __('messages.phone') }}" class="form-control" name="phone" value="{{ old('phone') }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email" class="col-sm-5 col-md-3 control-label"><b>{{ __('messages.email') }}</b></label>

                                <div class="col-sm-7 col-md-9">
                                    <input id="email" type="text" placeholder="{{ __('messages.email') }}" class="form-control" name="email" value="{{ old('email') }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="services" class="col-sm-3 control-label pull-left"> <b> {{ __('messages.services') }}</b></label>
                                <div class="col-xs-12 col-sm-12 col-md-9">
                                    @foreach($shop_services as $key => $service)
                                    <label for="service{{ $service->service_id }}" class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                                        <input id="service{{ $service->service_id }}" type="checkbox" data-price="{{$service->price}}" name="services[]" class="servicebox_tik" value="{{ $service->service_id }}"> 
                                        {{ $service->name }} <span class="badge">{{$service->price}}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="price" class="col-sm-5 col-md-3 control-label"> <b> {{ __('messages.price') }}</b></label>

                                <div class="col-sm-7 col-md-9">
                                    <input id="price" type="text" placeholder="{{ __('messages.price') }}" class="form-control" name="price" value="{{ old('price') }}" required disabled="">
                                </div>
                            </div>

                            <div class="form-group">


                                <div class="col-sm-9 col-sm-offset-3 home-send">
                                    <button type="submit" id="walkingAddSubmit" class="btn btn-primary">{{ __('messages.send') }}</button>

                                </div>
                            </div>

                        </div>  
                    </form>
                </div>
                <div class="tab-pane" id="tab_2">
                    <b>{{ __('messages.waiting_list') }}</b>

                    @php 
                    $waitingList = $waitingList->get();
                    @endphp 

                    <table id="datatable" class="table table-bordered flair-datatable">
                        <thead>
                            <tr class="table-heading">
                                <th>{{ __('messages.time_date') }}</th>
                                <th>{{ __('messages.services') }}</th>
                                <th>{{ __('messages.customer') }}</th>
                                <th>{{ __('messages.price') }} (SAR)</th>
                                <th width="180px;">{{ __('messages.details') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($waitingList as $booking)
                            <tr>
                                <td>{{ date('m/d/Y - h:i:A', strtotime($booking->booking_date)) }}</td>
                                <td>{{ show_services($booking) }}</td>
                                <td align="center"><a href="#" data-id="{{ $booking->customer->id }}" data-toggle="modal" data-target="#viewCustomerModal">{{ $booking->customer->name }}</a></td>
                                <td align="center"><a href="#" data-id="{{ $booking['id'] }}" data-toggle="modal" data-target="#viewBookingModal">{{ $booking->final_amount }}</a></td>
                                <td align="center" class="user-buttons admin-offers">

                                    <form action="{{ route('booking.approve') }}" method="POST">
                                        {{ csrf_field() }}
                                        <input type="hidden" id="booking_id" name="booking_id" value="{{ $booking['id'] }}">
                                        <button type="submit"  class="btn btn-approve pull-left" onclick="return confirm('Are you sure you want to approve selected booking?')">
                                            {{ __('messages.approve') }}
                                        </button>
                                    </form>

                                    <form action="{{ route('booking.reject') }}" method="POST">
                                        {{ csrf_field() }}
                                        <input type="hidden" id="booking_id" name="booking_id" value="{{ $booking['id'] }}">
                                        <button type="submit"  class="btn btn-primary" onclick="return confirm('Are you sure you want to reject selected booking?')">
                                            {{ __('messages.reject') }}
                                        </button>
                                    </form>


                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>


                </div>

            </div>
            <!-- /.tab-content -->
        </div>

        <div class="row">
            <div class="col-md-12 graph-content">
                <div class="panel panel-default">
                   <div class="row dash-graph-header">
					   <div class="col-xs-7 col-sm-8 col-md-9 col-lg-10 pull-left">
						   <div class="panel-heading">
							  <h3>  {{ __('messages.statistics') }} </h3>
						   </div>
					   </div>

                   <div class="col-xs-5 col-sm-4 col-md-3 col-lg-2 pull-right">
                        <select name="sp_statics_detail" id="sp_statics_detail" class="form-control filter pull-right" required="">
                            <option value="currentMonth" selected>{{ __('messages.current_month') }}</option>
                            <option value="lastMonth">{{ __('messages.last_month') }}</option>
                            <option value="last6Months">{{ __('messages.last_6_month') }}</option>
                            <option value="last1Year">{{ __('messages.last_year') }}</option>
                        </select>
				   </div>
				</div>
                    <div class="panel-body">
						<div class="chart tab-pane active" id="booking-chart" style="position: relative; height: 300px;"></div>
						<div class="shapes">
							<div> 
								<div class="rectangle-one">
								</div>
								<span>Booking</span>
							</div>
							<div> 
								<div class="rectangle-two">		
								</div>
								<span>Walk-in</span>
							</div>
						</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="viewCustomerModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.customer_view') }}</h4>
            </div>
            <form action="{{ route('users.update','test') }}" method="post" class="form-horizontal" id="customerEditForm">
                {{ method_field('patch') }}
                {{ csrf_field() }}
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    @include('users.customer_view')
                </div>

            </form>
        </div>
    </div>
</div> 


<!-- Booking View Modal -->
<div class="modal fade" id="viewBookingModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="width:101%;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.booking_info') }}</h4>
            </div>
            <form action="#" method="post" class="form-horizontal" id="bookingViewForm">
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group">
                        <label for="unique_id" class="col-sm-3 control-label">{{ __('messages.booking_id') }}</label>

                        <div class="col-sm-9">
                            <input id="unique_id" type="text" placeholder="{{ __('messages.booking_id') }}" class="form-control" name="unique_id" value="{{ old('unique_id') }}" required disabled="">
                        </div>
                    </div>
                    @include('bookings.view')
                </div>
            </form>
        </div>
    </div>
</div>    
@endsection

@section('page-js-script')
<script type="text/javascript">
    $(document).ready(function () {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        config = {
            data: {!! $graphStatics !!},
            xkey: 'y',
            ykeys: ['bookings', 'walkins'],
            labels: ['Total Bookings', 'Total Walk-ins'],
            fillOpacity: 0.6,
            hideHover: 'auto',
            behaveLikeLine: true,
            resize: true,
            pointFillColors: ['#ffffff'],
            pointStrokeColors: ['#A3A1FB', '#54D8FF'],
            lineColors: ['#A3A1FB', '#54D8FF']
        };

        config.element = 'booking-chart';
        var bookingGraph = new Morris.Area(config);

        $("#sp_statics_detail").change(function () {
            var _val = $(this).val();

            jQuery.ajax({
                url: "{{ url('/viewStatistics') }}",
                method: 'POST',
                data: {duration: _val},
                success: function (result) {
                    bookingGraph.setData(result);
                    bookingGraph.redraw();
                }
            });

        });


        $('.servicebox_tik').on('ifChanged', function (event) {
            var totalPrice = 0;

            $('input[name="services[]"]:checked').each(function () {
                var _sprice = $(this).data('price');
                totalPrice = totalPrice + _sprice;
            });
            $("#price").val(totalPrice);
        });

        $('#viewCustomerModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var _id = button.data('id');

            var modal = $(this)
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            jQuery.ajax({
                url: "{{ url('/viewCustomer') }}",
                method: 'POST',
                data: {id: _id},
                success: function (result) {
                    modal.find('.modal-body #id').val(result.data.id);
                    modal.find('.modal-body #unique_id').val(result.data.unique_id);
                    modal.find('.modal-body #name').val(result.data.name);
                    modal.find('.modal-body #email').val(result.data.email);
                    modal.find('.modal-body #phone').val(result.data.phone);

                    modal.find('.modal-body #address').val(result.data.address);

                    modal.find('.modal-body input:radio[name=gender]').filter('[value="' + result.data.gender + '"]').iCheck('check');

                    modal.find('.modal-footer .block_customer').attr('data-id', result.data.id);
                    if (result.data.area != null) {
                        modal.find('.modal-body #area').val(result.data.area.name);
                    }



                }
            });
        });

        $('#viewBookingModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var _id = button.data('id');

            var modal = $(this);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            jQuery.ajax({
                url: "{{ route('getBooking') }}",
                method: 'post',
                data: {id: _id},
                success: function (result) {
                    //console.log(result.data);
                    modal.find('.modal-body #id').val(result.data.id);
                    modal.find('.modal-body #unique_id').val(result.data.unique_id);
                    modal.find('.modal-body #username').val(result.data.customer.name);
                    modal.find('.modal-body #booking_date').val(result.data.booking_date);
                    modal.find('.modal-body #booking_time').val(result.data.booking_starttime);
                    modal.find('.modal-body #service_provider').val(result.data.shop.name);
                    modal.find('.modal-body #payment').val(result.data.final_amount);
                    modal.find('.modal-body #services').val(result.services);
                    modal.find('.modal-body #staff').val(result.barber);

                    modal.find('.modal-body #payment_method').val(result.data.payment_method);
                    modal.find('.modal-body .label-primary').removeClass().addClass("label label-primary " + result.data.booking_class);
                    modal.find('.modal-body .label-primary').html(result.data.booking_status);


                }
            });
        });
    });
</script>
@endsection