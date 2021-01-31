<?php

namespace App\Repositories;

use App\Filters\AppointmentFilters;
use App\Models\Appointment;
use Illuminate\Http\Response;

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

   public function fiterDoctorAppointments($doctor)
   {
        $approvedAppointment = $doctor->appointments()->filter(app(AppointmentFilters::class))->get();
        return $approvedAppointment;
        // if ($appointment->count() == 1) {
        //     abort(Response::HTTP_BAD_REQUEST, 'doctor has an appointment at this time');
        // }
   }

   public function storeAppointment($data, $doctor)
   {
       $data['patient_id'] = auth()->user()->id;
       $appointment = $doctor->appointments()->create($data);
       return $appointment;
   }
}   