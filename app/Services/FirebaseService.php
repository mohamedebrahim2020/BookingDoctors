<?php

namespace App\Services;

use Kreait\Firebase\Database;
use Kreait\Firebase\Exception\Messaging\InvalidMessage;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;

use Kreait\Firebase\Messaging\Notification;

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

    public function pushNotification($tokens, $title, $body)
    {
        $messaging = app(Messaging::class);
        $imageUrl = 'http://lorempixel.com/400/200/';

        $notification = Notification::fromArray([
            'title' => $title,
            'body' => $body,
            'image' => $imageUrl,
        ]);

        $message = CloudMessage::new()->withNotification($notification)->withDefaultSounds();
        $messaging->sendMulticast($message, $tokens);
    }

    public function storeDeviceToken($data)
    {
        $token = auth()->user()->firebaseTokens()->updateOrCreate(
            ['platform' => $data['platform']],
            ['token' => $data['token']]
        );
        return $token;
    }
}