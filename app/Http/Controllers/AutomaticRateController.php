<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AutomaticRate;
use App\QuoteV2;
use App\Charge;
use App\Http\Resources\AutomaticRateResource;
use App\Http\Resources\ChargeResource;
use Illuminate\Support\Facades\Auth;

class AutomaticRateController extends Controller
{

    public function list(Request $request, QuoteV2 $quote)
    {   
        $results = AutomaticRate::filterByQuote($quote->id)->filter($request);

        return AutomaticRateResource::collection($results);
    }

    public function store(Request $request, QuoteV2 $quote)
    {
        $data = $request->validate(['POL' => 'required',
                                    'POD' => 'required',
                                    'carrier'=> 'required'
                                    ]);

        $rate = AutomaticRate::create([
                'quote_id' => $quote->id,
                'contract' => '',
                'validity_start' => $quote->validity_start,
                'validity_end' => $quote->validity_end,
                'origin_port_id' => $data['POL'],
                'destination_port_id' => $data['POD'],
                'currency_id' => '149'     
                ]);

        $freight = Charge::create([
                'automatic_rate_id' => $rate->id,
                'type_id' => '3',
                'calculation_type_id' => '5',
                'currency_id' => $rate->currency_id,
                ]);
                
        return new AutomaticRateResource($rate);
    }
 
    public function update(Request $request, QuoteV2 $quote, AutomaticRate $autorate)
    {   

        $form_keys = $request->input('keys');
        
        if(!in_array('profits_currency',$form_keys)){
            $data = $request->validate([
                'transit_time' => 'numeric'
            ]);
            
            foreach($form_keys as $fkey){
                if(!in_array($fkey,$data) && $fkey != 'keys'){
                    $data[$fkey] = $request->input($fkey);
                }
            };

            if(!isset($data['contract'])){
                $data['contract'] = '';
            }

            if(!isset($data['exp_date'])){
                $data['validity_end'] = $data['exp_date'];
                unset($data['exp_date']);
            }

            foreach(array_keys($data) as $key){
                $autorate->update([$key=>$data[$key]]);
            }
        
        }else{

            $data=[];
           
            foreach($form_keys as $fkey){
                if(strpos($fkey,'profits') !== false){
                    $data += $request->validate([$fkey=>'numeric|nullable']);
                }
            }
    
            $markups = [];
            
            foreach($data as $key=>$value){
                if($value==null){$value=0;}
                if($key!='profits_currency'){
                    $markups['m'.str_replace('profits_','',$key)] = $value;
                }
            }

            if(count($markups) != 0){
                $markups_json = json_encode($markups);
    
                $autorate->update(['markups'=>$markups_json]);

                $autorate->totalize($request->input('profits_currency'));
            }

        }
    }

    public function retrieve(QuoteV2 $quote, AutomaticRate $autorate)
    {
        return new AutomaticRateResource($autorate);
    }
}
