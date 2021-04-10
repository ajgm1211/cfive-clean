<?php

namespace App\Http\Resources;

use App\Container;
use Illuminate\Http\Resources\Json\JsonResource;

class QuotationChargeResource extends JsonResource
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
            'price' => $this->price ?? [],
            'profit' => $this->profit ?? [],
            'total' => $this->total_price ?? [],
            'units' => $this->units ?? null,
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
