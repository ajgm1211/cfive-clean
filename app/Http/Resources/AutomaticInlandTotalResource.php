<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AutomaticInlandTotalResource extends JsonResource
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
            'port_id' => $this->port,
            'type' => $this->type,
            'currency_id' => $this->currency,
            'totals' => $this->totals,
            'markups' => $this->markups,
            'totals_currency' => $this->currency()->first()->alphacode,
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
        
        if($this->totals!=null){
            $totals = json_decode($data['totals']);
            foreach($totals as $code=>$total){
                $total_key = str_replace('c','',$code);
                $data['totals_'.$total_key] = $total;
                }
            }

        return $data;
    }
}
