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
            'price' => $this->arrayToString($this->price) ?? [],
            'profit' => $this->arrayToString($this->profit) ?? [],
            'total' => $this->arrayToString($this->total) ?? [],
            /*'price' => is_a($this->resource, "App\LocalChargeQuote") ? $this->arrayToString($this->price) : $this->price,
            'profit' => is_a($this->resource, "App\LocalChargeQuote") ? $this->arrayToString($this->profit) : $this->profit,
            'total' => is_a($this->resource, "App\LocalChargeQuote") ? $this->arrayToString($this->total) : $this->total,*/
            'units' => $this->units ?? null,
            'currency' => $this->currency->alphacode ?? null,
            'provider' => $this->provider_name ?? null,
        ];

    }

    public function arrayToString($array){

        $new_array = [];

        foreach((array)$array as $key=>$item){
            $new_array[$key] = (string) $item;
        }

        return $new_array;
    }
}
