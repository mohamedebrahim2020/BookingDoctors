<?php
namespace App\Filters;

use Carbon\Carbon;

class DoctorFilters extends QueryFilters
{    
    public function email($term) 
    {
        return $this->builder->where('email', $term);

    }
  
    public function active($term) {
        if ($term == 1) {
            return $this->builder->where('activated_at','!=', null);
        } elseif ($term == 0) {
            return $this->builder->where('activated_at', null);
        }
    }

    public function time($day)
    {
        return $this->builder->where('day', Carbon::createFromTimestamp($day)->dayOfWeek)
        ->where([
            ['from','<=', Carbon::createFromTimestamp($day)->Format('h:m A')],
            ['to','>=', Carbon::createFromTimestamp($day)->Format('h:m A')]
        ])
        ->orWhere('is_all_day', 1);
    }

}    