<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LocalChargeResource extends JsonResource
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
            'type' => $this->surcharge,
            'origin' => $this->localcharports, 
            'destination' => $this->localcharports,
            'destination_type' => $this->typedestiny,
            'carriers' => $this->localcharcarriers->pluck('carrier'),
            'contract' => $this->contract,
            'currency' => $this->currency,
            'calculation_type' => $this->calculationtype,
            'amount' => $this->ammount
        ];
    }
}
