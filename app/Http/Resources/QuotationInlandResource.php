<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Traits\UtilTrait;
class QuotationInlandResource extends JsonResource
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
            'type' => $this->type,
            'contract' => $this->contract,
            'charge' => $this->charge,
            'distance' => $this->distance,
            'port' => $this->port->display_name ?? null,
            'address' => $this->inland_address->address ?? null,
            'provider' => $this->providers->name ?? null,
            'valid_from' => $this->valid_from,
            'valid_until' => $this->valid_until,
            'price' => $this->arrayToFloat($this->price),
            'profit' => $this->arrayToFloat($this->profit),
            'total' => $this->setTotal($this->price, $this->profit),
            'currency' => $this->currency->alphacode ?? null,
        ];
    }
}
