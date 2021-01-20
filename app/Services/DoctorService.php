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

    public function checkAuth($data)
    {
        $doctor = $this->repository->findDoctorByEmail($data['username']);
        (!Hash::check($data['password'], $doctor->password) || !$doctor->activated_at) ? abort(401, 'unauthenticated') : "" ;      
    }
}    