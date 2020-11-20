<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\DestinationType;

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

        return $this->addContainers($data);
    }

    public function addContainers($data)
    {   
        $quote = $this->quotev2()->first();

        if($quote->type == 'FCL'){
            if($this->markups!=null){
                $profits = json_decode($data['markups']);
                foreach($profits as $code=>$profit){
                    $prof_key = str_replace('m','',$code);
                    $data['profits_'.$prof_key] = isDecimal($profit,true);
                }
            }
            if($this->total!=null){
                $totals = json_decode($data['total']);
                foreach($totals as $code=>$total){
                    $total_key = str_replace('c','',$code);
                    $data['totals_'.$total_key] = isDecimal($total,true);
                }
            }
        }else if($quote->type == "LCL"){
            if($this->markups!=null){
                $profits = json_decode($data['markups']);
                foreach($profits as $code=>$profit){
                    $data['profits_'.$code] = isDecimal($profit,true);
                }
            }
            if($this->total!=null){
                $totals = json_decode($data['total']);
                foreach($totals as $code=>$total){
                    $data['totals_'.$code] = isDecimal($total,true);
                }
            }
        }

        return $data;
    }

    public function setSchedule($sctype)
    {
        if($sctype == 'Direct'){
            return ['id'=>1,'name'=>DestinationType::where('id',2)->first()->name];
        }else if($sctype == 'Transhipment'){
            return ['id'=>2,'name'=>DestinationType::where('id',1)->first()->name];
        }else if($sctype == 1 || $sctype == 2){
            return ['id'=>$sctype,'name'=>DestinationType::where('id',$sctype)->first()->name];
        }else if($sctype == null){
            return $sctype;
        }
    }
}
