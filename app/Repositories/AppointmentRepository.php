<?php

namespace App\Repositories;

use App\Filters\AppointmentFilters;
use App\Models\Appointment;

class AppointmentRepository extends BaseRepository 
{
    /**
    * UserRepository constructor.
    *
    * @param Doctor $model
    */
   public function __construct(Appointment $model)
   {
       parent::__construct($model);
   }

   public function filterAppointmentsByStatus()
   {
       return auth()->user()->appointments()->filter(app(AppointmentFilters::class))->get();
   }
 
   public function filterDoctorAppointments($doctor)
   {
        $approvedAppointment = $doctor->appointments()->filter(app(AppointmentFilters::class))->get();
        return $approvedAppointment;
   }

   public function storeAppointment($data, $doctor)
   {
       $data['patient_id'] = auth()->user()->id;
       $appointment = $doctor->appointments()->create($data);
       return $appointment;
   }

   public function getCurrent()
   {
        $currentAppointment = auth()->user()->appointments()->filter(app(AppointmentFilters::class))->first();
        return $currentAppointment; 
   }
}   
