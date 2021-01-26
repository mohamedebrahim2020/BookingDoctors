@component('mail::message')
    hello {{$email}}
    'you are successfully registered as an patient
    you must use the following code to set your 
    activationCode : {{$activationCode}}
    Thanks,<br>
    BookingDoctors Website
@endcomponent