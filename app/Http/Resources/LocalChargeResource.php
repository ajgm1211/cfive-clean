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
            $route = [ 'id' => 'port', 'name' => 'Port', 'vselected' => 'harbors' ];
        }
        else {
            $countries = $this->localcharcountries;
            $origin = $countries->pluck('countryOrig');
            $destination = $countries->pluck('countryDest');
            $route = [ 'id' => 'country', 'name' => 'Country', 'vselected' => 'countries' ];
        }

        return [
            'id' => $this->id,
            'typeofroute' => $route,
            'surcharge' => $this->surcharge,
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