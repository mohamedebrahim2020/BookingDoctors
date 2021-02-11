<?php

namespace App\Observers;

use App\Models\Doctor;
use Illuminate\Support\Facades\Cache;

class DoctorObserver
{
    /**
     * Handle the Doctor "updated" event.
     *
     * @param  \App\Models\Doctor  $doctor
     * @return void
     */
    public function updated(Doctor $doctor)
    {
        $key = 'doctor_'. $doctor->id;
        if (Cache::has($key)) {
            Cache::forget($key);
        }
    }
}
