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
            'charge_id' => $this->surcharge_id ?? null,
            'calculation_type' => $this->calculation_type->name ?? null,
            'price' => $this->arrayToFloat($this->price),
            //'profit' => $this->profit,
            'currency' => $this->currency->alphacode ?? null
        ];
    }

    public function arrayToFloat($array){

        $new_array = [];

        foreach((array)$array as $key=>$item){
            $new_array[$key] = (float) $item;
        }

        return $new_array;
    }
}
