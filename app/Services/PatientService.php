<?php

namespace App\Services;

use App\Enums\FolderName;
use App\Notifications\PatientVerificationMail;
use App\Repositories\PatientRepository;
use App\Repositories\PatientVerificationCodeRepository;
use App\Traits\StoreFileTrait;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class PatientService extends BaseService
{
    use StoreFileTrait;

    public function __construct(PatientRepository $repository)
    {
        $this->repository = $repository;
    }

    public function store($data)
    {
        $verificationCode = Str::random(10);
        $data['photo'] = $this->addFileToPublic($data['photo'], FolderName::PATIENT_PHOTO);
        $patient = $this->repository->store($data);
        $this->repository->storeCode($patient, $verificationCode);
        $patient->notify(new PatientVerificationMail($patient->email, $verificationCode));
        return $patient;
    }

    public function checkCode($data)
    {
        $patient = $this->repository->findPatientByEmail();
        if ($patient->verificationCode->code == $data['code'] && Carbon::now()->lessThanOrEqualTo($patient->verificationCode->expired_at)) {
            $this->repository->update(["verified_at" => Carbon::now()], $patient->id);
            $patient->verificationCode->update(['expired_at' => Carbon::now()]);
        } else {
            abort(Response::HTTP_BAD_REQUEST);
        }
    }

    public function checkAuth($data)
    {
        $patient = $this->repository->findPatientByEmail($data);
        (!Hash::check($data['password'], $patient->password) || !$patient->verified_at) ? abort(Response::HTTP_UNAUTHORIZED) : "" ;
    }

    public function storeAppointment($data, $doctorID)
    {
        $patient = $this->show(auth()->user()->id);
        $shift = app(DoctorService::class)->repository->fiterDoctorShifts($doctorID);
        $this->checkDuration($shift, $data['duration']);
        app(DoctorService::class)->repository->fiterDoctorAppointments($doctorID);
        $appointment = $this->repository->storeAppointment($data, $doctorID);
        app(DoctorService::class)->recieveAppointmentRequest($doctorID,$data,$patient->name);
        return $appointment;
    }

    public function checkDuration($shift, $duration)
    {
        if ($shift->count() == 0) {
            abort(Response::HTTP_BAD_REQUEST, 'doctor has no shift at this time');
        } else {
            $shift = $shift->toArray();
            if ($shift[0]['is_all_day'] == 1) {

                return true;
            } else {
                $shiftDuration = Carbon::parse($shift[0]["to"])->diffInMinutes(Carbon::parse($shift[0]["from"]));
                ($shiftDuration < $duration) ? abort(Response::HTTP_BAD_REQUEST, 'duration is longer than shift'):'';
            }
        } 
    }

    public function codeResend($data)
    {
        $patient = $this->repository->findPatientByEmail($data);
        $this->checkVerification($patient);
        $this->deletePatientOldCode($patient);
        $verificationCode = Str::random(10);
        $this->repository->storeCode($patient, $verificationCode);
        $patient->notify(new PatientVerificationMail($patient->email, $verificationCode));
        return $patient;
    }

    public function deletePatientOldCode($patient)
    {
        if ($patient->verified_at) {
            abort(Response::HTTP_BAD_REQUEST);
        }    
    }

    public function checkVerification($patient)
    {
        if ($patient->verificationCode) {
            $patient->verificationCode->delete();
        }    
    }
}