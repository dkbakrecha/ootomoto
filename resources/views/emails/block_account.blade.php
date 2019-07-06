@component('mail::message')
Hello {{ $userData->name }},

Your account has been blocked by Admin team. Please contact {{ config('app.name') }} team for more details.<br>

Regards,<br>
Team {{ config('app.name') }}
@endcomponent