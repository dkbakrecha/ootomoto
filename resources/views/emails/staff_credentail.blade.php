@component('mail::message')
Hello {{ $userData['name'] }},

Thanks for joining {{ config('app.name') }} family. Your supervisor account has been created.<br>

You can login to your supervisor account by following login credentials.<br>
Email Address : {{ $userData['email'] }}<br>
Password : {{ $userData['password'] }}<br>

Regards,<br>
Team {{ config('app.name') }}
@endcomponent