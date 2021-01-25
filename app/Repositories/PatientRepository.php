<?php

namespace App\Repositories;

use App\Filters\PatientFilters;
use App\Models\Patient;
use Carbon\Carbon;

class PatientRepository extends BaseRepository 
{
    /**
    * PatientRepository constructor.
    *
    * @param Patient $model
    */
   public function __construct(Patient $model)
   {
       parent::__construct($model);
   }

   public function storeCode($patient, $code)
   {
       $patient->verificationCode()->create([
            'code' => $code,
            'expired_at' => Carbon::now()->addHour(),
       ]);
   }

   public function findPatientByEmail()
   {
        $patient = Patient::filter(app(PatientFilters::class))->firstorfail();
        return $patient;
   }
}   