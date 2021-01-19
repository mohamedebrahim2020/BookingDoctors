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

   public function unactivatedDoctors()
   {
       $doctors = Doctor::where('activated_at', null)->get();
       return $doctors;
   }
}   