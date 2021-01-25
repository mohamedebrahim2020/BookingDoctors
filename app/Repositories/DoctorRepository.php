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
       $doctor = Doctor::filter(app(DoctorFilters::class))->firstorfail();
       return $doctor;
   }

   public function query()
   {
       $doctors = Doctor::filter(app(DoctorFilters::class))->get();
       return $doctors;
   }

   public function storeWorkingDay($data, $doctor)
   {
       $doctor->verificationCode()->create([
            'code' => ,
            'expired_at' => ,
       ]);
   }

}   