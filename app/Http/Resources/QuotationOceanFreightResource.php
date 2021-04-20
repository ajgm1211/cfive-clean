<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuotationOceanFreightResource extends JsonResource
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
            'contract' => $this->contract,
            'valid_from' => $this->valid_from,
            'valid_until' => $this->valid_until,
            'profit' => $this->arrayToFloat($this->profit) ?? [],
            'total' => $this->arrayToFloat(json_decode($this->total)) ?? [],
            'currency' => $this->currency->alphacode ?? null,
            'origin' => $this->origin_port->display_name ?? null,
            'destiny' => $this->destination_port->display_name ?? null,
            'transit_time' => (int) $this->transit_time ?? null,
            'via' => $this->via ?? null,
            'carrier' => $this->carrier,
            'charges' => count($this->charge)>0 ? QuotationOceanFreightChargeResource::collection($this->charge):QuotationOceanFreightChargeLclResource::collection($this->charge_lcl_air),
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
