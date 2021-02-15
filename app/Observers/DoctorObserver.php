<?php

namespace App\Observers;

use App\Models\Doctor;
use Illuminate\Support\Facades\Cache;

class DoctorObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(Doctor $doctor)
    {
        $key = 'doctor_' . $doctor->id;
        $doctor = Cache::remember($key, 33600, function () use($doctor) {
            return $doctor;
        });
    }

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
            Cache::put($key, $doctor, 33600);
        }
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(Doctor $doctor)
    {
        $key = 'doctor_'. $doctor->id;
        if (Cache::has($key)) {
            Cache::forget($key);
        }
    }
}
