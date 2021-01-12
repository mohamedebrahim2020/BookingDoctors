@component('mail::message')
    hello {{$name}}
    'you are successfully registered as an admin
    you must use the following password to login
    password : {{$password}}
    Thanks,<br>
    BookingDoctors Website
@endcomponent