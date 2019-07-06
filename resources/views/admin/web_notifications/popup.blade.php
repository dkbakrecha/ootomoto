@php
$adminNewRegistrationNotificationsCount = DB::table('web_notifications')
                                ->where('notification_for', 1)
                                ->where('event_type', 0)
                                ->where('is_read', 0)
                                ->get()
                                ->count();

$adminReviewsFlaggedNotificationsCount = DB::table('web_notifications')
                                ->where('notification_for', 1)
                                ->where('event_type', 3)
                                ->where('is_read', 0)
                                ->get()
                                ->count();

$adminNewOfferNotificationsCount = DB::table('web_notifications')
                                ->where('notification_for', 1)
                                ->where('event_type', 4)
                                ->where('is_read', 0)
                                ->get()
                                ->count();
@endphp

@if ($adminNewRegistrationNotificationsCount)
<li>

    <a href="#">
        <i class="fa fa-star text-aqua"></i> {{ $adminNewRegistrationNotificationsCount }} {{ __('messages.notification_new_member') }}
    </a>
</li>
@endif

@if ($adminReviewsFlaggedNotificationsCount)
<li>
    <a href="#">
        <i class="fa fa-comment text-aqua"></i> {{ $adminReviewsFlaggedNotificationsCount }} {{ __('messages.notification_review_flagged') }}
    </a>
</li>
@endif

@if ($adminNewOfferNotificationsCount)
<li>
    <a href="#">
        <i class="fa fa-comment text-aqua"></i> {{ $adminNewOfferNotificationsCount }} {{ __('messages.notification_new_offers') }}
    </a>
</li>
@endif