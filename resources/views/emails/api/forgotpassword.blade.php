@component('mail::message')
# Hello!

It looks like you forgot your password when trying to login to Flair app.<br>
Would you like to reset your password?<br>

Reset password verification code : {{ $token }}<br>

If not, you can just ignore this email.<br>

Regards,<br>
{{ config('app.name') }}
@endcomponent