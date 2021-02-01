<?php

namespace App\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class IndexAppointmentResource extends JsonResource
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
            'patient' => new ShowPatientResource($this->patient),
            'time' => $this->time,
            'duration' => $this->duration,
            'status' => $this->status,
            'reason' => $this->cancel_reason,
        ];
    }
}
