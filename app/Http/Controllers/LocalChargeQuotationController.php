<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\QuoteV2;
use App\AutomaticRate;
use App\Charge;
use App\ChargeLclAir;
use App\Harbor;
use App\Http\Resources\SaleTermChargeResource;
use App\LocalChargeQuote;
use App\LocalChargeQuoteTotal;
use App\SaleTermCharge;
use App\SaleTermCode;
use App\SaleTermV3;
use App\Surcharge;

class LocalChargeQuotationController extends Controller
{
    /**
     * get harbors
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
     * data
     *
     * @return void
     */
    public function data()
    {
        $surcharges = Surcharge::where('company_user_id', \Auth::user()->company_user_id)->get();
        $sale_codes = SaleTermCode::where('company_user_id', \Auth::user()->company_user_id)->get();

        $surcharges = $surcharges->map(function ($value) {
            $value['type'] = 'surcharge';
            return $value->only(['name', 'type']);
        });

        $sale_codes = $sale_codes->map(function ($value) {
            $value['type'] = 'salecode';
            return $value->only(['name', 'type']);
        });

        $merged = $surcharges->merge($sale_codes);

        return $merged->all();
    }

    /**
     * get sale terms' templates
     *
     * @param  mixed $request
     * @return void
     */
    public function saleterms(Request $request)
    {
        $saleterms = SaleTermV3::select('id', 'name')->where(['port_id' => $request->port_id, 'group_container_id' => $request->equipment, 'type_id' => $request->type])->get();

        return $saleterms;
    }

    /**
     * get sale terms' charges
     *
     * @param  mixed $request
     * @return void
     */
    public function salecharges(Request $request)
    {
        $charges = SaleTermCharge::where('sale_term_id', $request->id)->with('calculation_type', 'currency', 'sale_term_code')->get();

        return $charges;
    }

    /**
     * get local charges
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
     * get local charges in origin
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
     * get local charges in destiny
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

    /**
     * store new charges
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {

        $ids = $request->ids;

        foreach ($ids as $key => $id) {

            $localcharge = Charge::findOrFail($id);

            $price = json_decode($localcharge->amount);
            $profit = json_decode($localcharge->markups);

            $charge = $localcharge->surcharge->name;

            if (!empty($request->sale_codes[$key])) {
                $charge = $request->sale_codes[$key]['name'];
            }

            $local_charge = LocalChargeQuote::create([
                'price' => $price,
                'profit' => $profit,
                'charge' => $charge,
                'surcharge_id' => $localcharge->surcharge_id,
                'calculation_type_id' => $localcharge->calculation_type_id,
                'currency_id' => $localcharge->currency_id,
                'port_id' => $request->port_id,
                'quote_id' => $request->quote_id,
                'type_id' => $request->type_id,
            ]);

            $local_charge->sumarize();
            $local_charge->totalize();
        }

        $local_charge_quote = LocalChargeQuote::where([
            'quote_id' => $request->quote_id, 'type_id' => $request->type_id,
            'port_id' => $request->port_id
        ])->with('surcharge', 'calculation_type', 'currency')->get();

        return $local_charge_quote;
    }

    /**
     * store new charges from sale terms
     *
     * @param  mixed $request
     * @return void
     */
    public function storeChargeSaleTerm(Request $request)
    {
        $sale_charges = SaleTermCharge::where('sale_term_id', $request->params['id'])->get();

        foreach ($sale_charges as $sale_charge) {

            $local_charge = LocalChargeQuote::create([
                'price' => $sale_charge->total,
                'profit' => [],
                'charge' => $sale_charge->sale_term_code->name,
                'calculation_type_id' => $sale_charge->calculation_type_id,
                'currency_id' => $sale_charge->currency_id,
                'port_id' => $request->params['port_id'],
                'quote_id' => $request->params['quote_id'],
                'type_id' => $request->params['type_id'],
            ]);

            $local_charge->sumarize();
            $local_charge->totalize();
        }

        $local_charge_quote = LocalChargeQuote::where([
            'quote_id' => $request->params['quote_id'], 'type_id' => $request->params['type_id'],
            'port_id' => $request->params['port_id']
        ])->with('surcharge', 'calculation_type', 'currency')->get();

        return $local_charge_quote;
    }

