<?php

namespace App\Services;

use App\Repositories\DoctorRepository;
use Illuminate\Support\Facades\Hash;

class DoctorService extends BaseService
{
    public function __construct(DoctorRepository $repository)
    {
        $this->repository = $repository;
    }

    public function checkAuth($password, $filters)
    {
        $doctor = $this->repository->findDoctorByEmail($filters);
        (!Hash::check($password, $doctor->password) || !$doctor->activated_at) ? abort(401, 'unauthenticated') : "" ;      
    }
}    