<?php
namespace App\Filters;

use Illuminate\Http\Request;

class DoctorFilters extends QueryFilters
{
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
        parent::__construct($request);

    } 
    
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