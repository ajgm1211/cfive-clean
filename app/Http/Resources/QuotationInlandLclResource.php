<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProvidersResource;

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
            'charge' => $this->charge,
            'distance' => $this->distance,
            'port' => $this->port->display_name ?? null,
            'address' => $this->inland_address->address ?? null,
            'provider' => new ProvidersResource($this->providers),
            'valid_from' => $this->valid_from,
            'valid_until' => $this->valid_until,
            'units' => $this->units,
            'price' => $this->total,
            'profit' => $this->profit,
            'total' => $this->total+$this->profit,
            'currency' => $this->currency->alphacode ?? null,
            'grouped_with' => $this->inland_local_group->local_charge_quote_lcl_id ?? null,
        ];
    }
}
