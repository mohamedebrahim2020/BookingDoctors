<?php

namespace App\Observers;

use App\Enums\AppointmentStatus;
use App\Jobs\PushNotification;
use App\Models\Appointment;
use App\Services\DoctorService;
use App\Services\PatientService;
use Illuminate\Http\Response;

class AppointmentObserver
{
    /**
     * Handle the Appointment "created" event.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return void
     */
    public function created(Appointment $appointment)
    {
        $doctor = app(DoctorService::class)->show($appointment->doctor_id);
        if ($doctor->firebaseTokens()->count() > 0) {
            $tokens = $doctor->firebaseTokens()->pluck('token')->toArray();
            $title = 'new appointment is created'; 
            $body = 'you have an appointment request from' . $appointment->patient->name;
            PushNotification::dispatch($tokens, $title, $body)->afterResponse();
        }
    }

    /**
     * Handle the Appointment "updated" event.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return void
     */
    public function updated(Appointment $appointment)
    {
        $patient = app(PatientService::class)->show($appointment->patient_id);
        if ($patient->firebaseTokens()->count() > 0) {
            $tokens = $patient->firebaseTokens()->pluck('token')->toArray();
            $title = 'appointment status updated'; 
            $body = 'appointment status is' . AppointmentStatus::fromValue((int) $appointment->status)->key;
            PushNotification::dispatch($tokens, $title, $body)->afterResponse();
        }
    }
}
