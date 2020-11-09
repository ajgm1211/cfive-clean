<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChargeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'automatic_rate_id' => $this->automatic_rate()->first(),
            'type_id' => $this->type_id,
            'surcharge_id' => is_null($this->surcharge_id) ? $this->surcharge_id : $this->surcharge()->first(), 
            'calculation_type_id' => $this->calculation_type,
            'amount' => $this->amount, 
            'markups' => $this->markups,
            'currency_id' => $this->currency()->first(),
            'total' => $this->total,
            'fixed_surcharge' => is_null($this->surcharge_id) ? $this->surcharge_id : $this->surcharge()->first()->name,
            'fixed_currency' => $this->currency()->first(),
            'fixed_calculation_type' => is_null($this->calculation_type) ? $this->calculation_type : $this->calculation_type()->first()->name
        ];

        return $this->addContainers($data);
    }

    public function addContainers($data)
    {   
        if($this->amount!= null){
            $charges = json_decode($data['amount']);
            foreach($charges as $key=>$value){
                if($this->surcharge_id == null){
                    $fr_key = 'freights_'.str_replace('c','',$key);
                    $data[$fr_key] = $value;
                } else {
                    $cont_key = 'rates_'.str_replace('c','',$key);
                    $data[$cont_key] = $value;
                }
            }
        }

        return $data;
    }
}
