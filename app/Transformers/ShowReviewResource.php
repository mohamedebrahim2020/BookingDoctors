<?php

namespace App\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ShowReviewResource extends JsonResource
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
            'rank' => $this->rank,
            'comment' => $this->comment,
            'respond' => $this->respond,
            'date' => $this->created_at,
        ];
    }
}
