<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OceanFreightResource extends JsonResource
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
            'origin' => $this->port_origin, 
            'destination' => $this->port_destiny,
            'carrier' => $this->carrier,
            'twuenty' => $this->twuenty,
            'forty' => $this->forty,
            'fortyhc' => $this->fortyhc,
            'fortynor' => $this->fortynor,
            'fortyfive' => $this->fortyfive,
            'currency' => $this->currency,
            'schedule_type' => $this->schedule_type,
            'transit_time' => $this->transit_time,
            'via' => $this->via
        ];
    }
}
