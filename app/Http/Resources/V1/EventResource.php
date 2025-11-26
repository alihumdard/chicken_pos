<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'eventName' => $this->heading,
            'clientName' => $this->client_name,
            'clientEmail' => $this->email,
            'eventDate' => $this->date,
            'eventTime' => $this->time,
            'rate' => $this->rate,
            'address' => $this->address,
            'entryType' => $this->entry,
            'otherInfo' => $this->others,
        ];
    }
}