    /**
     * get previous stored local charges
     *
     * @param  mixed $request
     * @return void
     */
    public function storedCharges(Request $request)
    {
        $local_charge_quotes = LocalChargeQuote::where([
            'quote_id' => $request->quote_id, 'type_id' => $request->type_id,
            'port_id' => $request->port_id
        ])->with('surcharge', 'calculation_type', 'currency')->get();

        return $local_charge_quotes;
    }

    /**
     * destroy a local charge
     *
     * @param  mixed $id
     * @return void
     */
    public function destroy(Request $request, $id)
    {
        switch ($request->type) {
            case 1:
                $local_charge_quote = LocalChargeQuote::findOrFail($id);

                $local_charge_quote->delete();

                $local_charge_quote->totalize();
                break;
            case 2:
                Charge::destroy($id);
                break;
        }

        return response()->json(['success' => 'Ok']);
    }

    /**
     * getTotal
     *
     * @param  mixed $request
     * @return void
     */
    public function getTotal(Request $request)
    {

        $total = LocalChargeQuoteTotal::where(['quote_id' => $request->quote_id, 'port_id' => $request->port_id])->with('currency')->first();

        return $total;
    }

    /**
     * get localcharges' remarks
     *
     * @param  mixed $id
     * @return void
     */
    public function getRemarks($id)
    {

        $remarks = QuoteV2::select('localcharge_remarks')->findOrFail($id);

        return $remarks;
    }

    /**
     * update charges and localcharges
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function update(Request $request, $id)
    {

        switch ($request->type) {
            case 1:
                $index = $request->index;

                $local_charge = LocalChargeQuote::findOrFail($id);
                $local_charge->$index = $request->data;
                $local_charge->update();

                $local_charge->totalize();
                break;
            case 2:
                $index = $request->index;
                $local_charge = Charge::findOrFail($id);
                $local_charge->$index = $request->data;
                $local_charge->update();
                break;
            case 3:
                $index = $request->index;
                $local_charge = Charge::findOrFail($id);
                $price = json_decode($local_charge->amount);
                foreach ($price as $key => $amount) {
                    if ($key == $index) {
                        $price->$index = $request->data;
                    }
                }
                $local_charge->amount = json_encode($price);
                $local_charge->update();
                break;
            case 4:
                $index = $request->index;
                $local_charge = Charge::findOrFail($id);
                $profit = json_decode($local_charge->markups);
                foreach ($profit as $key => $markup) {
                    if ($key == $index) {
                        $profit->$index = $request->data;
                    }
                }
                $local_charge->markups = json_encode($profit);
                $local_charge->update();
                break;
        }

        return response()->json(['success' => 'Ok']);
    }

    /**
     * update localcharges' remarks
     *
     * @param  mixed $request
     * @param  mixed $quote_id
     * @return void
     */
    public function updateRemarks(Request $request, $quote_id)
    {

        QuoteV2::findOrFail($quote_id)->update([
            'localcharge_remarks' => $request->data
        ]);

        return response()->json(['success' => 'Ok']);
    }

    /**
     * store charge's info
     *
     * @param  mixed $request
     * @return void
     */
    public function storeCharge(Request $request)
    {

        $quote = QuoteV2::findOrFail($request->quote_id);

        Charge::create([
            'automatic_rate_id' => $request->rate_id,
            'calculation_type_id' => $request->charges['calculation_type']['id'],
            'currency_id' => $request->charges['currency']['id'],
            'surcharge_id' => $request->charges['surcharge']['id'],
            'type_id' => 1,
            'amount' => json_encode($request->charges['price']),
            'markups' => json_encode($request->charges['markup'])
        ]);

        return response()->json(['success' => 'Ok']);
    }
}
