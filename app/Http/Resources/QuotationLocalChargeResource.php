<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Traits\UtilTrait;

class QuotationLocalChargeResource extends JsonResource
{

    use UtilTrait;

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
            'charge_id' => $this->surcharge_id ?? null,
            'charge_options' => $this->surcharge->options ?? null,
            'calculation_type' => $this->calculation_type->name ?? null,
            'calculation_type_code' => $this->calculation_type->unique_code ?? null,
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
        ];

    }
}
