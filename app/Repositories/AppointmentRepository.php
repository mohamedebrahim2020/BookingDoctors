<?php

namespace App\Repositories;

use App\Enums\AppointmentStatus;
use App\Filters\AppointmentFilters;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        if (env('DB_CONNECTION') === 'sqlite') {
            $results = DB::connection('sqlite')->select('select * from appointments WHERE
            strftime("%s",time) <=  strftime("%s","now") AND status = 2');
            if ($results == []) {
                return $results;
            } else {
                return $results[0];
            }
        } else {
            $currentAppointment = auth()->user()->appointments()->filter(app(AppointmentFilters::class))->first();
            return $currentAppointment;
        }

   }

   public function getDailyAppointments()
   {
       $today = Carbon::now()->toDateString();
       $appointments = $this->model->where('status', AppointmentStatus::APPROVED)
       ->whereDate('time', $today)
       ->get();
       return $appointments;
   }
}   
