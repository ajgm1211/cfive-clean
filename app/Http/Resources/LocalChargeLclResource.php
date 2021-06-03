<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LocalChargeLclResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if ($this->localcharportslcl->count() > 0) {
            $ports = $this->localcharportslcl;
            $origin = $ports->pluck('portOrig')->unique('id')->values();
            $destination = $ports->pluck('portDest')->unique('id')->values();
            $route = ['id' => 'port', 'name' => 'Port', 'vselected' => 'harbors'];
        } else {
            $countries = $this->localcharcountrieslcl;

            $origin = $countries->pluck('countryOrig')->unique('id')->values()->map(function ($country) {
                $country['display_name'] = $country['name'];

                return $country->only(['id', 'display_name', 'name']);
            });

            $destination = $countries->pluck('countryDest')->unique('id')->values()->map(function ($country) {
                $country['display_name'] = $country['name'];

                return $country->only(['id', 'display_name', 'name']);
            });

            $route = ['id' => 'country', 'name' => 'Country', 'vselected' => 'countries'];
        }

        return [
            'id' => $this->id,
            'typeofroute' => $route,
            'surcharge' => $this->surcharge,
            'origin' => $origin,
            'destination' => $destination,
            'destination_type' => $this->typedestiny,
            'carriers' => $this->localcharcarrierslcl->pluck('carrier'),
            'contract' => $this->contract,
            'currency' => $this->currency,
            'calculation_type' => $this->calculationtypelcl,
            'amount' => $this->ammount,
            'minimum' => $this->minimum,
        ];
    }
}
