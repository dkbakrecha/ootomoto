@component('mail::message')
Hello {{ $userData->name }},

Thank you for booking with Flair.<br>
Your booking ({{ $bookingData->unique_id }}) is now confirmed.<br>

Regards,<br>
Team {{ config('app.name') }}
@endcomponent