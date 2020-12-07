<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\AutomaticInlandLclAirResource;
use App\Http\Resources\AutomaticInlandTotalResource;
use App\Http\Resources\InlandAddressResource;
use App\InlandAddress;
use App\QuoteV2;
use App\Container;
use App\Currency;
use App\Harbor;
use App\AutomaticRate;
use App\AutomaticInlandLclAir;
use App\AutomaticInlandTotal;
use App\Http\Traits\SearchTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use GoogleMaps;

class AutomaticInlandLclController extends Controller
{
    
    public function list(Request $request, QuoteV2 $quote,$combo)
    {   
        $combo_array = explode(';',$combo);

        $port_id = $combo_array[0];

        $address_id = $combo_array[1];

        $total = AutomaticInlandTotal::where([['quote_id',$quote->id],['port_id',$port_id],['inland_address_id',$address_id]])->first();

        if($total!=null){
            $total->totalize();
        }
        
        $results = AutomaticInlandLclAir::where([['port_id',$port_id],['inland_address_id',$address_id]])->filterByQuote($quote->id)->filter($request);
        
        return AutomaticInlandLclAirResource::collection($results);
    }

    public function store(Request $request, QuoteV2 $quote, $port_id)
    {     
        $inland_address = InlandAddress::where([['quote_id',$quote->id],['port_id',$port_id],['address',$request->input('address')]])->first();

        if($inland_address == null){
            $inland_address = InlandAddress::create([
                'quote_id'=>$quote->id,
                'port_id'=>$port_id,
                'address'=>$request->input('address')
            ]);
        }
                
        $vdata = $request->validate([
            'charge' => 'nullable|sometimes',
            'provider_id' => 'nullable',
            'currency_id' => 'required',
            'total' => 'sometimes|nullable',
            'profit' => 'sometimes|nullable'
        ]);
        
        $type = $request->input('type');

        $autoDistance = $request->input('distance');

        if($autoDistance != 0){
            $distance = $autoDistance;
        }else{
            if ($type == 'Destination') {
                $origin = Harbor::where('id',$port_id)->first()->coordinates;
                $destination = $inland_address->address;
            } elseif ($type == 'Origin') {
                $origin = $inland_address->address;
                $destination = Harbor::where('id',$port_id)->first()->coordinates;
            }

            $response = GoogleMaps::load('directions')
                ->setParam([
                    'origin' => $origin,
                    'destination' => $destination,
                    'mode' => 'driving',
                    'language' => 'es',
                ])->get();

            $var = json_decode($response);
            if (empty($var->routes)) {
                $distance = 1;
            }
            foreach ($var->routes as $resp) {
                foreach ($resp->legs as $dist) {

                    $km = explode(" ", $dist->distance->text);
                    $distance = str_replace(".", "", $km[0]);
                    $distance = floatval($distance);
                    if ($distance < 1) {
                        $distance = 1;
                    }
                }
            }           
        }

        $inland = AutomaticInlandLclAir::create([
            'quote_id' => $quote->id,
            'automatic_rate_id' => $quote->rates_v2()->first()->id,
            'provider'=> 'Inland',
            'provider_id' => count($vdata['provider_id'])==0 ? null : $vdata['provider_id']['id'],
            'currency_id' => $vdata['currency_id']['id'],
            'port_id' => $port_id,
            'charge' => $vdata['charge'],
            'inland_address_id'=> $inland_address->id,
            'type' => $type,
            'distance' => $distance,
            'contract' => 1, 
            'total' => $vdata['total'],
            'markup' => $vdata['profit'],
            'validity_start' => $quote->validity_start,
            'validity_end' => $quote->validity_end,
        ]);

        $totals = AutomaticInlandTotal::where([['quote_id',$quote->id],['port_id',$port_id],['inland_address_id',$inland_address->id]])->first();

        if($totals == null){
            $user_currency = $quote->user()->first()->companyUser()->first()->currency_id;

            $totals = AutomaticInlandTotal::create([
                'quote_id' => $quote->id,
                'port_id' => $port_id,
                'inland_address' => $inland_address->id,
                'currency_id' => $user_currency
            ]);
        }
        
        $totals->totalize();
    }

