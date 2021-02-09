<?php

namespace App\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ShowDoctorResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'specialization' => $this->specialization->name,
            'photo' => $this->photo,
            'degree_copy' => $this->degree_copy,
            'is_active' => (bool) $this->activated_at,
            'regestired_at' => $this->created_at->timestamp,
            'working_days' => DoctorWorkingDaysResource::collection($this->WorkingDays)
        ];
    }
}
