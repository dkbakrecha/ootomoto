@extends('admin.layouts.app')

@section('content')
<section class="content-header search-header">
    <h1>
        {{ __($searchTerm) }}
    </h1>
    <hr>
</section>

<div class="row search-row">
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">{{ __('messages.transactions') }}</div>
            <div class="panel-body flow">
                @if(!empty($bookingData))

                @foreach ($bookingData as $booking)
                <div class="row info-row clearfix">
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 search-date">
                        <div>{{ date('d',strtotime($booking['booking_date'])) }}</div>
                        <div>{{ date('M',strtotime($booking['booking_date'])) }}</div>
                    </div>
                    <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7 mobile-view">
                        <div class="search-info">
                            <label>
                                {{ __('messages.booking_number') }}:
                            </label>
                            <a href="#" data-id="{{ $booking['id'] }}" data-toggle="modal" data-target="#viewBookingModal">{{ $booking['unique_id'] }}</a>

                        </div>
                        <br>
                        <div class="search-info">
                            <label>
                                {{ __('messages.customer_name') }}:
                            </label>
                            <span> &nbsp;
                                {{ $booking['customer']['name'] }}
                            </span>
                        </div>
                    </div>
                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 pull-right currency">
                        {{ $booking['final_amount'] . " SAR" }}
                    </div>
                </div>
                @endforeach 

                @else
                <div class="no-search-message">
                    <h3> {{ __('messages.search_transaction_empty') }} </h3>
                </div>
                @endif

            </div>
        </div>
    </div>

    <div class="col-md-4 right-bar">
        <div class="panel panel-default">
            <div class="panel-heading">{{ __('messages.site_users') }}</div>

            <div class="panel-body flow">
                @if(!empty($userData))
                @foreach ($userData as $user)
                <div class="row info-row">
                    <div>
                        @if ($user['user_type'] == 0)
                        <div class="search-info">
                            <label>
                                {{ __('messages.shop_name') }}:
                            </label>
                            <a href="#" data-id="{{ $user['id'] }}" data-toggle="modal" data-target="#editProviderModal">{{ $user['name'] }}</a>
                        </div>
                        <br>
                        @elseif($user['user_type'] == 2)
                        <div class="search-info">
                            <label>
                                {{ __('messages.customer') }}:
                            </label>
                            <a href="#" data-id="{{ $user['id'] }}" data-toggle="modal" data-target="#viewCustomerModal">{{ $user['name'] }}</a>
                        </div>
                        <br>
                        @else
                        <div class="search-info">
                            <label>
                                {{ __('messages.supervisor') }}:
                            </label>
                            <a href="#" data-id="{{ $user['id'] }}" data-toggle="modal" data-target="#viewSupervisorModal">{{ $user['name'] }}</a>
                        </div>
                        <br>
                        @endif
                    </div>
                    <div class="search-info search-number">
                        <label>
                            {{ __('messages.phone_number') }}:
                        </label>
                        <span>
                            {{ $user['phone'] }}
                        </span>
                    </div>
                </div>
                @endforeach 

                @else
                <div class="no-search-message">
                    <h3> {{ __('messages.search_users_empty') }} </h3>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>



<!-- Modal -->
<div class="modal fade" id="editProviderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.service_provider_info') }}</h4>
            </div>
            <form action="{{ route('provider.update','test') }}" method="post" class="form-horizontal" id="providerEditForm"  enctype="multipart/form-data">
                {{ method_field('patch') }}
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="alert alert-danger" style="display:none"></div>
                    <input type="hidden" id="id" name="id">
                    @include('admin.users.provider_info')
                </div>
            </form>
        </div>
    </div>
</div> 

<!-- Modal -->
<div class="modal fade" id="viewCustomerModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.customer_info') }}</h4>
            </div>
            <form action="" method="post" class="form-horizontal" id="">
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    @include('admin.customer.view')
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="viewSupervisorModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('messages.supervisor_view') }}</h4>
            </div>
            <form action="" method="post" class="form-horizontal" id="">
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    @include('admin.customer.view')
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
                    @include('admin.bookings.view')
                </div>
            </form>
        </div>
    </div>
</div>    

@endsection

