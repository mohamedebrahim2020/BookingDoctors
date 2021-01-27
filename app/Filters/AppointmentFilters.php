<?php
namespace App\Filters;

use Carbon\Carbon;

class AppointmentFilters extends QueryFilters
{    
    public function time($day)
    {
        return $this->builder->where('time', Carbon::parse($day)->dayOfWeek);
    }

}    