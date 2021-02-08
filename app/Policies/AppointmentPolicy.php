<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppointmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Doctor  $doctor
     * @param  \App\Models\Appointment  $appointment
     * @return mixed
     */
    public function view($user, Appointment $appointment)
    {
        return $appointment->doctor_id == $user->id || $appointment->patient_id == $user->id  ;        
    }

}
