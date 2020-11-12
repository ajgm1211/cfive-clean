<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuotationOceanFreightResource extends JsonResource
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
            'contract' => $this->contract,
            'valid_from' => $this->valid_from,
            'valid_until' => $this->valid_until,
            'profit' => $this->profit,
            'total' => json_decode($this->total),
            'origin' => $this->origin_port->display_name ?? null,
            'destiny' => $this->destination_port->display_name ?? null,
            'transit_time' => $this->transit_time ?? null,
            'via' => $this->via ?? null,
            'carrier' => $this->carrier,
            'charges' => QuotationOceanFreightChargeResource::collection($this->charge),
        ];
    }
}