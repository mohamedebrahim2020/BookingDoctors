<?php

namespace App\Repositories;

use App\Filters\AppointmentFilters;
use App\Filters\DoctorFilters;
use App\Models\Doctor;
use Carbon\Carbon;
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
       $shifts = $this->model->workingDays()->filter(app(DoctorFilters::class))->get();
       if ($shifts->count() == 0) {
           abort(Response::HTTP_BAD_REQUEST, 'doctor has no shift at this time');
       } else {
            $shifts = $shifts->map(function ($item, $key) {
                 return([$item->from, $item->to]);
            }
            );
        }
       
   }

   public function filterDoctorAppointments($doctorID)
   {
    //    $appointements = $this->model->patients()->filter(app(AppointmentFilters::class))->get();
    //     dd($appointements);
   }

   public function storeAppointment($data, $doctorID)
   {
       
       $doctor = $this->find($doctorID);
       $appointement = $doctor->patients->toArray();
       $time = $appointement[0]["pivot"]["time"];
       $duration = $appointement[0]["pivot"]["duration"];
       $zz = Carbon::parse($time);
       $dd = $zz->addMinutes((int)$duration)->toDateTimeString();
       dd($dd);
       dd($appointement[0]["pivot"]["time"]);
    //    $doctor->patients()->attach(auth()->user()->id, $data);
   }

}   