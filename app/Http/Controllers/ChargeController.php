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
        $data = $request->validate([
            'surcharge_id' => 'nullable',
            'calculation_type_id' => 'required',
            'currency_id' => 'required',
        ]);

        //ADD AND PREPARE DATA FROM CONTAINERS

        $charge = Charge::create([
            'automatic_rate_id' => $autorate->id,
            'calculation_type_id' => $data['calculation_type_id'],
            'currency_id' => $data['currency_id'],
            'type_id' => 3
        ]);

    }
}
