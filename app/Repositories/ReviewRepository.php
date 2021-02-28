<?php

namespace App\Repositories;

use App\Filters\ReviewFilters;
use App\Models\Review;

class ReviewRepository extends BaseRepository 
{
    /**
    * UserRepository constructor.
    *
    * @param Specialization $model
    */
   public function __construct(Review $model)
   {
       parent::__construct($model);
   }

   public function index()
   {
       $reviews = $this->model->filter(app(ReviewFilters::class))->get();
       return $reviews;
   }
   public function storeReview($data, $appointment)
   {
        $review = $appointment->review()->create($data);
        return $review;
   }
   
} 