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
       return request()->user('doctor')->appointments()->filter(app(AppointmentFilters::class))->get();
   }
} 