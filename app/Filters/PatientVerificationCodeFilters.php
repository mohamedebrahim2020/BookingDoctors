<?php
namespace App\Filters;

use Illuminate\Http\Request;

class PatientVerificationCodeFilters extends QueryFilters
{    
    public function email() 
    {
        return $this->builder->where('deleted_at', null);

    }
}  