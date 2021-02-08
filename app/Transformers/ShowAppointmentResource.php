<?php

namespace App\Transformers;

use App\Enums\AppointmentStatus;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowAppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'doctor_id' => $this->doctor_id,
            'patient_id' => $this->patient_id,
            'time' => Carbon::parse($this->time/1000)->toDateTimeString(),
            'duration' => $this->duration,
            'status' => AppointmentStatus::fromValue((int) $this->status)->key,
            'cancel_or_reject_reason' => $this->cancel_reason,
        ];
    }
}
