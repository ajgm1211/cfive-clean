<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuotationOceanFreightChargeResource extends JsonResource
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
            'charge' => $this->surcharge->name ?? 'Ocean Freight',
            'calculation_type' => $this->calculation_type->name ?? null,
            'price' => $this->price,
            'profit' => $this->profit,
            'currency' => $this->currency->alphacode
        ];
    }
}
