<?php

namespace App\Observers;

use App\Models\Appointment;
use App\Services\DoctorService;
use App\Services\FirebaseService;

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
        $tokens = $doctor->firebaseTokens()->pluck('token')->toArray();
        app(FirebaseService::class)->pushNotification($tokens);
    }

    /**
     * Handle the Appointment "updated" event.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return void
     */
    public function updated(Appointment $appointment)
    {
        $patient = app(DoctorService::class)->show($appointment->patient_id);
        $tokens = $patient->firebaseTokens()->pluck('token')->toArray();
        app(FirebaseService::class)->pushNotification($tokens);
    }
}
