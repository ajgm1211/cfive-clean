<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Traits\UtilTrait;
use App\Http\Resources\CarrierResource;
use App\PivotLocalChargeQuote;

class QuotationChargeResource extends JsonResource
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
        $pivot_local_charge = PivotLocalChargeQuote::where(['charge_id' => $this->id, 
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
            'price' => $this->arrayMapToFloat($this->price) ?? [],
            'profit' => $this->arrayMapToFloat($this->profit) ?? [],
            'total' => $this->profit ? $this->setTotal($this->price, $this->profit):$this->arrayMapToFloat($this->price),
            'units' => $this->units ?? null,
            'currency' => $this->currency->alphacode ?? null,
            'provider' => (new CarrierResource($this->automatic_rate->carrier ?? null))->companyUser($this->automatic_rate->quote->company_user ?? null),
            'added' => $pivot_local_charge>0 ? true:false,
            'sale_code_id' => $this->charge_sale_code_quote['sale_term_code_id'],
        ];
    }
    
    /**
     * getType
     *
     * @param  mixed $type
     * @return void
     */
    public function getType($type)
    {
        if ($type == 1) {
            return $this->automatic_rate->origin_port->display_name;
        } elseif ($type == 2) {
            return $this->automatic_rate->destination_port->display_name;
        } else {
            return null;
        }
    }
}
