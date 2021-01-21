<?php

namespace App\Repositories;

use App\Filters\DoctorFilters;
use App\Models\Doctor;

class DoctorRepository extends BaseRepository 
{
    /**
    * UserRepository constructor.
    *
    * @param Doctor $model
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

   public function query($data)
   {
       $filters = new DoctorFilters($data);
       $doctors = Doctor::filter($filters)->get();
       return $doctors;
   }

}   