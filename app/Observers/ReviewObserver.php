<?php

namespace App\Observers;

use App\Jobs\PushNotification;
use App\Models\Review;

class ReviewObserver
{
    /**
     * Handle the Review "updated" event.
     *
     * @param  \App\Models\Review  $review
     * @return void
     */
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
}
