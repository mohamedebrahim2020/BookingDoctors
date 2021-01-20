<?php

namespace App\Services;

use App\Repositories\DoctorRepository;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class DoctorService extends BaseService
{
    public function __construct(DoctorRepository $repository)
    {
        $this->repository = $repository;
    }

    public function store($data)
    {
        
        
        $data['photo'] = $this->addFileToPublic($data['photo'],'/photo');
        $data['degree_copy'] = $this->addFileToPublic($data['degree_copy'],'/degree_copy');
        $doctor = $this->repository->store($data);
        return $doctor;
    }

    public function addFileToPublic($file, $folder)
    {
        $fileName   = time() . '.' . $file->getClientOriginalExtension();
        $img = Image::make($file->getRealPath());
        $randomString = Str::random(15);
        $fileNametostore = $folder . '/'. $randomString . $fileName;
        Storage::put($fileNametostore, $img);
        return $fileNametostore;
        
    }
}    