<?php
namespace App\Filters;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class ReviewFilters extends QueryFilters
{      
    public function byDoctor($term = null) {
        if ($term == null) {
            return $this->builder;
         } else {
            return $this->builder->whereHas('appointment', function (Builder $query) use ($term) {
                $query->where('doctor_id', $term);
            });
        }
    }

}    