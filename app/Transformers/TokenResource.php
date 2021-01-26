<?php

namespace App\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class TokenResource extends JsonResource
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
            'accessToken' => $this->access_token,
            'refreshToken' => $this->refresh_token,
        ];
    }
}