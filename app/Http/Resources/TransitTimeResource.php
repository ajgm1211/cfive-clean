<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Container;

class TransitTimeResource extends JsonResource
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
            'origin' => $this->origin, 
            'destination' => $this->destination,
            'carrier' => $this->carrier,
            'service' => $this->service,
            'transit_time' => $this->transit_time,
            'via' => $this->via
        ];
    }
}
