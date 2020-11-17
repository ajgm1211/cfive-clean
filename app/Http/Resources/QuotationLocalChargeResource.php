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
            'price' => $this->price,
            'profit' => $this->profit,
            'total' => $this->total,
            'currency' => $this->currency->alphacode ?? null,
        ];

    }
}
