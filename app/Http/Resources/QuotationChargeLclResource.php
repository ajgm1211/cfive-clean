<?php

namespace App\Http\Resources;

use App\Http\Traits\UtilTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CarrierResource;
use App\PivotLocalChargeLclQuote;

class QuotationChargeLclResource extends JsonResource
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
            $pivot_local_charge = PivotLocalChargeLclQuote::where(['charge_lcl_air_id' => $item->id, 
            'quote_id' => $item->quote_id])->count();

            return [
                'id' => $item->id,
                'segment_id' => $this->segment_id,
                'type' => $item->type->description,
                'charge' => $item->surcharge->name ?? 'Ocean Freight',
                'charge_id' => $item->surcharge_id ?? null,
                'charge_options' => $item->surcharge->options ?? null,
                'calculation_type' => $item->calculation_type->name ?? null,
                'calculation_type_code' => $item->calculation_type->unique_code ?? null,
                'port' => $this->getType($item->automatic_rate, $item->type_id),
                'units' => $item->units ?? 0,
                'price' => $item->price ?? 0,
                'profit' => $item->profit ?? 0,
                'total' => $this->setTotal($item->units, $item->price, $item->profit ),
                'currency' => $item->currency->alphacode ?? null,
                'provider' => (new CarrierResource($item->automatic_rate->carrier ?? null))->companyUser($item->automatic_rate->quote->company_user ?? null),
                'added' => $pivot_local_charge>0 ? true:false,
                'sale_code_id' => $item->charge_sale_code_quote['sale_term_code_id'] ?? null,
            ];
        });
    }

    public function getType($automatic_rate, $type){
        if($type == 1){
            return $automatic_rate->origin_port->display_name;
        }elseif($type == 2){
            return $automatic_rate->destination_port->display_name;
        }else{
            return null;
        }
    }

    public function setTotal($units, $price, $profit){
        return ((float)$units*(float)$price) + (float)$profit;
    }
}
