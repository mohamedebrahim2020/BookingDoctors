<?php

namespace App\Repositories;

use App\Models\Review;

class ReviewRepository extends BaseRepository 
{
    /**
    * PatientRepository constructor.
    *
    * @param Patient $model
    */
   public function __construct(Review $model)
   {
       parent::__construct($model);
   }
}   