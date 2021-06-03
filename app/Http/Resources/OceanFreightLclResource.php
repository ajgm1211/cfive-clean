<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OceanFreightLclResource extends JsonResource
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
            'contract' => $this->contract,
            'currency' => $this->currency,
            'uom' => $this->uom,
            'minimum' => $this->minimum,
        ];
    }
}
