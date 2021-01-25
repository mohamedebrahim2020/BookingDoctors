<?php
namespace App\Filters;

use Illuminate\Http\Request;

class PatientFilters extends QueryFilters
{    
    public function email($term) 
    {
        return $this->builder->where('email', $term);

    }
}  