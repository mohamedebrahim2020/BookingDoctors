<?php
namespace App\Filters;

use App\Enums\AppointmentStatus;

class AppointmentFilters extends QueryFilters
{   
    public function time($time)
    {
        $from = $time;
        $to = $time + (request()->duration * 60000);
        $checkBefore = 120 * 60000;
        return $this->builder->whereBetween('time', [$from - $checkBefore, $to])->where('status', AppointmentStatus::APPROVED);
    }
}    