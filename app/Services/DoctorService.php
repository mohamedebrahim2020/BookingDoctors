<?php

namespace App\Services;

use App\Notifications\DoctorActivationMail;
use Carbon\Carbon;
use Illuminate\Http\Response;
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