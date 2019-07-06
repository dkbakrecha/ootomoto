@component('mail::message')
Hello {{ $userData->name }},

Your account has been flagged by System due to cancel too many bookings. Please contact {{ config('app.name') }} team for more details.<br>

Regards,<br>
Team {{ config('app.name') }}
@endcomponent