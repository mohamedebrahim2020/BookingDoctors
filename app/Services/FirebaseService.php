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

    public function resetAppointment()
    {
        app(Database::class)->getReference()->update(['doctor_' . auth()->user()->id . '/has_new_appointment' => 'false']);
    }
}