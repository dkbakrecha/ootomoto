@component('mail::message')
Hello {{ $userData->name }}

Thank you for booking with Flair.<br>
Your booking ({{ $bookingData->unique_id }}) has been rejected by service provider.<br>

Regards,<br>
Team {{ config('app.name') }}
@endcomponent