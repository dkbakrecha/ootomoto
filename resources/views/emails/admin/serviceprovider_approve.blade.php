@component('mail::message')
Hello {{ $userData->name }},

Thanks for joining {{ config('app.name') }} family. Your service provider account has been approved by Admin.<br>

You can login to your service provider account by following login credentials.<br>
Email Address : {{ $userData->email }}<br>
Password : {{ $userData->passcode }}<br>

Regards,<br>
Team {{ config('app.name') }}
@endcomponent