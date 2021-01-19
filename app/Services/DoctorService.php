<?php

namespace App\Services;

use App\Repositories\DoctorRepository;
use Illuminate\Support\Facades\Storage;

class DoctorService extends BaseService
{
    public function __construct(DoctorRepository $repository)
    {
        $this->repository = $repository;
    }

    public function store($data)
    {
        
        $doctor = $this->repository->store($data);
        $this->addFileToPublic($data['photo'],'photo');
        $this->addFileToPublic($data['degree_copy'],'degree_copy');
        return $doctor;
    }

    public function addFileToPublic($file, $folder)
    {
        Storage::put($folder, $file);
    }
}    