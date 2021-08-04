<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CarrierResource;
use App\Http\Traits\UtilTrait;

class QuotationOceanFreightResource extends JsonResource
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
            'carrier' => (new CarrierResource($this->carrier))->companyUser($this->quote->company_user),
            //'carrier' => $this->carrier,
            'charges' => count($this->charge)>0 ? QuotationOceanFreightChargeResource::collection($this->charge):QuotationOceanFreightChargeLclResource::collection($this->charge_lcl_air),
        ];
    }
}
