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
            $results[$auto->id] = Charge::filterByAutorate($auto->id)->filter($request);
        }

        return ChargeResource::collection($results[$autorate->id]);
    }

    public function store(Request $request, QuoteV2 $quote, AutomaticRate $autorate)
    {   
        $vdata = [
            'surcharge_id' => 'nullable',
            'calculation_type_id' => 'required',
            'currency_id' => 'required',
        ];
        
        $equip = $quote->getContainerCodes($quote->equipment);
        $equip_array = explode(',',$equip);
        array_splice($equip_array,-1,1);

        foreach($equip_array as $eq){
            $vdata['rates_'.$eq] = 'sometimes|nullable';
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
            'type_id' => 3,
            'amount'=>$rates_json
        ]);

    }
}
