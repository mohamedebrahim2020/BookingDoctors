<?php
namespace App\Filters;

use App\Enums\AppointmentStatus;
use Illuminate\Support\Facades\DB;

class AppointmentFilters extends QueryFilters
{   
    public function time($time)
    {
        $from = $time;
        $to = $time + (request()->duration * 60000);
        $checkBefore = 120 * 60000;
        return $this->builder->whereBetween(DB::raw('time + (duration * 60000)'), [$from, $to])->where('status', AppointmentStatus::APPROVED);
    }
}    