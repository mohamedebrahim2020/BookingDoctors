<?php
namespace App\Filters;

use Carbon\Carbon;

class WorkingDayFilters extends QueryFilters
{    
    public function time($day)
    {
        
        $day = $day/1000; 
        return $this->builder->where('day', Carbon::createFromTimestamp($day)->dayOfWeek)
            ->where(function ($query) use ($day) {
                $query->where([
                    ['from', '<=', Carbon::createFromTimestamp($day)->Format('h:m A')],
                    ['from', 'like', '%' . Carbon::createFromTimestamp($day)->Format('A') . '%'],
                    ['to', '>=', Carbon::createFromTimestamp($day)->Format('h:m A')],
                    ['to', 'like', '%' . Carbon::createFromTimestamp($day)->Format('A') . '%']
                ])
                ->orWhere('is_all_day', 1);
            });
    }

}    