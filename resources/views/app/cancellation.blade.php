@extends('layouts.baiscapp')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <p><strong>Modification:</strong> Customers can modify a booking through the app. Customers may change the time of the booking up to two times per booking.</p>
            <h5>Cancellation Policy:</h5>
            <ol type="A">
                <li><strong>booking without prior payment:</strong> A customer is able to cancel a booking before the booking start time. For cancelation of bookings occurring after the booking start time, the booking will be marked as “No-Show”.</li>
                <li><strong>booking with prior payment:</strong> A customer is able to cancel a booking before the booking start time and the full amount will be refunded to the customer. For cancelation of bookings occurring after the booking start time, the booking will be marked as “No-Show” and only 70% of the payment will be refunded.</li>
            </ol>
            <p><strong>No-show:</strong> In case of three “No show” events, Flair has the right to suspend the customer’s account. An email will be sent to the customer about the account suspension. The booking will be marked as “No show” in the following situations:
                when a customer does not show up to the Service Provider within 10 minutes of the booking start time. 
                When a customer cancel the booking after the start time.</p>


        </div>
    </div>
</div>
@endsection