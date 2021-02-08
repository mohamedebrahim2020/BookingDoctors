<?php

namespace App\Services;

use App\Repositories\PatientVerificationCodeRepository;
use App\Repositories\SpecializationRepository;

class PatientVerificationCodeService extends BaseService
{
    public function __construct(PatientVerificationCodeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function deletePatientOldCode($patient)
    {
        $code = $this->repository->fiterPatientCode($patient)->toArray();
        if ($code) {
           $this->delete($this->show($code[0]['id'])); 
        }
    }
}