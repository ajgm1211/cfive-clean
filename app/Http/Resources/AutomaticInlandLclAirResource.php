<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AutomaticInlandLclAirResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'quote_id' => $this->quote_id,
            'automatic_rate_id' => $this->rate()->first(),
            'port_id' => $this->port,
            'charge' => $this->charge,
            'provider_id' => $this->provider()->first(),
            'provider' => $this->provider,
            'contract' => $this->contract,
            'type' => $this->type,
            'validity_start' => $this->validity_start,
            'validity_end' => $this->validity_end,
            'distance' => $this->distance,
            'currency_id' => $this->currency,
            'total' => $this->total,
            'markup' => $this->markup,
        ];

        return $data;
    }
}
