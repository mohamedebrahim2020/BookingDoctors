<?php

namespace App\Observers;

use App\Jobs\PushNotification;
use App\Models\Review;
use App\Services\DoctorService;

class ReviewObserver
{
    public function updated(Review $review)
    {
        $patient = $review->appointment->patient;
        if ($patient->firebaseTokens()->count() > 0) {
            $tokens = $patient->firebaseTokens()->pluck('token')->toArray();
            $title = ' doctor respond to review'; 
            $body = 'you have a respond  from doctor:' . $review->appointment->doctor->name;
            PushNotification::dispatch($tokens, $title, $body)->afterResponse();
        }
    }

    public function created(Review $review)
    {
        $doctor = $review->appointment->doctor;
        app(DoctorService::class)->update(['average_reviews' => $doctor->reviews->avg('rank')], $doctor->id);
        if ($doctor->firebaseTokens()->count() > 0) {
            $tokens = $doctor->firebaseTokens()->pluck('token')->toArray();
            $title = 'review is created'; 
            $body = 'you have an review  from' . $review->appointment->patient->name;
            PushNotification::dispatch($tokens, $title, $body)->afterResponse();
        }
    }
}
