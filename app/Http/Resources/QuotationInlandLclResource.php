<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuotationInlandLclResource extends JsonResource
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
            'type' => $this->type,
            'contract' => $this->contract,
            'distance' => $this->distance,
            'port' => $this->port->display_name ?? null,
            'address' => $this->inland_address->address ?? null,
            'provider' => $this->providers->name ?? null,
            'valid_from' => $this->valid_from,
            'valid_until' => $this->valid_until,
            'units' => $this->units,
            'price' => $this->price,
            'profit' => $this->profit,
            'total' => $this->total,
            'currency' => $this->currency->alphacode ?? null,
        ];
    }
}