@section('page-js-script')
<script type="text/javascript">
    //Open Edit Service Provider Model with Data
    $('#editProviderModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var _id = button.data('id')

        var modal = $(this)
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery.ajax({
            url: "{{ url('/admin/getProvider') }}",
            method: 'post',
            data: {id: _id},
            success: function (result) {
                modal.find('.modal-body #id').val(result.data.id);
                modal.find('.modal-body #unique_id').val(result.data.unique_id);
                modal.find('.modal-body #name').val(result.data.name);
                modal.find('.modal-body #area_id').val(result.data.area_id);
                modal.find('.modal-body #address').val(result.data.address);
                modal.find('.modal-body #map').val(result.data.map);
                modal.find('.modal-body #lat').val(result.data.lat);
                modal.find('.modal-body #long').val(result.data.long);
                modal.find('.modal-body #incharge_name').val(result.data.incharge_name);
                modal.find('.modal-body #email').val(result.data.email);
                modal.find('.modal-body #phone').val(result.data.phone);
                modal.find('.modal-body #owner_name').val(result.data.owner_name);
                modal.find('.modal-body #owner_phone').val(result.data.owner_phone);
                modal.find('.modal-body #crn').val(result.data.crn);
                modal.find('.modal-body #lincense').val(result.data.lincense);
                modal.find('.modal-body #comment').val(result.data.comment);
                modal.find('.modal-body .select2').val(result.service).trigger('change');
                modal.find('.modal-body #accept_payment').val(result.data.accept_payment);
                modal.find('.modal-body #commission').val(result.data.commission);
                modal.find('.modal-body #man').prop('checked', false);
                modal.find('.modal-body #women').prop('checked', false);
                modal.find('.modal-body #kid').prop('checked', false);
                
                if (result.data.man == 1) {
                    modal.find('.modal-body #man').iCheck('check');
                }
                if (result.data.women == 1) {
                    modal.find('.modal-body #women').iCheck('check');
                }
                if (result.data.kid == 1) {
                    modal.find('.modal-body #kid').iCheck('check');
                }
                
                modal.find(".modal-body input[name=auto_approve][value=" + result.data.auto_approve + "]").iCheck('check');
                modal.find(".modal-body input[name=commission_type][value=" + result.data.commission_type + "]").iCheck('check');

                modal.find('.modal-body #previewImages').html('<button type="button" class="close" id="close_imageBox" onClick="closeImgBox()"><span>×</span></button>');
                modal.find('.modal-body #previewImages').hide();
                if (result.shop_images.length == 0) {
                    modal.find('.modal-body #previewImages').append("{{ __('messages.no_images_uploaded') }}");
                    modal.find('#image_label').html("{{ __('messages.no_images_uploaded') }}");
                } else {
                    modal.find('#image_label').html("{{ __('messages.view_images') }}");
                    jQuery.each(result.shop_images, function (i, val) {
                        var source = "{!! asset('images/shop') !!}" + "/" + val.filename;
                        modal.find('.modal-body #previewImages').append('<span id="img_' + val.id + '" class="sp_image_wrap"><span class="pop"><img src="' + source + '" height="100px" width="100px"></span><i data-id="' + val.id + '" class="fa fa-trash shopimg-trash"></i></span>');
                    });
                }
            }
        });
    });

    //Open Edit Customer Model with Data
    $('#viewCustomerModal, #viewSupervisorModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var _id = button.data('id')

        var modal = $(this)
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery.ajax({
            url: "{{ url('/admin/viewCustomer') }}",
            method: 'post',
            data: {id: _id},
            success: function (result) {
                modal.find('.modal-body #id').val(result.data.id)
                modal.find('.modal-body #unique_id').val(result.data.unique_id)
                modal.find('.modal-body #name').val(result.data.name)
                modal.find('.modal-body #email').val(result.data.email)
                modal.find('.modal-body #phone').val(result.data.phone)

                modal.find('.modal-body input:radio[name=gender]').filter('[value="' + result.data.gender + '"]').iCheck('check');

                modal.find('.modal-body #gender').val(result.data.gender)
                modal.find('.modal-body #address').val(result.data.address)
                
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
            url: "{{ route('admin.getBooking') }}",
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

</script>
@endsection