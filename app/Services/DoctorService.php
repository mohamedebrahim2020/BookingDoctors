<?php

namespace App\Services;

use App\Notifications\DoctorActivationMail;
use App\Repositories\DoctorRepository;
use Carbon\Carbon;

class DoctorService extends BaseService
{
    public function __construct(DoctorRepository $repository)
    {
        $this->repository = $repository;
    }

    public function unactivatedDoctors()
    {
        return $this->repository->unactivatedDoctors();
    }

    public function unactivatedDoctor($id)
    {
        $doctor = $this->repository->find($id);
        if (!$doctor->activated_at) {
            return $doctor;
        } else {
            abort(404);
        }
    }

    public function activateDoctor($id)
    {
        $doctor = $this->repository->find($id);
        if (!$doctor->activated_at) {
            $doctor->activated_at = Carbon::now();
            $doctor->save();
            $doctor->notify(new DoctorActivationMail($doctor->name));
        } else {
            abort(404);
        }        
    }
}    