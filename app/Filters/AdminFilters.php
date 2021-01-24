<?php
namespace App\Filters;

use Illuminate\Http\Request;

class AdminFilters extends QueryFilters
{    
    public function email($term) 
    {
        return $this->builder->where('email', $term);
    }
  
}    