@extends('admin.layouts.app')
@section('sectionTitle', __('messages.booking'))

@section('content')
@include('admin.elements.general_top')
@include('admin.elements.messages')

<table class="table table-bordered flair-datatable">
    <thead>
        <tr class="table-heading">
            <th width="110px">{{ __('messages.booking_id') }}</th>
            <th width="250px">{{ __('messages.username') }}</th>
            <th>{{ __('messages.booking_info') }}</th>
            <th>{{ __('messages.payment') }} (SAR)</th>
            <th width="180px">{{ __('messages.status') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($bookings as $booking)
        <tr>
            <td>{{ $booking->unique_id }}</td>
            <td>{{ $booking->customer->name }}</td>
            <td align="center"><a href="#" data-id="{{ $booking->id }}" data-toggle="modal" data-target="#viewBookingModal">{{ __('messages.booking_info') }}</a></td>
            <td align="center">{{ $booking->final_amount }}</td>
            <td align="center" class="user-buttons">
                <?php /*  1 = complete, 2 = confirmed, 3 = pending, 4 = cancelled, 5 = rejected */ ?>
                @if($booking->status == 1)
                <span class="label label-primary booking-status-complete">{{ __('messages.complete') }}</span>
                @elseif($booking->status == 2)
                <span class="label label-primary booking-status-confirmed">{{ __('messages.confirmed') }}</span>
                @elseif($booking->status == 3)
                <span class="label label-primary booking-status-pending">{{ __('messages.pending') }}</span>
                @elseif($booking->status == 4)
                <span class="label label-primary booking-status-cancelled">{{ __('messages.cancelled') }}</span>
                @elseif($booking->status == 5)
                <span class="label label-primary booking-status-rejected">{{ __('messages.rejected') }}</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

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
    $(document).ready(function () {
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
    });
</script>
@endsection