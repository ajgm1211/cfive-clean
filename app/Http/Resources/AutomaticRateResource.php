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
            'schedule_type' => $this->setSchedule($this->schedule_type),
            'transit_time' => $this->transit_time,
            'via' => $this->via,
        ];

        return $data;
    }

    public function setSchedule($sctype)
    {
        if($sctype == 'Direct'){
            return ['id'=>1,'name'=>ScheduleType::where('id',1)->first()->name];
        }else if($sctype == 'Transhipment'){
            return ['id'=>2,'name'=>ScheduleType::where('id',2)->first()->name];
        }else if($sctype == 1 || $sctype == 2){
            return ['id'=>$sctype,'name'=>ScheduleType::where('id',$sctype)->first()->name];
        }else if($sctype == null){
            return $sctype;
        }
    }
}
