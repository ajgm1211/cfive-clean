<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AutomaticRate;
use App\QuoteV2;
use App\ChargeLclAir;
use App\Http\Resources\AutomaticRateResource;
use App\Http\Resources\ChargeLclResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ChargeLclController extends Controller
{
    public function list(Request $request, QuoteV2 $quote, AutomaticRate $autorate)
    {   
        $results = ChargeLclAir::where([['surcharge_id','!=',null],['type_id',3]])->filterByAutorate($autorate->id)->filter($request);
        
        return ChargeLclResource::collection($results);
    }

    public function store(Request $request, QuoteV2 $quote, AutomaticRate $autorate)
    {   
        $vdata = [
            'surcharge_id' => 'required',
            'calculation_type_id' => 'required',
            'units' => 'required|numeric',
            'minimum' => 'required|numeric',
            'price_per_unit' => 'required|numeric',
            'currency_id' => 'required',
        ];
        
        $validate = $request->validate($vdata);
        
        $validate['total'] = $validate['units'] * $validate['price_per_unit'];

        if($validate['total'] < $validate['minimum']){
            $validate['total'] = $validate['minimum'];
        }

        $charge = ChargeLclAir::create([
            'automatic_rate_id' => $autorate->id,
            'calculation_type_id' => $validate['calculation_type_id'],
            'currency_id' => $validate['currency_id'],
            'surcharge_id' => $validate['surcharge_id'],
            'type_id' => 3,
            'units' => $validate['units'],
            'minimum' => $validate['minimum'],
            'price_per_unit' => $validate['price_per_unit'],
            'total' => $validate['total'],
            'markup' => 1.00,
        ]);

        $totals = $rate->totals()->first();

        $totals->totalize($autorate->currency_id);

        return new ChargeLclResource($charge);
    }

    public function update(Request $request, ChargeLclAir $charge)
    {
        $autorate = $charge->automatic_rate()->first();

        $form_keys = $request->input('keys');

        $data = [];
        
        if($request->input('fixed_currency_id')!=null){
            $data += $request->validate([
                'fixed_units'=>'required|numeric',
                'fixed_price_per_unit'=>'required|numeric',
                'fixed_minimum'=>'required|numeric',
                'fixed_currency_id'=>'required'
            ]);

            $data['fixed_total'] = $data['fixed_units'] * $data['fixed_price_per_unit'];

            if($data['fixed_total'] < $data['fixed_minimum']){
                $data['fixed_total'] = $data['fixed_minimum'];
            }
    
            foreach($data as $key=>$value){
                $key = str_replace('fixed_','',$key);
                $charge->update([$key=>$value]);
            }
        }else{
            $data += $request->validate([
                'units'=>'required|numeric',
                'price_per_unit'=>'required|numeric',
                'minimum'=>'required|numeric',
                'currency_id'=>'required',
                'calculation_type_id'=>'required'
            ]);

            $data['total'] = $data['units'] * $data['price_per_unit'];

            if($data['total'] < $data['minimum']){
                $data['total'] = $data['minimum'];
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
        }

        $totals = $autorate->totals()->first();
        
        if(isset($data['fixed_currency'])){
            $charge->update(['currency_id'=>$data['fixed_currency']]);
            $totals->totalize($request->input('fixed_currency'));
        } else {
            $totals->totalize($autorate->currency_id);
        }  

        return new ChargeLclResource($charge);
        
    }

    public function retrieve(AutomaticRate $autorate)
    {   
        $charge = ChargeLclAir::where([['automatic_rate_id',$autorate->id],['surcharge_id',null]])->first();

        return new ChargeLclResource($charge);
    }

    public function destroy(ChargeLclAir $charge)
    {
        $charge->delete();
        
        $autorate = $charge->automatic_rate()->first();

        $totals = $autorate->totals()->first();

        $totals->totalize($autorate->currency_id);

        return response()->json(null, 204);
    }

    public function destroyAll(Request $request)
    {   
        DB::table('charges')->whereIn('id', $request->input('ids'))->delete();

        return response()->json(null, 204);
    }
}
