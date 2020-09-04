<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\QuoteV2;
use App\AutomaticRate;
use App\Charge;
use App\ChargeLclAir;
use App\Harbor;
use App\SaleTermV3;

class LocalChargeQuotationController extends Controller
{
    public function harbors(Request $request)
    {

        $quote = QuoteV2::with('origin_harbor', 'destination_harbor')->where('id', $request->quote_id)->first();

        $origin_ports = $quote->origin_harbor->map(function ($value) {
            return $value->only(['id', 'display_name']);
        });

        $destination_ports = $quote->destination_harbor->map(function ($value) {
            return $value->only(['id', 'display_name']);
        });

        $harbors = $origin_ports->merge($destination_ports)->unique();

        $collection = $harbors->values()->all();

        return $collection;
    }

    public function saleterms(Request $request)
    {

        $saleterms = SaleTermV3::select('id', 'name')->where('port_id', $request->port_id)->get();

        return $saleterms;
    }
}
