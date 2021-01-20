<?php

namespace App\Services;

use App\Notifications\DoctorActivationMail;
use App\Repositories\DoctorRepository;
use Carbon\Carbon;
use Illuminate\Http\Response;

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
        return $doctor;
    }

    public function activateDoctor($id)
    {
        $doctor = $this->repository->find($id);
        if (!$doctor->activated_at) {
            $this->repository->update(["activated_at" => Carbon::now()], $id);
            $doctor->notify(new DoctorActivationMail($doctor->name));
        } else {
            abort(Response::HTTP_BAD_REQUEST);
        }        
    }

    public function query($request)
    {
        return $this->repository->query($request);
    }
}    