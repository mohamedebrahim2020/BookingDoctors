<?php

namespace App\Services;

use App\Enums\FolderName;
use App\Notifications\PatientVerificationMail;
use App\Repositories\PatientRepository;
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
        } else {
            abort(Response::HTTP_BAD_REQUEST);
        }
    }

    public function checkAuth($data)
    {
        $patient = $this->repository->findPatientByEmail($data);
        (!Hash::check($data['password'], $patient->password) || !$patient->verified_at) ? abort(Response::HTTP_UNAUTHORIZED) : "" ;
    }
}