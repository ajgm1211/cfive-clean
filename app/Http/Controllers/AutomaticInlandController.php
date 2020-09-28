<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\AutomaticInlandResource;
use App\Http\Resources\AutomaticInlandTotalResource;
use App\QuoteV2;
use App\AutomaticInland;
use App\AutomaticInlandTotal;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AutomaticInlandController extends Controller
{
    public function list(Request $request, QuoteV2 $quote,$port_id)
    {   
        $total = AutomaticInlandTotal::where([['quote_id',$quote->id],['port_id',$port_id]])->first();

        if($total!=null){
            $total->totalize();
        }
        
        $results = AutomaticInland::where('port_id',$port_id)->filterByQuote($quote->id)->filter($request);
        
        return AutomaticInlandResource::collection($results);
    }

    public function store(Request $request, QuoteV2 $quote, $port_id)
    {
        $vdata = [
            'charge' => 'required',
            'provider' => 'required',
            'currency_id' => 'required',
        ];
        
        $equip = $quote->getContainerCodes($quote->equipment);
        $equip_array = explode(',',$equip);
        array_splice($equip_array,-1,1);

        foreach($equip_array as $eq){
            $vdata['rates_'.$eq] = 'sometimes|nullable|numeric';
        }

        $validate = $request->validate($vdata);

        $inland_rates = [];
        $inland_markup = [];

        foreach($equip_array as $eq){
            if(isset($validate['rates_'.$eq])){
                $inland_rates['c'.$eq] = $validate['rates_'.$eq]; 
            }else{
                $inland_rates['c'.$eq] = 0.00;
            }
            $inland_markup['m'.$eq] = 0.00;
        }

        $rates_json = json_encode($inland_rates);
        $markups_json = json_encode($inland_markup);

        $inland = AutomaticInland::create([
            'quote_id' => $quote->id,
            'automatic_rate_id' => $quote->rates_v2()->first()->id,
            'provider' => $validate['provider'],
            'charge' => $validate['charge'],
            'currency_id' => $validate['currency_id'],
            'port_id' => $port_id,
            'type' => $request->input('type'),
            'distance' => 40.00, //CHECK THIS LATER
            'contract' => 1, 
            'rate' => $rates_json,
            'markup' => $markups_json,
            'validity_start' => $quote->validity_start,
            'validity_end' => $quote->validity_end,
        ]);

        $totals = AutomaticInlandTotal::where([['quote_id',$quote->id],['port_id',$port_id]])->first();

        if($totals==null){
            $user_currency=$quote->user()->first()->companyUser()->first()->currency_id;

            $inland_totals = AutomaticInlandTotal::create([
                'quote_id' => $quote->id,
                'currency_id' => $user_currency,
                'port_id' => $port_id,
                'type' => $request->input('type'),
                'totals' => $rates_json,
                'markups' => $markups_json,
            ]);
            
            $inland_totals->totalize();
        }else{
            $totals->totalize();
        }
    }

    public function update(Request $request, QuoteV2 $quote, AutomaticInland $autoinland)
    {
        $form_keys = $request->input('keys');
        
        $data = [];

        foreach($form_keys as $fkey){
            if(strpos($fkey,'rates') !== false){
                $data += $request->validate([$fkey=>'sometimes|numeric|nullable']);
            }
        }

        $rates = [];
        
        foreach($data as $key=>$value){
            if($value==null){$value=0;}
            
            if(strpos($key,'rates') !== false){
                $rates['c'.str_replace('rates_','',$key)] = $value;
            }
        }
        
        $data += $request->validate(['charge'=>'required',
                                    'provider'=>'required',
                                    'currency_id'=>'required',
                                    ]);

        if(count($rates) != 0){
            $rates_json = json_encode($rates);
            $data['rate'] = $rates_json;
        }

        foreach($data as $key=>$value){
            if(isset($autoinland->$key) || $autoinland->$key==null){
                if($key=="currency_id"){
                    $autoinland->update(["currency_id"=>$value['id']]);
                }else{
                    $autoinland->update([$key=>$value]);
                }
            }
        }

        return new AutomaticInlandResource($autoinland);
    }

    public function updateTotals(Request $request, QuoteV2 $quote, $port_id)
    {        
        $form_keys = $request->input('keys');

        $total = AutomaticInlandTotal::where([['quote_id',$quote->id],['port_id',$port_id]])->first();

        $data=[];
           
        foreach($form_keys as $fkey){
            if(strpos($fkey,'profits') !== false){
                $data += $request->validate([$fkey=>'sometimes|numeric|nullable']);
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

            $total->update(['markups'=>$markups_json]);
        }

        $total->totalize();
    }

    public function retrieve(QuoteV2 $quote, $port_id)
    {   
        $inland_total = AutomaticInlandTotal::where([['quote_id',$quote->id],['port_id',$port_id]])->first();
        return new AutomaticInlandTotalResource($inland_total);
    }

    public function destroy(AutomaticInland $autoinland)
    {      
        $port = $autoinland->port_id;
        
        $total = AutomaticInlandTotal::where([['quote_id',$autoinland->quote_id],['port_id',$port]])->first();

        $autoinland->delete();
        
        $total->totalize();

        return response()->json(null, 204); 
    }

    public function destroyAll(Request $request)
    {   
        DB::table('automatic_inlands')->whereIn('id', $request->input('ids'))->delete();

        return response()->json(null, 204);
    }
}
