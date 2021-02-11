<?php

namespace App\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class DoctorWorkingDaysResource extends JsonResource
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
            'day' => $this->day,
            'from' => $this->from,
            'to' => $this->to,
        ];
    }
}
