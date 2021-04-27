<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuotationLocalChargeResource extends JsonResource
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
            'charge' => $this->charge,
            'calculation_type' => $this->calculation_type->name ?? null,
            'port' => $this->port->display_name ?? null,
            /*'price' => $this->arrayToFloat($this->price) ?? [],
            'profit' => $this->arrayToFloat($this->profit) ?? [],
            'total' => $this->arrayToFloat($this->total) ?? [],*/
            'price' => is_a($this->resource, "App\LocalChargeQuote") ? $this->arrayToFloat($this->price) : $this->price,
            'profit' => is_a($this->resource, "App\LocalChargeQuote") ? $this->arrayToFloat($this->profit) : $this->profit,
            'total' => is_a($this->resource, "App\LocalChargeQuote") ? $this->arrayToFloat($this->total) : $this->total,
            'units' => $this->units ?? null,
            'currency' => $this->currency->alphacode ?? null,
            'provider' => $this->provider_name ?? null,
            'charge_options' => $this->surcharge->options ?? null,
            'charge_id' => $this->surcharge_id ?? null,
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
