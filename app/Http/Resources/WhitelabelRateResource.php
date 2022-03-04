<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WhitelabelRateResource extends JsonResource
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
            'owner'=>$this->contract->user ? $this->contract->user->fullname : 'Unassigned',
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
            //'schedule_type_id' => $this->schedule_type_id,
            'transit_time' => $this->transit_time,
            'via' => $this->via,
            'charges' => $this->charges,
            'charge_totals_by_type' => $this->charge_totals_by_type,
            'totals' => $this->totals,
            'totals_freight_currency' => $this->totals_freight_currency,
            'client_currency' => $this->client_currency,
            'totals_with_markups' => isset($this->totals_with_markups) ? $this->totals_with_markups : null,
            'totals_with_markups_freight_currency' => isset($this->totals_with_markups_freight_currency) ? $this->totals_with_markups_freight_currency : null,
            'containers_with_markups' => isset($this->containers_with_markups) ? $this->containers_with_markups : null,
            'search' => $this->search,
            'price_level' => isset($this->price_level) ? $this->price_level : null,
            'remarks' => isset($this->remarks) ? $this->remarks : null,
            'global_total' => strval($this->global_total),
            'quantity_totals' => $this->quantity_totals,
            // 'contract_backup_id' => $this->contractBackupId,
            // 'contract_request_id' => $this->contractRequestId,
            // 'contract_id' => $this->contractId
            // 'options' => $this->options,

        ];    
    }
}
