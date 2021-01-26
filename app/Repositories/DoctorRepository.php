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
       $workingDay = $doctor->workingDays()->create([
           'day' =>  $data['day'],
           'from' => $data['from'],
           'to' => $data['to'],
           'is_all_day' => $data['is_all_day'],
       ]);
       return $workingDay;
   }

}   