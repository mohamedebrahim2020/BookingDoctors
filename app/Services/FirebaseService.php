<?php

namespace App\Services;

use Kreait\Firebase\Database;
use Kreait\Firebase\Messaging\Message;

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

    public function pushNotification()
    {
        app(Message::class)->withTarget;
//         $deviceToken = '...';

// $message = CloudMessage::withTarget('token', $deviceToken)
//     ->withNotification($notification) // optional
//     ->withData($data) // optional
// ;

// $message = CloudMessage::fromArray([
//     'token' => $deviceToken,
//     'notification' => [/* Notification data as array */], // optional
//     'data' => [/* data array */], // optional
// ]);

// $messaging->send($message);
    }
}