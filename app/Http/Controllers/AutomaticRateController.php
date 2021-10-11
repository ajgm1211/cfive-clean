<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AutomaticRate;
use App\QuoteV2;
use App\Charge;
use App\Surcharge;
use App\ChargeLclAir;
use App\CalculationTypeLcl;
use App\AutomaticInlandTotal;
use App\AutomaticRateTotal;
use App\Http\Resources\AutomaticRateResource;
use App\Http\Resources\AutomaticRateTotalResource;
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
        
        $currency = $quote->company_user()->first()->currency()->first();

        $rate = AutomaticRate::create([
            'quote_id' => $quote->id,
            'contract' => '',
            'validity_start' => $quote->validity_start,
            'validity_end' => $quote->validity_end,
            'origin_port_id' => $data['POL'],
            'destination_port_id' => $data['POD'],
            'currency_id' => $currency->id,
            'carrier_id' => $data['carrier'],
        ]);

        $ocean_surcharge = Surcharge::where([['name', 'Ocean Freight'],['company_user_id',null]])->first();
        
        if($quote->type == 'FCL'){
            $freight = Charge::create([
                'automatic_rate_id' => $rate->id,
                'type_id' => '3',
                'surcharge_id' => $ocean_surcharge->id,
                'calculation_type_id' => '5',
                'currency_id' => $rate->currency_id,
            ]);
        }else if($quote->type == 'LCL'){
            $calculationtype_lcl = CalculationTypeLcl::where('name','W/M')->first();

            $freight = ChargeLclAir::create([
                'automatic_rate_id' => $rate->id,
                'type_id' => '3',
                'calculation_type_id' => $calculationtype_lcl->id,
                'surcharge_id' => $ocean_surcharge->id,
                'units' => 1.00,
                'price_per_unit' => 1.00,
                'minimum' => 1.00,
                'total' => 1.00,
                'markup' => 1.00,
                'currency_id' => $rate->currency_id,
            ]);
        }

        $this->storeTotals($quote,$rate);
       
        return new AutomaticRateResource($rate);
    }

    public function storeTotals(QuoteV2 $quote, AutomaticRate $rate){
        
        $totals = $rate->totals()->first();

        $currency = $rate->currency()->first();
        
        if($totals){
            $totals->totalize($currency->id);
        }else{
            $totals = AutomaticRateTotal::create([
                'quote_id' => $quote->id,
                'currency_id' => $currency->id,
                'origin_port_id' => $rate->origin_port_id,
                'destination_port_id' => $rate->destination_port_id,
                'automatic_rate_id' => $rate->id,
                'carrier_id' => $rate->carrier_id,
                'totals' => null,
                'markups' => null                    
            ]);

            $totals->totalize($currency->id);
            
        }
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
                'transit_time' => 'numeric|nullable'
            ]);
            
            foreach($form_keys as $fkey){
                if(!in_array($fkey,$data) && $fkey != 'keys'){
                    $data[$fkey] = $request->input($fkey);
                }
            };

            if(!isset($data['contract'])){
                $data['contract'] = '';
            }

            if(isset($data['exp_date'])){
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

        $totals = $autorate->totals()->first();

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
                    $markups['m'.str_replace('profits_','',$key)] = isDecimal($value,true);
                }else if($quote->type == 'LCL'){
                    $markups[str_replace('profits_','',$key)] = isDecimal($value,true);
                }
            }
        }
            
        if(count($markups) != 0){

            $totals->update(['markups'=>$markups]);
            $autorate->update(['markups'=>$markups]);

            $totals->totalize($request->input('profits_currency'));
        }
    }

    public function retrieve(QuoteV2 $quote, AutomaticRate $autorate)
    {
        return new AutomaticRateResource($autorate);
    }

    public function retrieveTotals(QuoteV2 $quote, AutomaticRate $autorate)
    {
        $totals = $autorate->totals()->first();

        return new AutomaticRateTotalResource($totals);
    }

    public function destroy(AutomaticRate $autorate)
    {
        $quote = $autorate->quotev2()->first();

        $inlandAddressesOrig = $quote->inland_addresses()->where('port_id',$autorate->origin_port_id)->get();

        $inlandAddressesDest = $quote->inland_addresses()->where('port_id',$autorate->destination_port_id)->get();

        if($inlandAddressesOrig){
            foreach($inlandAddressesOrig as $address){
                $address->delete();

            }
        }
        
        if($inlandAddressesDest){
            foreach($inlandAddressesDest as $address){
                $address->delete();
            }
        }

        $totals = $autorate->totals();

        $totals->delete();

        $autorate->delete();

        $automatic_rates = $quote->rates_v2()->get();
        $rates_origin_ports = [];
        $rates_destination_ports = [];

        foreach($automatic_rates as $rate){
            array_push($rates_origin_ports, $rate->origin_port_id);
            array_push($rates_destination_ports, $rate->destination_port_id);
        }

        if($quote->type == "FCL"){
            $localCharges = $quote->local_charges()->get();
            $localChargeTotals = $quote->local_charges_totals()->get();
        }else if($quote->type == "LCL"){
            $localCharges = $quote->local_charges_lcl()->get();
            $localChargeTotals = $quote->local_charges_lcl_totals()->get();
        }

        foreach($localCharges as $charge){
            if($charge->type_id == 1 && !in_array($charge->port_id, $rates_origin_ports)){
                $charge->delete();
            }else if($charge->type_id == 2 && !in_array($charge->port_id, $rates_destination_ports)){
                $charge->delete();
            }
        }

        foreach($localChargeTotals as $chargeTotal){
            if($chargeTotal->type_id == 1 && !in_array($chargeTotal->port_id, $rates_origin_ports)){
                $chargeTotal->delete();
            }else if($chargeTotal->type_id == 2 && !in_array($chargeTotal->port_id, $rates_destination_ports)){
                $chargeTotal->delete();
            }
        }

        $quote->updatePdfOptions('exchangeRates');

        return response()->json(null, 204);
    }
}
