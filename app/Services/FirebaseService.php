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

    public function pushNotification()
    {
        $messaging = app(Messaging::class);
        $validateOnly = true;
        $deviceTokens = ['dYrgLmSME70mk04J7xVRK8:APA91bGiWwS05eMKVtWMz2M2grQqw6bsoxEyRSgUpgqk_2aWI3YnxJqz3tVbS5R9Bfrn-fUFZGZGcKszC9s_hmymc1mpIxPRHlwNrR3ZIMoGSt1yxF17Bm9YFv2_Lm3yjzUVa360Rez9'];
        $title = 'My Notification Title';
        $body = 'My Notification Body';
        $imageUrl = 'http://lorempixel.com/400/200/';

        $notification = Notification::fromArray([
            'title' => $title,
            'body' => $body,
            'image' => $imageUrl,
        ]);

        $message = CloudMessage::new()->withNotification($notification);
        return $messaging->sendMulticast($message, $deviceTokens, $validateOnly);


    }
}