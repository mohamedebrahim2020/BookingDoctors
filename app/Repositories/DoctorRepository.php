<?php

namespace App\Repositories;

use App\Filters\AppointmentFilters;
use App\Filters\DoctorFilters;
use App\Filters\WorkingDayFilters;
use App\Models\Doctor;
use Illuminate\Http\Response;

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

   public function fiterDoctorShifts($doctorID)
   {
        $this->model = $this->find($doctorID);
        $shift = $this->model->workingDays()->filter(app(WorkingDayFilters::class))->get();
        return $shift;  
   }

   public function fiterDoctorAppointments($doctorID)
   {
        $this->model = $this->find($doctorID);
        $appointment = $this->model->appointments()->filter(app(AppointmentFilters::class))->get();
        if ($appointment->count() == 1) {
            abort(Response::HTTP_BAD_REQUEST, 'doctor has an appointment at this time');
        }
   }
   public function storeWorkingDay($data)
   {
      $doctor = $this->find(auth('doctor')->user()->id);
      $doctor->workingDays()->createMany($data["working_days"]);
      return $doctor->workingDays;
   }

}   