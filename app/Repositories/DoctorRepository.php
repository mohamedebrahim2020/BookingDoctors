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

   public function findDoctorByEmail($filters)
   {
       $doctor = Doctor::filter($filters)->firstorfail();
       return $doctor;
   }
}   