<?php

namespace App\Filters;

use App\Enums\AppointmentStatus;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class AppointmentFilters extends QueryFilters
{
    public function status($value = null)
    {
        if (!$value) {
            return $this->builder;
        } else {
            return $this->builder->where('status', $value);
        }
    }

    public function time($time)
    {
        $from = $time;
        $to = $time + (request()->duration * 60);
        return $this->builder->whereBetween(DB::raw('time + (duration * 60)'), [$from, $to])->where('status', AppointmentStatus::APPROVED);
    }

    public function currentAppointment()
    {
        $now = Carbon::now()->timestamp;
        return $this->builder
            ->where(function ($query) use ($now) {
                $query->where('status', '=', AppointmentStatus::APPROVED)
                    ->whereRaw("UNIX_TIMESTAMP(time) < $now")
                    ->whereRaw("(UNIX_TIMESTAMP(time) + (duration * 60)) > $now");
            });
    }
}
