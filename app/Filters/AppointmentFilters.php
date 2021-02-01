<?php
namespace App\Filters;

use Illuminate\Http\Request;

class AppointmentFilters extends QueryFilters
{    
    public function status ($value)
    {
        return $this->builder->where('status', $value);
    }
} 