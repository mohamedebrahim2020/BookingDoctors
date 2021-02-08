<?php

namespace App\Repositories;

use App\Filters\PatientFilters;
use App\Filters\PatientVerificationCodeFilters;
use App\Models\Patient;
use App\Models\PatientVerificationCode;
use Carbon\Carbon;

class PatientVerificationCodeRepository extends BaseRepository 
{
    /**
    * PatientRepository constructor.
    *
    * @param Patient $model
    */
   public function __construct(PatientVerificationCode $model)
   {
       parent::__construct($model);
   }

   public function fiterPatientCode($patient)
   {
        $code = $patient->verificationCode()->filter(app(PatientVerificationCodeFilters::class))->get();
        return $code;  
   }
}   