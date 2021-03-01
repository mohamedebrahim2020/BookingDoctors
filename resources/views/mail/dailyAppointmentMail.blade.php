@component('mail::message')
hello {{$patient_name}}
you have a today appointment
your appointment at {{$time}} for {{$duration}} minutes
with doctor: {{$doctor_name}}
Thanks,<br>
BookingDoctors Website 
@endcomponent