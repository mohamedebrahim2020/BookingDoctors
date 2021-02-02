@component('mail::message')
hello {{$patient_name}}
your appointment at {{$time}} for {{$duration}} minutes
with doctor: {{$doctor_name}}
is {{$status}}
@if($reason != null)
the reason is: {{$reason}} 
@endif
Thanks,<br>
BookingDoctors Website 
@endcomponent