<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Traits\UtilTrait;
use App\Http\Resources\CarrierResource;
use App\PivotLocalChargeQuote;
use App\AutomaticRate;

class QuotationChargeResource extends JsonResource
{
    use UtilTrait;
    
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected $segment_id;

    public function toArray($request)
    {
        return $this->resource->map(function($item){
            $pivot_local_charge = PivotLocalChargeQuote::where(['charge_id' => $item->id, 'quote_id' => $item->quote_id])->count();
            return [
                'id' => $item->id,
                'segment_id' => $this->segment_id ?? null,
                'type' => $item->type->description,
                'charge' => $item->surcharge->name ?? 'Ocean Freight',
                'charge_id' => $item->surcharge_id ?? null,
                'charge_options' => $item->surcharge->options ?? null,
                'calculation_type' => $item->calculation_type->name ?? null,
                'calculation_type_code' => $item->calculation_type->unique_code ?? null,
                'port' => $this->getType($item->automatic_rate, $item->type_id),
                'price' => $this->arrayMapToFloat($item->price) ?? [],
                'profit' => $this->arrayMapToFloat($item->profit) ?? [],
                'total' => $item->profit ? $this->setTotal($item->price, $item->profit):$this->arrayMapToFloat($item->price),
                'units' => $item->units ?? null,
                'currency' => $item->currency->alphacode ?? null,
                'provider' => (new CarrierResource($item->automatic_rate->carrier ?? null))->companyUser($item->automatic_rate->quote->company_user ?? null),
                'added' => $pivot_local_charge>0 ? true:false,
                'sale_code_id' => $item->charge_sale_code_quote['sale_term_code_id'] ?? null,
            ];
        });
    }
    
    /**
     * getType
     *
     * @param  mixed $type
     * @return void
     */
    public function getType($automatic_rate, $type){
        if($type == 1){
            return $automatic_rate->origin_port->display_name;
        }elseif($type == 2){
            return $automatic_rate->destination_port->display_name;
        }else{
            return null;
        }
    }
}
