<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChargeLclResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'automatic_rate_id' => $this->automatic_rate()->first(),
            'type_id' => $this->type_id,
            'surcharge_id' => is_null($this->surcharge_id) ? $this->surcharge_id : $this->surcharge()->first(), 
            'calculation_type_id' => $this->calculation_type()->first(),
            'units' => $this->units,
            'minimum' => isDecimal($this->minimum,true),
            'price_per_unit' => isDecimal($this->price_per_unit,true),
            'currency_id' => $this->currency()->first(),
            'total' => isDecimal($this->total,true),
            'fixed_surcharge_id' => is_null($this->surcharge_id) ? $this->surcharge_id : $this->surcharge()->first()->name,
            'fixed_currency_id' => $this->currency()->first(),
            'fixed_calculation_type_id' => is_null($this->calculation_type) ? $this->calculation_type : $this->calculation_type()->first()->name,
            'fixed_units' => $this->units,
            'fixed_minimum' => isDecimal($this->minimum,true),
            'fixed_price_per_unit' => isDecimal($this->price_per_unit,true),
            'fixed_total' => isDecimal($this->total,true)
        ];

        return $data;
    }
}
