<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\ScheduleType;

class AutomaticRateResource extends JsonResource
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
            'quote_id' => $this->quote_id,
            'contract' => $this->contract,
            'origin_port_id' => $this->origin_port_id, 
            'destination_port_id' => $this->destination_port_id,
            'origin_airport_id' => $this->origin_airport_id, 
            'destination_airport_id' => $this->destination_airport_id,
            'carrier_id' => $this->carrier_id,
            'airline_id' => $this->airline_id,
            'rates' => $this->rates,
            'exp_date'=>$this->validity_end,            
            'markups' => $this->markups,
            'currency_id' => $this->currency_id,
            'total' => $this->total,
            'remarks' => $this->remarks,
            'remarks_spanish' => $this->remarks_spanish,
            'remarks_english' => $this->remarks_english,
            'remarks_portuguese' => $this->remarks_portuguese,
            'schedule_type' => is_null($this->schedule_type) ? $this->schedule_type : ['id'=>$this->schedule_type,'name'=>ScheduleType::where('id',$this->schedule_type)->first()->name],
            'transit_time' => $this->transit_time,
            'via' => $this->via,
            'totals_currency' => $this->currency()->first()->alphacode,
            'profits_currency' => $this->currency()->first()
        ];

        return $this->addContainers($data);
    }

    public function addContainers($data)
    {
        if($this->markups!=null){
            $profits = json_decode($data['markups']);
            foreach($profits as $code=>$profit){
                $prof_key = str_replace('m','',$code);
                $data['profits_'.$prof_key] = $profit;
                }
            }
        
        if($this->total!=null){
            $totals = json_decode($data['total']);
            foreach($totals as $code=>$total){
                $total_key = str_replace('c','',$code);
                $data['totals_'.$total_key] = $total;
                }
            }

        return $data;
    }
}
