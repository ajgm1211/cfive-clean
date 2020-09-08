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
 
    public function update(Request $request, QuoteV2 $quote, AutomaticRate $autorate)
    {   
        $form_keys = $request->input('keys');

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

    public function retrieve(QuoteV2 $quote, AutomaticRate $autorate)
    {
        return new AutomaticRateResource($autorate);
    }
}
