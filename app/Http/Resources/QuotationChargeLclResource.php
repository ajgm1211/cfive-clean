<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CarrierResource;
use App\PivotLocalChargeLclQuote;

class QuotationChargeLclResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $pivot_local_charge = PivotLocalChargeLclQuote::where(['charge_lcl_air_id' => $this->id, 
        'quote_id' => $this->quote_id])->count();

        return [
            'id' => $this->id,
            'type' => $this->type->description,
            'charge' => $this->surcharge->name ?? 'Ocean Freight',
            'charge_id' => $this->surcharge_id ?? null,
            'charge_options' => $this->surcharge->options ?? null,
            'calculation_type' => $this->calculation_type->name ?? null,
            'calculation_type_code' => $this->calculation_type->unique_code ?? null,
            'port' => $this->getType($this->type_id),
            'units' => $this->units ?? 0,
            'price' => $this->price ?? 0,
            'profit' => $this->profit ?? 0,
            'total' => $this->total ?? $this->setTotal(),
            'currency' => $this->currency->alphacode ?? null,
            'provider' => (new CarrierResource($this->automatic_rate->carrier ?? null))->companyUser($this->automatic_rate->quote->company_user ?? null),
            'added' => $pivot_local_charge>0 ? true:false,
            'sale_code_id' => $this->charge_sale_code_quote['sale_term_code_id'],
        ];
    }

    public function getType($type){
        if($type == 1){
            return $this->automatic_rate->origin_port->display_name;
        }elseif($type == 2){
            return $this->automatic_rate->destination_port->display_name;
        }else{
            return null;
        }
    }

    public function setTotal(){
        return ((float)$this->units*(float)$this->price) + (float)$this->profit;
    }
}
