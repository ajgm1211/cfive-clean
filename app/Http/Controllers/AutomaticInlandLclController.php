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
use App\InlandLocalChargeLclGroup;
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
            $results = AutomaticInlandLclAir::where('inland_totals_id',$total->id)->filterByQuote($quote->id)->filter($request);
        }else{
            return null;
        }
        
        
        return AutomaticInlandLclAirResource::collection($results);
    }

    public function store(Request $request, QuoteV2 $quote, $port_id)
    {     
        $inland_address = InlandAddress::where([['quote_id',$quote->id],['port_id',$port_id],['address',$request->input('address')]])->first();

        $type = $request->input('type');

        if($inland_address == null){
            $inland_address = InlandAddress::create([
                'quote_id'=>$quote->id,
                'port_id'=>$port_id,
                'address'=>$request->input('address'),
                'type'=>$type
                ]);

            if($type == 'Destination'){
                $quote->update(['destination_address' => $request->input('address')]);
            }else if($type == 'Origin'){
                $quote->update(['origin_address' => $request->input('address')]);
            }
        }
                
        $vdata = $request->validate([
            'charge' => 'nullable|sometimes',
            'provider_id' => 'nullable',
            'currency_id' => 'required',
            'total' => 'sometimes|nullable',
            'profit' => 'sometimes|nullable'
        ]);

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

        $totals = $inland_address->inland_totals()->first();

        if($totals == null){
            $totals = AutomaticInlandTotal::create([
                'quote_id' => $quote->id,
                'port_id' => $port_id,
                'type' => $type,
                'inland_address_id' => $inland_address->id,
                'currency_id' => $vdata['currency_id']['id']
            ]);

            $pdfOptions = [
                "grouped" =>false, 
                "groupId"=>null
                ];
                
            $totals->pdf_options = $pdfOptions;
            $totals->save();
        }

        $inland = AutomaticInlandLclAir::create([
            'quote_id' => $quote->id,
            'provider'=> $vdata['provider_id']['name'] ?? null,
            'provider_id' => isset($vdata['provider_id']) && count($vdata['provider_id'])==0 ? null : $vdata['provider_id']['id'],
            'currency_id' => $vdata['currency_id']['id'],
            'port_id' => $port_id,
            'charge' => $vdata['charge'],
            'inland_totals_id'=> $totals->id,
            'type' => $type,
            'distance' => $distance,
            'contract' => 1, 
            'total' => $vdata['total'],
            'markup' => $vdata['profit'],
            'validity_start' => $quote->validity_start,
            'validity_end' => $quote->validity_end,
        ]);

        $inland->syncProviders($vdata['provider_id']);
        
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

        $totals = $autoinland->inland_totals()->first();

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

    public function updatePdfOptions(Request $request, QuoteV2 $quote, $port_id)
    {
        $totals = AutomaticInlandTotal::where([['quote_id',$quote->id],['port_id',$port_id]])->get();
        foreach($totals as $total){

            $total->update(['pdf_options'=>$request->input('pdf_options')]);
            $id = $request->input('pdf_options')['groupId'];
            
            foreach($total->inlands_lcl as $inland){
                InlandLocalChargeLclGroup::where('automatic_inland_lcl_id', $inland->id)->delete();
                if(!is_array($id)){
                    InlandLocalChargeLclGroup::create(['automatic_inland_lcl_id'=>$inland->id,'local_charge_quote_lcl_id'=>$id]);
                }
            }
        }
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

    public function harbors(QuoteV2 $quote)
    {
        $rates = $quote->rates_v2()->get();

        $origin_ports = [];
        $destination_ports = [];

        foreach($rates as $rate){
            $origin = $rate->origin_port()->first();
            $destination = $rate->destination_port()->first();
            
            if($origin->count()!=0){
                array_push($origin_ports,$origin);
            }
            if($destination->count()!=0){
                array_push($destination_ports,$destination);
            }
        }
        
        $ports_sorted = [];
        
        if(count($origin_ports)!=0){
            foreach($origin_ports as $port){
                $inlands = AutomaticInlandLclAir::where([['quote_id',$quote->id],['port_id',$port->id]])->get();
                $clearPort = [
                    "name"=>$port->display_name,
                    "id"=>$port->id,
                    "type"=>"Origin",
                    "code"=>$port->code
                ];
                if(count($inlands)!=0){
                    array_unshift($ports_sorted,$clearPort);
                }else{
                    array_push($ports_sorted,$clearPort);
                }
            }
        }
        
        if(count($destination_ports)!=0){
            foreach($destination_ports as $port){
                $inlands = AutomaticInlandLclAir::where([['quote_id',$quote->id],['port_id',$port->id]])->get();
                $clearPort = [
                    "name"=>$port->display_name,
                    "id"=>$port->id,
                    "type"=>"Destination",
                    "code"=>$port->code
                ];
                if(count($inlands)!=0){
                    array_unshift($ports_sorted,$clearPort);
                }else{
                    array_push($ports_sorted,$clearPort);
                }
            }
        }


        return $ports_sorted;
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

    public function deleteFull(QuoteV2 $quote, $combo)
    {
        $combo_array = explode(';',$combo);
        
        $address = $combo_array[0];

        $port_id = $combo_array[1];
        
        $inland_address = InlandAddress::where([['quote_id',$quote->id],['port_id',$port_id],['address',$address]])->first();

        $inland_address->delete();

        $quote->updateAddresses();

        $quote->updatePdfOptions('exchangeRates');

        return response()->json(null, 204); 
    }
}
