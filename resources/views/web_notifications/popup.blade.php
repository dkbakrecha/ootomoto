@php
$currentUserID = Auth::guard('web')->id();

$bookingAcceptNotificationsCount = DB::table('web_notifications')
                                ->where('notification_for', $currentUserID)
                                ->where('event_type', 1)
                                ->where('is_read', 0)
                                ->get()
                                ->count();

$reviewsReceivedNotificationsCount = DB::table('web_notifications')
                                ->where('notification_for', $currentUserID)
                                ->where('event_type', 2)
                                ->where('is_read', 0)
                                ->get()
                                ->count();
@endphp

@if ($bookingAcceptNotificationsCount)
<li>

    <a href="#">
        <i class="fa fa-star text-aqua"></i> {{ $bookingAcceptNotificationsCount }} {{ __('messages.notification_new_booking') }}
    </a>
</li>
@endif

@if ($reviewsReceivedNotificationsCount)
<li>
    <a href="#">
        <i class="fa fa-comment text-aqua"></i> {{ $reviewsReceivedNotificationsCount }} {{ __('messages.notification_new_review') }}
    </a>
</li>
@endif
