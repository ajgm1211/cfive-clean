<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AutomaticRate;
use App\QuoteV2;
use App\Charge;
use App\Http\Resources\AutomaticRateResource;
use App\Http\Resources\ChargeResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ChargeController extends Controller
{
    public function list(Request $request, QuoteV2 $quote, AutomaticRate $autorate)
    {   
        $results = Charge::where([['surcharge_id','!=',null],['type_id',3]])->filterByAutorate($autorate->id)->filter($request);
        
        return ChargeResource::collection($results);
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

        $autorate->totalize($autorate->currency_id);

        return new ChargeResource($charge);
    }

    public function update(Request $request, Charge $charge)
    {
        $autorate = $charge->automatic_rate()->first();

        $form_keys = $request->input('keys');

        $data = [];

        foreach($form_keys as $fkey){
            if(strpos($fkey,'freights') !== false || strpos($fkey,'rates') !== false){
                $data += $request->validate([$fkey=>'sometimes|numeric|nullable']);
            }
        }

        $rates = [];
        
        foreach($data as $key=>$value){
            if($value==null){$value=0;}
            
            if(strpos($key,'freights') !== false){
                $rates['c'.str_replace('freights_','',$key)] = $value;
            } else if (strpos($key,'rates') !== false){
                $rates['c'.str_replace('rates_','',$key)] = $value;
            }
        }
        
        $data += $request->validate([
            'fixed_currency'=>'sometimes|required',
            'surcharge_id'=>'sometimes|required',
            'currency_id'=>'sometimes|required',
            'calculation_type_id'=>'sometimes|required']);

        if(count($rates) != 0){
            $rates_json = json_encode($rates);
            $data['amount'] = $rates_json;
        }

        foreach($data as $key=>$value){
            if(isset($charge->$key) || $charge->$key==null){
                if(strpos($key,'_id')!==false){
                    $charge->update([$key=>$value['id']]);
                }else{
                    $charge->update([$key=>$value]);
                }
            }
        }

        if(isset($data['fixed_currency'])){
            $charge->update(['currency_id'=>$data['fixed_currency']]);
            $autorate->totalize($request->input('fixed_currency'));
        } else {
            $autorate->totalize($autorate->currency_id);
        }  

        return new ChargeResource($charge);
        
    }

    public function retrieve(AutomaticRate $autorate)
    {   
        $charge = Charge::where([['automatic_rate_id',$autorate->id],['surcharge_id',null]])->first();
        
        return new ChargeResource($charge);
    }

    public function destroy(Charge $charge)
    {
        $charge->delete();
        
        $autorate = $charge->automatic_rate()->first();
        $autorate->totalize($autorate->currency_id);

        return response()->json(null, 204);
    }

    public function destroyAll(Request $request)
    {   
        DB::table('charges')->whereIn('id', $request->input('ids'))->delete();

        return response()->json(null, 204);
    }
}
