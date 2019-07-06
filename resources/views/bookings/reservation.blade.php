@extends('layouts.app')
@section('sectionTitle', __('messages.reservation'))

@section('sectionButtons')
<a href="{{ route('bookings') }}" type="button" class="btn btn-primary btn-link">
    {{ __('messages.list_view') }}
</a>
@endsection

@section('content')
@include('elements.general_top')
@include('elements.messages')

<div id="calendar"></div>



<!-- Booking View Modal -->
<div class="modal fade" id="viewBookingModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
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
        var date = new Date();
        var d = date.getDate(),
                m = date.getMonth(),
                y = date.getFullYear();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            url: "{{ url('getReservation') }}",
            method: 'get',
            success: function (result) {
                console.log(result);
                /* initialize the calendar
                 -----------------------------------------------------------------*/
                //Date for the calendar events (dummy data)
                $('#calendar').fullCalendar({
                    header: {
                        left: 'today,prev,next',
                        center: 'title',
                        right: 'month,agendaWeek,agendaDay'
                    },
                    buttonText: {
                        today: 'Today',
                        month: 'Month',
                        week: 'Week',
                        day: 'Day',
                        prev: 'Back',
                        next: 'Next'
                    },
                    //Random default events
                    //defaultView: 'agendaWeek',
                    events: result,
                    allDaySlot: false,
                    editable: false,
                    droppable: false, // this allows things to be dropped onto the calendar !!!
                    eventLimit: true,
                    slotEventOverlap:false,
                    agendaEventMinHeight : 50,
                    eventClick: function (calEvent, jsEvent, view) {
                        var _booking_id = calEvent.id;
                        var modal = $('#viewBookingModal');
                        modal.modal('show');
                        jQuery.ajax({
                            url: "{{ route('getBooking') }}",
                            method: 'post',
                            data: {id: _booking_id},
                            success: function (result) {
                                //console.log(result.data);
                                modal.find('.modal-body #id').val(result.data.id);
                                modal.find('.modal-footer #booking_id').val(result.data.id);
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
                        /*alert('Event: ' + );
                         alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);
                         alert('View: ' + view.name);*/

                        // change the border color just for fun
                        //$(this).css('border-color', 'red');

                    }
                });
            }
        });
    });
</script>
@endsection