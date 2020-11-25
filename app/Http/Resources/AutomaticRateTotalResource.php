<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AutomaticRateTotalResource extends JsonResource
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
            'automatic_rate_id' => $this->automatic_rate_id,
            'currency_id' => $this->currency,
            'totals' => $this->totals,
            'markups' => $this->markups,
            'totals_currency' => $this->currency()->first()->alphacode,
            'profits_currency' => $this->currency()->first()
        ];

        return $this->addContainers($data);
    }

    public function addContainers($data)
    {
        $quote = $this->quote()->first();

        if($quote->type=='FCL'){
            if($this->markups!=null){
                $profits = $data['markups'];
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
        }else if($quote->type=='LCL'){
            if($this->markups!=null){
                $profits = json_decode($data['markups']);
                foreach($profits as $code=>$profit){
                    $data['profit'] = $profit;
                }
            }else{
                $data['profit'] = 0;
            }
            
            if($this->totals!=null){
                $totals = json_decode($data['totals']);
                foreach($totals as $code=>$total){
                    $data['lcl_totals'] = $total;
                }
            }else{
                $data['lcl_totals'] = 0;
            }
        }

        return $data;
    }
}

