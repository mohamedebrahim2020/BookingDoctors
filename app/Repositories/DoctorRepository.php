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

   public function findDoctorByEmail()
   {
       $doctor = $this->model->filter(app(DoctorFilters::class))->firstorfail();
       return $doctor;
   }

   public function query()
   {
       $doctors = $this->model->filter(app(DoctorFilters::class))->get();
       return $doctors;
   }

   public function storeWorkingDay($data)
   {
      $doctor = $this->find(auth('doctor')->user()->id);
      $doctor->workingDays()->createMany($data["working_days"]);
      return $doctor->workingDays;
   }

}   