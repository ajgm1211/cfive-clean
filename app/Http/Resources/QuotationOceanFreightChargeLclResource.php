<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuotationOceanFreightChargeLclResource extends JsonResource
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
            'units' => $this->units,
            'price' => $this->price,
            'minimum' => $this->minimum ?? null,
            //'profit' => $this->profit,
            'TON/M3' => $this->total,
            'currency' => $this->currency->alphacode
        ];
    }
}
