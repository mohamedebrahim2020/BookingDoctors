<?php

namespace App\Repositories;

use App\Filters\AppointmentFilters;
use App\Filters\DoctorFilters;
use App\Filters\WorkingDayFilters;
use App\Models\Doctor;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

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
       $key = 'doctors' . request()->getQueryString();
       $doctors = Cache::remember($key, 33600, function () {
           return $this->model->filter(app(DoctorFilters::class))->get();
       });
       return $doctors;
   }

   public function fiterDoctorShifts($doctorID)
   {
        $this->model = $this->find($doctorID);
        $shift = $this->model->workingDays()->filter(app(WorkingDayFilters::class))->get();
        return $shift;  
   }

   public function storeWorkingDay($data)
   {
      $doctor = $this->find(auth('doctor')->user()->id);
      $doctor->workingDays()->createMany($data["working_days"]);
      return $doctor->workingDays;
   }

}   