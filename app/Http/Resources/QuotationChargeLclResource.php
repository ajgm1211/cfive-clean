<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuotationChargeLclResource extends JsonResource
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
            'type' => $this->type->description,
            'charge' => $this->surcharge->name ?? 'Ocean Freight',
            'calculation_type' => $this->calculation_type->name ?? null,
            'port' => $this->getType($this->type_id),
            'units' => $this->units ?? null,
            'price' => $this->price ?? null,
            'profit' => $this->profit ?? null,
            'total' => $this->total ?? null,
            'currency' => $this->currency->alphacode ?? null,
            'provider' => $this->automatic_rate->carrier->name ?? null,
        ];
    }

    public function getType($type){
        if($type == 1){
            return $this->automatic_rate->origin_port->display_name;
        }elseif($type == 2){
            return $this->automatic_rate->destination_port->display_name;
        }else{
            return null;
        }
    }
}
