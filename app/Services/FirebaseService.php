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
        $deviceTokens = ['dDUkcN2Q2vwxBRPAlAquWK:APA91bHKpQ5Op_o4r9AM9Q_Jd0CL75ijk_9sVles7tQ68Q42Xav9X1Stsnt2UxfjcmNuJXI3r4N8BHtw8iqhaf3Bh1Ry1QwcokPGyh-tdgUKf46hPykyKUBolbzcTxo89IwA1RVrpzYP', 'jfjfjjllldkekoeoeoeoeoeoeooeoeoooooooooooooldll'];
        $title = 'My Notification Title';
        $body = 'My Notification Body' . "\u{1F603}";
        $imageUrl = 'http://lorempixel.com/400/200/';

        $notification = Notification::fromArray([
            'title' => $title,
            'body' => $body,
            'image' => $imageUrl,
        ]);

        $message = CloudMessage::new()->withNotification($notification)->withDefaultSounds();
        $messaging->sendMulticast($message, $deviceTokens);
    }
}