<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RateLclResource extends JsonResource
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
            'port_origin' => $this->port_origin,
            'port_destiny' => $this->port_destiny, 
            'origin_port' => $this->origin_port,
            'destiny_port' => $this->destiny_port,
            'carrier_id' => $this->carrier_id,
            'carrier' => $this->carrier,
            'contractlcl_id' => $this->contractlcl_id,
            'contract' => $this->contract,
            'uom' => $this->uom,
            'units' => $this->units,
            'minimum' => $this->minimum,
            'currency_id' => $this->currency_id,
            'currency' => $this->currency,
            'schedule_type_id' => $this->schedule_type_id,
            'surcharge_id' => $this->surcharge_id,
            'calculationtype_id' => $this->calculationtype_id,
            'charges' => $this->charges,
            'charge_totals_by_type' => $this->charge_totals_by_type,
            'total' => $this->total,
            'total_freight_currency' => $this->total_freight_currency,
            'client_currency' => $this->client_currency,
            'total_markups' => isset($this->total_markups) ? $this->total_markups : null,
            'total_with_markups' => isset($this->total_with_markups) ? $this->total_with_markups : null,
            'total_with_markups_freight_currency' => isset($this->total_with_markups_freight_currency) ? $this->total_with_markups_freight_currency : null,
            'search' => $this->search,
            'remarks' => isset($this->remarks) ? $this->remarks : null,
            'contract_backup_id' => $this->contractBackupId,
            'contract_request_id' => $this->contractRequestId,
        ];
    }
}
