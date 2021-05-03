<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RateResource extends JsonResource
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
            'contract_id' => $this->contract_id,
            'contract' => $this->contract,
            'containers' => $this->containers,
            'currency_id' => $this->currency_id,
            'currency' => $this->currency,
            'schedule_type_id' => $this->schedule_type_id,
            'transit_time' => $this->transit_time,
            'via' => $this->via,
            'charges' => $this->charges,
            'charge_totals_by_type' => $this->charge_totals_by_type,
            'totals' => $this->totals,
            'client_currency' => $this->client_currency,
            'totals_markups' => isset($this->totals_markups) ? $this->totals_markups : null,
            'container_markups' => isset($this->container_markups) ? $this->container_markups : null,
            'totals_with_markups' => isset($this->totals_with_markups) ? $this->totals_with_markups : null,
            'containers_with_markups' => isset($this->containers_with_markups) ? $this->containers_with_markups : null,
            'search' => $this->search,
            'price_level' => isset($this->price_level) ? $this->price_level : null,
            'remarks' => isset($this->remarks) ? $this->remarks : null,
            'contract_backup_id' => $this->contractBackupId,
            'contract_request_id' => $this->contractRequestId,
            'contract_id' => $this->contractId
        ];
    }
}
