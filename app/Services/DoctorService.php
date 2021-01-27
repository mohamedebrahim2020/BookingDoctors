<?php

namespace App\Services;

use App\Enums\FolderName;
use App\Repositories\DoctorRepository;
use Illuminate\Support\Facades\Hash;
use App\Notifications\DoctorActivationMail;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class DoctorService extends BaseService
{
    public function __construct(DoctorRepository $repository)
    {
        $this->repository = $repository;
    }


    public function checkAuth($data)
    {
        $doctor = $this->repository->findDoctorByEmail();
        (!Hash::check($data['password'], $doctor->password) || !$doctor->activated_at) ? abort(Response::HTTP_UNAUTHORIZED, 'unauthenticated') : "" ;
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

    public function query()
    {
        return $this->repository->query();
    }
    
    public function store($data)
    {
        $data['photo'] = $this->addFileToPublic($data['photo'], FolderName::PHOTO);
        $data['degree_copy'] = $this->addFileToPublic($data['degree_copy'], FolderName::DEGREE_COPY);
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

    public function storeAppointment($data, $doctorID)
    {
        $this->repository->fiterDoctorShifts($doctorID);
        // $this->repository->filterDoctorAppointments($doctorId);
        // $this->repository->storeAppointment($data, $doctorID);
        // $this->checkDoctorShifts($data, $doctorID);
    }

    public function checkDoctorShifts($data, $doctorID)
    {
        $day = (Carbon::parse($data['time'])->dayOfWeek);
        dd($day);
        $doctor = $this->show($doctorID); 
    }
}
