<?php

namespace App\Repositories;

use App\Filters\DoctorFilters;
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

   public function findDoctorByEmail($data)
   {
       $filters = new DoctorFilters($data);
       $doctor = Doctor::filter($filters)->firstorfail();
       return $doctor;
   }
}   