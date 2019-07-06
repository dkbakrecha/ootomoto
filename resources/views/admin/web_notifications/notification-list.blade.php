@extends('admin.layouts.app')
@section('sectionTitle', __('messages.notifications'))

@section('content')
@include('admin.elements.general_top')
@include('admin.elements.messages')

<!--
<style>
.unread {
	background-color: #cd2b421f;
}
</style>
-->

<div class="outer-notify">
        @if(!empty($notifications[0]))
    <?php //prd($notifications->toArray()); ?>
	@foreach ($notifications as $notification)
	<div class="row notification-section @if (!$notification->is_read) unread @endif" onclick="notificationRead({{$notification->id}})" id="notification_{{$notification->id}}">
		<div class="col-xs-8 col-sm-9">
			@if ($notification->event_type == 0)
			<div class="row">
				<div class="col-xs-1 col-sm-1 gutter-zero">
					<i class="fa fa-star text-aqua"></i>
				</div>
				<div class="col-xs-11 col-sm-11 gutter-zero notify-content">
					<span class="notification-uname">
						{{ $notification->user->name }}
					</span>
					<br>
					<span class="notification-event">
						{{ $notification->event }}
					</span>
				</div>
			</div>
			@endif

			@if ($notification->event_type == 3)
			<div class="row">
				<div class="col-xs-1 col-sm-1 gutter-zero">
					<i class="fa fa-comment text-aqua"></i>
				</div>

				<div class="col-xs-11 col-sm-11 gutter-zero notify-content">
					<span class="notification-uname">
						{{ $notification->user->name }}
					</span>
					<br>
					<span class="notification-event">
						{{ $notification->event }}
					</span>
				</div>
			</div>
			@endif

			@if ($notification->event_type == 4)
			<div class="row">
				<div class="col-xs-1 col-sm-1 gutter-zero">
					<i class="fa fa-comment text-aqua"></i>
				</div>

				<div class="col-xs-11 col-sm-11 gutter-zero notify-content">
					<span class="notification-uname">
						{{ $notification->user->name }}
					</span>
					<br>
					<span class="notification-event">
						{{ $notification->event }}
					</span>
				</div>
			</div>
			@endif
		</div>

		<div class="col-xs-4 col-sm-3 gutter-zero text-right">
			<div class="notification-date">

				@php
				$notificationDate = $notification->created_at;

				$formattedDate = date('d F / H:i A', strtotime($notification->created_at) );
				@endphp

				<span> {{ $formattedDate }} </span>

			</div>
		</div>
	</div>
	@endforeach
        
        @else
                <div class="no-search-message">
                    <h3> {{ __('messages.notification_empty') }} </h3>
                </div>
        @endif
</div>

@endsection

@section('page-js-script')
<script>
	$(function() {
		if (typeof window.notificationRead === 'undefined') {
			window.notificationRead = function(id) {

				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});

				var css_id = '#notification_' + id;

				jQuery.ajax({
					url: "{{ route('admin.readNotification') }}",
					method: 'post',
					// dataType: "json",
					data: {
						id: id
					},
					success: function(res) {
						$(css_id).removeClass('unread');
					}
				});
			}
		}
	});
</script>
@endsection