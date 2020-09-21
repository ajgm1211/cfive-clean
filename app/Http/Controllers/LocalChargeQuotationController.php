<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\QuoteV2;
use App\AutomaticRate;
use App\Charge;
use App\ChargeLclAir;
use App\Harbor;
use App\LocalChargeQuote;
use App\SaleTermCharge;
use App\SaleTermV3;

class LocalChargeQuotationController extends Controller
{
    /**
     * harbors
     *
     * @param  mixed $request
     * @return void
     */
    public function harbors(Request $request)
    {

        $quote = QuoteV2::with('origin_harbor', 'destination_harbor')->where('id', $request->quote_id)->first();

        $origin_ports = $quote->origin_harbor->map(function ($value) use ($quote) {
            $value['type'] = 1;
            $value['quote_id'] = $quote->id;
            return $value->only(['id', 'display_name', 'type', 'quote_id']);
        });

        $destination_ports = $quote->destination_harbor->map(function ($value) use ($quote) {
            $value['type'] = 2;
            $value['quote_id'] = $quote->id;
            return $value->only(['id', 'display_name', 'type', 'quote_id']);
        });

        $harbors = $origin_ports->merge($destination_ports)->unique();

        $collection = $harbors->values()->all();

        return $collection;
    }

    /**
     * saleterms
     *
     * @param  mixed $request
     * @return void
     */
    public function saleterms(Request $request)
    {

        $saleterms = SaleTermV3::select('id', 'name')->where(['port_id' => $request->port_id, 'group_container_id' => $request->type, 'type_id' => $request->type_route])->get();

        return $saleterms;
    }

    /**
     * charges
     *
     * @param  mixed $request
     * @return void
     */
    public function charges(Request $request)
    {

        $charges = SaleTermCharge::where('sale_term_id', $request->id)->with('calculation_type', 'currency', 'sale_term_code')->get();

        return $charges;
    }

    /**
     * localcharges
     *
     * @param  mixed $request
     * @return void
     */
    public function localcharges(Request $request)
    {
        switch ($request->type) {
            case 1:
                return $this->localChargesOrigin($request->quote_id, $request->port_id);
                break;
            case 2:
                return $this->localChargesDestination($request->quote_id, $request->port_id);
                break;
        }
    }

    /**
     * localChargesOrigin
     *
     * @param  mixed $quote_id
     * @param  mixed $port_id
     * @return void
     */
    public function localChargesOrigin($quote_id, $port_id)
    {
        $charges = Charge::select('*', 'amount as price', 'markups as markup')->where('type_id', 1)->whereHas('automatic_rate', function ($q) use ($port_id, $quote_id) {
            $q->where('origin_port_id', $port_id)->where('quote_id', $quote_id);
        })->with('currency', 'surcharge', 'calculation_type', 'automatic_rate.carrier')->get();

        $port = Harbor::with('country')->find($port_id);

        $data = compact(
            'charges',
            'port'
        );

        return $data;
    }

    /**
     * localChargesDestination
     *
     * @param  mixed $quote_id
     * @param  mixed $port_id
     * @return void
     */
    public function localChargesDestination($quote_id, $port_id)
    {
        $charges = Charge::select('*', 'amount as price', 'markups as markup')->where('type_id', 2)->whereHas('automatic_rate', function ($q) use ($port_id, $quote_id) {
            $q->where('destination_port_id', $port_id)->where('quote_id', $quote_id);
        })->with('currency', 'surcharge', 'calculation_type', 'automatic_rate.carrier')->get();

        $port = Harbor::with('country')->find($port_id);

        $data = compact(
            'charges',
            'port'
        );

        return $data;
    }

    public function store(Request $request)
    {

        $ids = $request->params['ids'];

        foreach ($ids as $id) {
            $localcharge = Charge::findOrFail($id);

            $price = json_decode($localcharge->amount);
            $profit = json_decode($localcharge->markups);

            $local_charge = LocalChargeQuote::create([
                'price' => $price,
                'profit' => $profit,
                'surcharge_id' => $localcharge->surcharge_id,
                'calculation_type_id' => $localcharge->calculation_type_id,
                'currency_id' => $localcharge->currency_id,
                'port_id' => $request->params['port_id'],
                'quote_id' => $request->params['quote_id'],
                'type_id' => $request->params['type_id'],
            ]);

            $local_charge->sumarize();
        }

        $local_charge_quote = LocalChargeQuote::where([
            'quote_id' => $request->params['quote_id'], 'type_id' => $request->params['type_id'],
            'port_id' => $request->params['port_id']
        ])->with('surcharge', 'calculation_type', 'currency')->get();

        return $local_charge_quote;
    }

    public function storedCharges(Request $request)
    {
        $local_charge_quotes = LocalChargeQuote::where([
            'quote_id' => $request->quote_id, 'type_id' => $request->type_id,
            'port_id' => $request->port_id
        ])->with('surcharge', 'calculation_type', 'currency')->get();

        return $local_charge_quotes;
    }

    public function destroy($id)
    {
        LocalChargeQuote::where('id', $id)->delete();

        return 'OK';
    }
}
