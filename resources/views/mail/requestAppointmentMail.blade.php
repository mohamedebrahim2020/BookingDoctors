@component('mail::message')
    hello {{$doctor}}
    you have an appointment notification from {{$patient}} at the time {{$time}} within
    duration {{$duration}},<br>
    BookingDoctors Website
@endcomponent