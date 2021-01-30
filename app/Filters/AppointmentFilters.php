<?php
namespace App\Filters;

use App\Enums\AppointmentStatus;

class AppointmentFilters extends QueryFilters
{   
    public function time($day)
    {
        return $this->builder->where('time','<=',$day)->where('status', AppointmentStatus::APPROVED);
    }
}    