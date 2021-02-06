<?php

namespace App\Services;

use Kreait\Firebase\Database;

class FirebaseService 
{
    public function setAppointment($doctorId, $appointmentId)
    {
        app(Database::class)->getReference('doctor_' . $doctorId)
        ->set([
                "has_new_appointment" => true,
                "last_new_appointment_id" => $appointmentId
        ]);
    }

    public function getAppointment()
    {
        $check = app(Database::class)->getReference('doctor_' . auth()->user()->id . '/has_new_appointment')->getValue();
        if ($check) {
            $appointment = app(Database::class)->getReference('doctor_' . auth()->user()->id . '/last_new_appointment_id')->getValue();
            app(Database::class)->getReference()->update(['doctor_' . auth()->user()->id . '/has_new_appointment' =>'false']);
            return $appointment;
        }
    }
}