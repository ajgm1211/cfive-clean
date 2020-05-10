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
        if($ports = $this->localcharports->count() > 0){
            $ports = $this->localcharports;
            $origin = $ports->pluck('portOrig');
            $destination = $ports->pluck('portDest');
        }
        else {
            $countries = $this->localcharcountries;
            $origin = $countries->pluck('countryOrig');
            $destination = $countries->pluck('countryDest');
        }

        return [
            'id' => $this->id,
            'type' => $this->surcharge,
            'origin' => $origin, 
            'destination' => $destination,
            'destination_type' => $this->typedestiny,
            'carriers' => $this->localcharcarriers->pluck('carrier'),
            'contract' => $this->contract,
            'currency' => $this->currency,
            'calculation_type' => $this->calculationtype,
            'amount' => $this->ammount
        ];
    }
}
