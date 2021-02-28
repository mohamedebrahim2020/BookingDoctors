<?php
namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class ReviewFilters extends QueryFilters
{      
    public function byDoctor($term = null) {
        return $this->builder->whereHas('appointment', function (Builder $query) use ($term) {
            $query->where('doctor_id', $term);
        });
    }
}    