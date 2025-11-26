<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class ClubResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'contactName' => $this->name,
            'email' => $this->email,
            'clubName' => $this->club_name,
            'location' => $this->location,
            'registeredAt' => $this->created_at,
        ];
    }
}