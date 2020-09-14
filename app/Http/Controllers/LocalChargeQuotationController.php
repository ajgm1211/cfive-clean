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

    public function localcharges(Request $request)
    {
        switch ($request->type) {
            case 1:
                return $this->localChargesOrigin($request->port_id);
                break;
            case 2:
                return $this->localChargesDestination($request->port_id);
                break;
        }
    }

    public function localChargesOrigin($port_id)
    {
        $charges = Charge::where('type_id', 1)->whereHas('automatic_rate', function ($q) use ($port_id) {
            $q->where('origin_port_id', $port_id);
        })->with('currency', 'surcharge', 'calculation_type', 'automatic_rate.carrier')->get();

        $port = Harbor::with('country')->find($port_id);

        $data = compact(
            'charges',
            'port'
        );

        return $data;
    }

    public function localChargesDestination($port_id)
    {
        $charges = Charge::where('type_id', 2)->whereHas('automatic_rate', function ($q) use ($port_id) {
            $q->where('destination_port_id', $port_id);
        })->with('currency', 'surcharge', 'calculation_type', 'automatic_rate.carrier')->get();

        $port = Harbor::with('country')->find($port_id);

        $data = compact(
            'charges',
            'port'
        );

        return $data;
    }
}
