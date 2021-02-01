@component('mail::message')
    hello {{$patient_name}}
    your appointment at {{$time}} for {{$duration}} minutes
    with doctor: {{$doctor_name}}
    is {{$status}}
    Thanks,<br>
    BookingDoctors Website
@endcomponent