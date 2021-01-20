<?php

namespace App\Repositories;

use App\Models\Doctor;

class DoctorRepository extends BaseRepository 
{
    /**
    * UserRepository constructor.
    *
    * @param User $model
    */
   public function __construct(Doctor $model)
   {
       parent::__construct($model);
   }

   public function findDoctorByEmail($email)
   {
       $doctor = Doctor::where('email', $email)->firstorfail();
       return $doctor;
   }
}   