<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Traits\SearchTrait;

class ChargeResource extends JsonResource
{
    use SearchTrait;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $surcharge_ocean = $this->getSurchargeOcean();
        $data = [
            'id' => $this->id,
            'automatic_rate_id' => $this->automatic_rate()->first(),
            'type_id' => $this->type_id,
            'surcharge_id' => ($this->surcharge_id == $surcharge_ocean->id) ? $this->surcharge_id : $this->surcharge()->first(), 
            'calculation_type_id' => $this->calculation_type,
            'amount' => $this->amount, 
            'markups' => $this->markups,
            'currency_id' => $this->currency()->first(),
            'total' => $this->total,
            'fixed_surcharge' => ($this->surcharge_id == $surcharge_ocean->id) ? $this->surcharge()->first()->name : $this->surcharge()->first()->name,
            'fixed_currency' => $this->currency()->first(),
            'fixed_calculation_type' => is_null($this->calculation_type) ? $this->calculation_type : $this->calculation_type()->first()->name
        ];

        return $this->addContainers($data);
    }

    public function addContainers($data)
    {   
        $surcharge_ocean = $this->getSurchargeOcean();
        if($this->amount!= null){
            $charges = json_decode($data['amount']);
            foreach($charges as $key=>$value){
                if($this->surcharge_id == $surcharge_ocean->id){
                    $fr_key = 'freights_'.str_replace('c','',$key);
                    $data[$fr_key] = isDecimal($value,true);
                } else {
                    $cont_key = 'rates_'.str_replace('c','',$key);
                    $data[$cont_key] = isDecimal($value,true);
                }
            }
        }

        return $data;
    }
}
