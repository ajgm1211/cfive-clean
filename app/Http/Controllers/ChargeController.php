<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AutomaticRate;
use App\QuoteV2;
use App\Charge;
use App\Http\Resources\AutomaticRateResource;
use App\Http\Resources\ChargeResource;
use Illuminate\Support\Facades\Auth;

class ChargeController extends Controller
{
    public function list(Request $request, QuoteV2 $quote, AutomaticRate $autorate)
    {   
        $autorates = AutomaticRate::where('quote_id',$quote->id)->get();

        foreach($autorates as $auto){
            $results[$auto->id] = Charge::where('surcharge_id','!=',null)->filterByAutorate($auto->id)->filter($request);
            //$results[$auto->id] = Charge::filterByAutorate($auto->id)->filter($request);
        }

        return ChargeResource::collection($results[$autorate->id]);
    }

    public function store(Request $request, QuoteV2 $quote, AutomaticRate $autorate)
    {   
        $vdata = [
            'surcharge_id' => 'required',
            'calculation_type_id' => 'required',
            'currency_id' => 'required',
        ];
        
        $equip = $quote->getContainerCodes($quote->equipment);
        $equip_array = explode(',',$equip);
        array_splice($equip_array,-1,1);

        foreach($equip_array as $eq){
            $vdata['rates_'.$eq] = 'sometimes|nullable|numeric';
        }

        $validate = $request->validate($vdata);

        $charge_rates = [];

        foreach($equip_array as $eq){
            if(isset($validate['rates_'.$eq])){
                $charge_rates['c'.$eq] = $validate['rates_'.$eq]; 
            }else{
                $charge_rates['c'.$eq] = 0.00;
            }
        }

        $rates_json = json_encode($charge_rates);

        $charge = Charge::create([
            'automatic_rate_id' => $autorate->id,
            'calculation_type_id' => $validate['calculation_type_id'],
            'currency_id' => $validate['currency_id'],
            'surcharge_id' => $validate['surcharge_id'],
            'type_id' => 3,
            'amount'=>$rates_json
        ]);

    }

    public function update(Request $request, Charge $charge)
    {
        $form_keys = $request->input('keys');

        $data = [];
        $type = '';

        foreach($form_keys as $fkey){
            if(strpos($fkey,'markups') !== false){
                $data += $request->validate([$fkey=>'numeric']);
                $type = 'm';
            } else if(strpos($fkey,'freights') !== false){
                $data += $request->validate([$fkey=>'numeric']);
                $type = 'c';
            }
        }
            
        $markups = [];
        $rates = [];

        foreach($data as $key=>$value){
            if($type == 'm'){
                $markups[$type.str_replace('markups_','',$key)] = $value;
            } else if($type == 'c'){
                $rates[$type.str_replace('freights_','',$key)] = $value;
            }
        }
        
        if(count($markups) != 0){
            $markups_json = json_encode($markups);
            $charge->update(['markups'=>$markups_json]);
        }else if (count($rates) != 0){
            $rates_json = json_encode($rates);
            $charge->update(['amount'=>$rates_json]);
        }
        
    }

    public function retrieve(AutomaticRate $autorate)
    {   
        $charge = Charge::where([['automatic_rate_id',$autorate->id],['surcharge_id',null]])->first();
        return new ChargeResource($charge);
    }

    public function destroy(Charge $charge)
    {
        $charge->delete();

        return response()->json(null, 204);
    }
}