    public function ratesCurrency($id, $typeCurrency)
    {
        $rates = Currency::where('id', '=', $id)->get();
        foreach ($rates as $rate) {
            if ($typeCurrency == "USD") {
                $rateC = $rate->rates;
            } else {
                $rateC = $rate->rates_eur;
            }
        }
        return $rateC;
    }

    public function storeTotals(QuoteV2 $quote, $combo)
    {   
        $combo_array = explode(';',$combo);
        
        $address = $combo_array[0];

        $port_type = $combo_array[1];

        $port_id = $combo_array[2];
        
        $inland_address = InlandAddress::where([['quote_id',$quote->id],['port_id',$port_id],['address',$address]])->first();
        
        if($inland_address == null){

            $inland_address = InlandAddress::create([
                'quote_id'=>$quote->id,
                'port_id'=>$port_id,
                'address'=>$address
            ]);
        }

        $user_currency = $quote->user()->first()->companyUser()->first()->currency_id;

        $totals = AutomaticInlandTotal::where([['quote_id',$quote->id],['port_id',$port_id],['inland_address_id',$inland_address->id]])->first();

        if($totals == null){
            $totals = AutomaticInlandTotal::create([
                'quote_id' => $quote->id,
                'currency_id' => $user_currency,
                'port_id' => $port_id,
                'inland_address_id' => $inland_address->id,
                'type' => $port_type,
                'totals' => null,
                'markups' => null                    
            ]);
        }else{
            $totals->totalize();
        }
    }

    public function update(Request $request, QuoteV2 $quote, AutomaticInlandLclAir $autoinland)
    {
        $form_keys = $request->input('keys');
        
        $data = [];
        
        $data += $request->validate([
            'charge' => 'nullable',
            'provider_id'=>'nullable',
            'currency_id'=>'required',
            'total' => 'numeric|sometimes|nullable'
        ]);
        
        foreach($data as $key=>$value){
            if(isset($autoinland->$key) || $autoinland->$key==null){
                if($key=="currency_id" || ($key=='provider_id' && $data[$key]!=null)){
                    $autoinland->update([$key=>$value['id']]);
                }else{
                    $autoinland->update([$key=>$value]);
                }
            }
        }

        $totals = $autoinland->inland_address()->first()->inland_totals()->first();

        $totals->totalize();

        return new AutomaticInlandLclAirResource($autoinland);
    }

    public function updateTotals(Request $request, QuoteV2 $quote, $combo)
    {    
        $combo_array = explode(';',$combo);

        $port_id = $combo_array[0];

        $address_id = $combo_array[1];

        $form_keys = $request->input('keys');

        $total = AutomaticInlandTotal::where([['quote_id',$quote->id],['port_id',$port_id],['inland_address_id',$address_id]])->first();

        $data=[];
        
        $data += $request->validate(['profit'=>'sometimes|numeric|nullable']);
        
        if($data['profit']==null){$data['profit']=0;}

        $markups_json = json_encode($data);

        $total->update(['markups'=>$markups_json]);

        $total->totalize();
    }

    public function retrieve(QuoteV2 $quote, $combo)
    {   
        $combo_array = explode(';',$combo);

        $port_id = $combo_array[0];

        $address_id = $combo_array[1];

        $inland_total = AutomaticInlandTotal::where([['quote_id',$quote->id],['port_id',$port_id],['inland_address_id',$address_id]])->first();

        return new AutomaticInlandTotalResource($inland_total);
    }

    public function retrieveAddresses(QuoteV2 $quote, $port_id)
    {
        $results = InlandAddress::where([['quote_id',$quote->id],['port_id',$port_id]])->get();

        return InlandAddressResource::collection($results);
    }

    public function destroy(AutomaticInlandLclAir $autoinland)
    {      
        $port = $autoinland->port_id;
        
        $total = AutomaticInlandTotal::where([['quote_id',$autoinland->quote_id],['port_id',$port]])->first();

        $autoinland->delete();
        
        $total->totalize();

        return response()->json(null, 204); 
    }

    public function destroyAll(Request $request)
    {   
        DB::table('automatic_inland_lcl_airs')->whereIn('id', $request->input('ids'))->delete();

        return response()->json(null, 204);
    }
}
