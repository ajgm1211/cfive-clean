<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AutomaticRate;
use App\QuoteV2;
use App\Charge;
use App\ChargeLclAir;
use App\AutomaticInlandTotal;
use App\Http\Resources\AutomaticRateResource;
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
        $data = $request->validate([
            'POL' => 'required',
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
            'currency_id' => '149',
            'carrier_id' => $data['carrier'],
        ]);
        
        if($quote->type == 'FCL'){
            $freight = Charge::create([
                'automatic_rate_id' => $rate->id,
                'type_id' => '3',
                'calculation_type_id' => '5',
                'currency_id' => $rate->currency_id,
            ]);
        }else if($quote->type == 'LCL'){
            $freight = ChargeLclAir::create([
                'automatic_rate_id' => $rate->id,
                'type_id' => '3',
                'calculation_type_id' => '10',
                'units' => 1.00,
                'price_per_unit' => 1.00,
                'minimum' => 1.00,
                'total' => 1.00,
                'markup' => 1.00,
                'currency_id' => $rate->currency_id,
            ]);
        }

        $originInland = AutomaticInlandTotal::create([
            'quote_id' => $quote->id,
            'port_id' => $rate->origin_port_id,
            'currency_id' => $quote->user()->first()->companyUser()->first()->currency_id,
            'type' => 'Origin',
        ]);

        $destInland = AutomaticInlandTotal::create([
            'quote_id' => $quote->id,
            'port_id' => $rate->destination_port_id,
            'currency_id' => $quote->user()->first()->companyUser()->first()->currency_id,
            'type' => 'Destination',
        ]);

                
        return new AutomaticRateResource($rate);
    }
 
    public function update(Request $request, QuoteV2 $quote, AutomaticRate $autorate)
    {   
        $form_keys = $request->input('keys');

        $remarks_keys = ['remarks_english','remarks_spanish','remarks_portuguese'];

        if(array_intersect($form_keys,$remarks_keys)!=[]){
            $data = $request->input();

            foreach($data as $key=>$value){
                $autorate->update([$key=>$value]);
            }

        }else{
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
        }
    }

    public function updateTotals(Request $request, QuoteV2 $quote, AutomaticRate $autorate)
    {
        $form_keys = $request->input('keys');

        $data = [];
           
        foreach($form_keys as $fkey){
            if(strpos($fkey,'profits') !== false){
                $data += $request->validate([$fkey=>'sometimes|numeric|nullable']);
            }
        }

        $markups = [];

        foreach($data as $key=>$value){
            if($value==null){$value=0;}
            if($key!='profits_currency'){
                if($quote->type == 'FCL'){
                    $markups['m'.str_replace('profits_','',$key)] = $value;
                }else if($quote->type == 'LCL'){
                    $markups[str_replace('profits_','',$key)] = $value;
                }
            }
        }
            
        if(count($markups) != 0){
            $markups_json = json_encode($markups);

            $autorate->update(['markups'=>$markups_json]);

            $autorate->totalize($request->input('profits_currency'));
        }
    }

    public function retrieve(QuoteV2 $quote, AutomaticRate $autorate)
    {
        return new AutomaticRateResource($autorate);
    }

    public function destroy(AutomaticRate $autorate)
    {
        $autorate->delete();
        
        $originInland = AutomaticInlandTotal::where([['quote_id',$autorate->quote_id],['port_id',$autorate->origin_port_id]])->first();

        $originInland->delete();

        $destInland = AutomaticInlandTotal::where([['quote_id',$autorate->quote_id],['port_id',$autorate->destination_port_id]])->first();

        $destInland->delete();

        return response()->json(null, 204);
    }
}
