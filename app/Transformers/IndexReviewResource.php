<?php

namespace App\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class IndexReviewResource extends JsonResource
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
            'rank' => $this->rank,
            'comment' => $this->comment,
            'respond' => $this->email,
            'created_at' => $this->created_at,
            'doctor' => [
                'id' => $this->appointment->doctor->id,
                'name' => $this->appointment->doctor->name,
                'averall_rank' => $this->appointment->doctor->average_reviews
            ],
            'patient' => new ShowPatientResource($this->appointment->patient)
        ];    
    }
}
