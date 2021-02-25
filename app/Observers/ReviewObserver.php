<?php

namespace App\Observers;

use App\Jobs\PushNotification;
use App\Models\Review;
use App\Services\DoctorService;

class ReviewObserver
{
    /**
     * Handle the Review "created" event.
     *
     * @param  \App\Models\Review  $review
     * @return void
     */
    public function created(Review $review)
    {
        $doctor = $review->appointment->doctor;
        app(DoctorService::class)->update(['average_reviews' => $doctor->reviews->avg('rank')]);
        if ($doctor->firebaseTokens()->count() > 0) {
            $tokens = $doctor->firebaseTokens()->pluck('token')->toArray();
            $title = 'review is created'; 
            $body = 'you have an review  from' . $review->appointment->patient->name;
            PushNotification::dispatch($tokens, $title, $body)->afterResponse();
        }
    }
}