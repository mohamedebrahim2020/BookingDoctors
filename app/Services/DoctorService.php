<?php

namespace App\Services;

use App\Enums\FolderName;
use App\Models\Doctor;
use App\Repositories\DoctorRepository;
use Illuminate\Support\Facades\Hash;
use App\Notifications\DoctorActivationMail;
use App\Notifications\RequestAppointmentNotification;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
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

    public function checkDoctorIsActivated($id)
    {
        $doctor = $this->show($id);
        if (!$doctor->activated_at) {
            abort(Response::HTTP_FORBIDDEN, 'doctor is not activated yet');
        }
        return $doctor;
    }

    public function recieveAppointmentRequest($doctorId,$appointmentData,$patient)
    {
        $doctor = $this->show($doctorId);
        $doctor->notify(new RequestAppointmentNotification($doctor->name, $appointmentData, $patient));
    }

    public function addWorkingDay($data)
    {
        $workingDays = $this->repository->storeWorkingDay($data);
        return $workingDays;
    }

    public function changePassword($data)
    {
        $doctor = auth()->user();
        if (!Hash::check($data['old_password'], $doctor->password)) {
            abort(Response::HTTP_UNAUTHORIZED, 'unauthenticated');
        }
        $data['password'] = $data['new_password'];
        $this->update($data, $doctor->id);
        $this->deleteOtherSessions();
        return $doctor;
    }

    public function deleteOtherSessions()
    {
        $currentTokenId = auth()->user()->token()->id;
        $tokens = auth()->user()->tokens;
        foreach ($tokens as $token) {
            if ($token->id != $currentTokenId) {
                $token->revoke();
            }
        }
    }
   
    public function Profile()
    {
        $key = 'doctor_' . auth()->user()->id;
        $doctor = Cache::remember($key, 33600, function ()  {
            return $this->show(auth()->user()->id);
        });
        return $doctor;
    }

    public function getReviews()
    {
        $doctor = $this->show(request()->doctor);
        $reviews = $doctor->reviews;
        return $reviews;
    }
}
