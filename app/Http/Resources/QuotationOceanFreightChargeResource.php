<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Traits\UtilTrait;

class QuotationOceanFreightChargeResource extends JsonResource
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
            'charge' => $this->surcharge->name ?? 'Ocean Freight',
            'charge_id' => $this->surcharge_id ?? null,
            'calculation_type' => $this->calculation_type->name ?? null,
            'price' => $this->arrayMapToFloat($this->price),
            //'profit' => $this->profit,
            'currency' => $this->currency->alphacode ?? null
        ];
    }
}
