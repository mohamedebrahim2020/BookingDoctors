<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Doctor;
use Illuminate\Auth\Access\HandlesAuthorization;

class DoctorPolicy
{
    use HandlesAuthorization;

    public function addWorkingDay(Doctor $doctor)
    {
        return $doctor->activated_at != null ;
    }
}
