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

}    