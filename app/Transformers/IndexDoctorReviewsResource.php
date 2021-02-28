<?php

namespace App\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class IndexDoctorReviewsResource extends JsonResource
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
            'average_review' => $this->average_reviews,
            'reviews' => ShowReviewResource::collection($this->reviews),
        ];
    }
}
