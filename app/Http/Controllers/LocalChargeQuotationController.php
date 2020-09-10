<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\QuoteV2;
use App\AutomaticRate;
use App\Charge;
use App\ChargeLclAir;
use App\Harbor;
use App\SaleTermCharge;
use App\SaleTermV3;

class LocalChargeQuotationController extends Controller
{
    public function harbors(Request $request)
    {

        $quote = QuoteV2::with('origin_harbor', 'destination_harbor')->where('id', $request->quote_id)->first();

        $origin_ports = $quote->origin_harbor->map(function ($value) {
            $value['type'] = 1;
            return $value->only(['id', 'display_name', 'type']);
        });

        $destination_ports = $quote->destination_harbor->map(function ($value, $index) {
            $value['type'] = 2;
            return $value->only(['id', 'display_name', 'type']);
        });

        $harbors = $origin_ports->merge($destination_ports)->unique();

        $collection = $harbors->values()->all();

        return $collection;
    }

    public function saleterms(Request $request)
    {

        $saleterms = SaleTermV3::select('id', 'name')->where(['port_id' => $request->port_id, 'group_container_id' => $request->type, 'type_id' => $request->type_route])->get();

        return $saleterms;
    }

    public function charges(Request $request)
    {

        $charges = SaleTermCharge::where('sale_term_id', $request->id)->with('calculation_type', 'currency', 'sale_term_code')->get();

        return $charges;
    }
}
