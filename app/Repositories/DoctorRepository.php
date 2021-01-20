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

   public function query($request)
   {
       $filters = new DoctorFilters($request);
       $doctors = Doctor::filter($filters)->get();
       return $doctors;
   }
}   