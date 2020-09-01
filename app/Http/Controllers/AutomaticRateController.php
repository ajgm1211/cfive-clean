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

    public function store(Request $request, AutomaticRate $autorate, QuoteV2 $quote)
    {
        dd($request);

        /**$data = $this->validateData($request, $autorate);//change to direct validation?

        $prepared_data = $this->prepareData($data, $autorate);//change to direct preparation?

        $rate = AutomaticRate::create($prepared_data);//direct creation

        return new AutomaticRateResource($rate);**/
    }
 
    public function update(Request $request, AutomaticRate $autorate)
    {
        dd($request);

        /**$data = $this->validateData($request, $contract);//direct validation

        $prepared_data = $this->prepareData($data, $contract);

        $rate->update($prepared_data);

        return new OceanFreightResource($rate);

        $charge = $autorate->charge()->get();
        
        $data = $this->validate([
            'currency' => 'required'
        ]);
        
        $freight = Charge::create([
            'automatic_rate_id' => $quote->id   
            ]);

        if($charges != null && $charges != []){
            foreach($charges as $charge){
                $freight = Charge::create([
                    'automatic_rate_id' => $autorate->id,
                    'type_id' => '3',///WHY
                    'calculation_type_id' => '5',//ALSO WHY
                    'currency_id' => $autorate->currency_id,
                ]);
                    

                $transit_time = $info_decoded->transit_time;
                $via = $info_decoded->via;
        
                $this->amount = $rates;
                $this->markups = $markups;
                $this->currency_id = $info_decoded->currency->id;
                $this->total = $rates;
                $this->save();
            }

        }**/
    }

    public function retrieve(QuoteV2 $quote, AutomaticRate $autorate)
    {
        return new AutomaticRateResource($autorate);
    }
}
