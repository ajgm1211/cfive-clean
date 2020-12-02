<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\QuoteV2;
use App\AutomaticRate;
use App\Charge;
use App\ChargeLclAir;
use App\Harbor;
use App\Http\Requests\StoreLocalChargeQuote;
use App\Http\Resources\SaleTermChargeResource;
use App\LocalChargeQuote;
use App\LocalChargeQuoteLcl;
use App\LocalChargeQuoteLclTotal;
use App\LocalChargeQuoteTotal;
use App\SaleTermCharge;
use App\SaleTermCode;
use App\SaleTermV3;
use App\Surcharge;
use App\Container;
use App\Http\Requests\StoreLocalChargeLclQuote;

class LocalChargeQuotationController extends Controller
{
    /**
     * get harbors
     *
     * @param  mixed $request
     * @return void
     */
    public function harbors(QuoteV2 $quote)
    {
        $origin_ports = $quote->origin_harbor->map(function ($value) use ($quote) {
            $value['type'] = 1;
            $value['quote_id'] = $quote->id;
            $value['charges'] = LocalChargeQuote::where(['quote_id' => $quote->id, 'port_id' => $value->id])->count();
            return $value->only(['id', 'display_name', 'type', 'quote_id', 'charges']);
        });

        $destination_ports = $quote->destination_harbor->map(function ($value) use ($quote) {
            $value['type'] = 2;
            $value['quote_id'] = $quote->id;
            $value['charges'] = LocalChargeQuote::where(['quote_id' => $quote->id, 'port_id' => $value->id])->count();
            return $value->only(['id', 'display_name', 'type', 'quote_id', 'charges']);
        });

        $harbors = $origin_ports->merge($destination_ports)->unique();

        $harbors = $harbors->sortByDesc('charges');

        $collection = $harbors->values()->all();

        return $collection;
    }

    /**
     * carriers
     *
     * @param  mixed $request
     * @return void
     */
    public function carriers(QuoteV2 $quote)
    {

        $carriers = $quote->carrier->unique('id')->values();

        return $carriers;
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
        $saleterms = SaleTermV3::select('id', 'name')->where(['port_id' => $request->port_id, 'group_container_id' => $request->equipment, 'type_id' => $request->type, 'company_user_id' => \Auth::user()->company_user_id])->get();

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
            case "1":
                return $this->localChargesOrigin($request->quote_id, $request->port_id);
                break;
            case "2":
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

        $selectedCharges = $request->selectedCharges;

        foreach ($selectedCharges as $localcharge) {

            $charge = $localcharge['surcharge']['name'];

            if (!empty($localcharge['sale_codes'])) {
                $charge = $localcharge['sale_codes']['name'];

                $previous_charge = LocalChargeQuote::where([
                    'charge' => $charge,
                    'port_id' => $request->port_id,
                    'calculation_type_id' => $localcharge['calculation_type_id'],
                    'currency_id' => $localcharge['currency_id'],
                    'quote_id' => $request->quote_id,
                    'type_id' => $request->type_id
                ])->first();

                if ($previous_charge) {
                    $previous_charge->groupingCharges($localcharge);
                    $previous_charge->sumarize();
                    $previous_charge->totalize();
                } else {
                    $local_charge = LocalChargeQuote::create([
                        'price' => $localcharge['price'],
                        'profit' => $localcharge['markup'],
                        'charge' => $charge,
                        'surcharge_id' => $localcharge['surcharge_id'],
                        'calculation_type_id' => $localcharge['calculation_type_id'],
                        'currency_id' => $localcharge['currency_id'],
                        'port_id' => $request->port_id,
                        'quote_id' => $request->quote_id,
                        'type_id' => $request->type_id,
                    ]);

                    $local_charge->sumarize();
                    $local_charge->totalize();
                }
            } else {
                $local_charge = LocalChargeQuote::create([
                    'price' => $localcharge['price'],
                    'profit' => $localcharge['markup'],
                    'charge' => $charge,
                    'surcharge_id' => $localcharge['surcharge_id'],
                    'calculation_type_id' => $localcharge['calculation_type_id'],
                    'currency_id' => $localcharge['currency_id'],
                    'port_id' => $request->port_id,
                    'quote_id' => $request->quote_id,
                    'type_id' => $request->type_id,
                ]);

                $local_charge->sumarize();
                $local_charge->totalize();
            }
        }

        return response()->json(['success' => 'Ok']);
    }

    /**
     * store new charges from sale terms
     *
     * @param  mixed $request
     * @return void
     */
    public function storeChargeSaleTerm(Request $request)
    {
        LocalChargeQuote::where(['sale_term_v3_id' => $request->params['id'], 'quote_id' => $request->params['quote_id']])->delete();

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
                'sale_term_v3_id' => $request->params['id'],
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

        switch ($request->type) {
            case 'FCL':
                $local_charge_quotes = LocalChargeQuote::where([
                    'quote_id' => $request->quote_id, 'type_id' => $request->type_id,
                    'port_id' => $request->port_id
                ])->with('surcharge', 'calculation_type', 'currency')->get();
                break;
            case 'LCL':
                $local_charge_quotes = LocalChargeQuoteLcl::where([
                    'quote_id' => $request->quote_id, 'type_id' => $request->type_id,
                    'port_id' => $request->port_id
                ])->with('surcharge', 'calculation_type', 'currency')->get();
                break;
        }

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
                if (strpos($index, 'total') !== false) {
                    $local_charge->$index = floatvalue($request->data);
                } else {
                    $local_charge->$index = $request->data;
                }
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
                if (empty($price)) {
                    $price[$index] = $request->data;
                } else {
                    foreach ($price as $key => $amount) {
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
                if (empty($profit)) {
                    $profit[$index] = $request->data;
                } else {
                    foreach ($profit as $key => $markup) {
                        $profit->$index = $request->data;
                    }
                }
                $local_charge->markups = json_encode($profit);
                $local_charge->update();
                break;
            case 5:
                $index = $request->index;
                $total = LocalChargeQuoteTotal::findOrFail($id);
                $total->$index = $request->data;
                $total->update();

                $total->totalize();
                break;
            case 6:
                $index = $request->index;
                $total = LocalChargeQuoteLcl::findOrFail($id);
                $total->$index = $request->data;
                $total->update();

                $total->totalLcl($index);
                $total->totalize();
                break;
            case 7:
                $index = $request->index;
                $total = LocalChargeQuoteLclTotal::findOrFail($id);
                $total->$index = $request->data;
                $total->update();

                $total->totalize();
                break;
            case 8:
                $index = $request->index;
                $total = ChargeLclAir::findOrFail($id);
                $total->$index = $request->data;
                $total->update();
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
    public function updateRemarks(Request $request, QuoteV2 $quote)
    {

        $quote->update([
            'localcharge_remarks' => $request->data
        ]);

        return response()->json(['success' => 'Ok']);
    }

    public function twoWaysStore(Request $request)
    {
        if ($request->quote_type == 'FCL') {
            $this->storeCharge($request);
        } else {
            $this->storeChargeLcl($request);
        }
    }

    /**
     * store lcl's charge info
     *
     * @param  mixed $request
     * @return void
     */
    public function storeChargeLcl(StoreLocalChargeLclQuote $request)
    {
        $quote = QuoteV2::findOrFail($request->quote_id);

        $rate = $quote->getRate($request->type_id, $request->port_id, $request->charges['carrier']['id']);

        ChargeLclAir::create([
            'automatic_rate_id' => $rate->id,
            'calculation_type_id' => $request->charges['calculation_type']['id'],
            'currency_id' => $request->charges['currency']['id'],
            'surcharge_id' => $request->charges['surcharge']['id'],
            'type_id' => $request->type_id,
            'units' => $request->charges['units'],
            'price_per_unit' => $request->charges['price'],
            'markup' => $request->charges['profit'],
        ]);

        LocalChargeQuoteLcl::create([
            'price' => (((float)$request->charges['price'] * (float)$request->charges['units']) + (float)$request->charges['profit']) / (float)$request->charges['units'],
            'units' => $request->charges['units'],
            'profit' => $request->charges['profit'],
            'total' => ((float)$request->charges['price'] * (float)$request->charges['units']) + (float)$request->charges['profit'],
            'charge' => $request->charges['surcharge']['name'],
            'surcharge_id' => $request->charges['surcharge']['id'],
            'calculation_type_id' => $request->charges['calculation_type']['id'],
            'currency_id' => $request->charges['currency']['id'],
            'port_id' => $request->port_id,
            'quote_id' => $request->quote_id,
            'type_id' => $request->type_id,
        ]);
    }

    /**
     * store charge's info
     *
     * @param  mixed $request
     * @return void
     */
    public function storeCharge(StoreLocalChargeQuote $request)
    {

        $request->validated();

        $quote = QuoteV2::findOrFail($request->quote_id);

        $rate = $quote->getRate($request->type_id, $request->port_id, $request->charges['carrier']['id']);

        Charge::create([
            'automatic_rate_id' => $rate->id,
            'calculation_type_id' => $request->charges['calculation_type']['id'],
            'currency_id' => $request->charges['currency']['id'],
            'surcharge_id' => $request->charges['surcharge']['id'],
            'type_id' => $request->type_id,
            'amount' => $this->removeCommas($request->charges['price']),
            'markups' => $this->removeCommas($request->charges['markup'])
        ]);

        $localcharge = $request->charges;

        $charge = $localcharge['surcharge']['name'];

        if (!empty($localcharge['sale_codes'])) {
            $charge = $localcharge['sale_codes']['name'];

            $previous_charge = LocalChargeQuote::where([
                'charge' => $charge,
                'port_id' => $request->port_id,
                'calculation_type_id' => $localcharge['calculation_type']['id'],
                'currency_id' => $localcharge['currency']['id'],
                'quote_id' => $request->quote_id,
                'type_id' => $request->type_id
            ])->first();

            if ($previous_charge) {

                $previous_charge->groupingCharges($localcharge);
                $previous_charge->sumarize();
                $previous_charge->totalize();
            } else {
                $local_charge = LocalChargeQuote::create([
                    'price' => $localcharge['price'],
                    'profit' => $localcharge['markup'],
                    'charge' => $charge,
                    'surcharge_id' => $localcharge['surcharge']['id'],
                    'calculation_type_id' => $localcharge['calculation_type']['id'],
                    'currency_id' => $localcharge['currency']['id'],
                    'port_id' => $request->port_id,
                    'quote_id' => $request->quote_id,
                    'type_id' => $request->type_id,
                ]);

                $local_charge->sumarize();
                $local_charge->totalize();
            }
        } else {
            $local_charge = LocalChargeQuote::create([
                'price' => $localcharge['price'],
                'profit' => $localcharge['markup'],
                'charge' => $charge,
                'surcharge_id' => $localcharge['surcharge']['id'],
                'calculation_type_id' => $localcharge['calculation_type']['id'],
                'currency_id' => $localcharge['currency']['id'],
                'port_id' => $request->port_id,
                'quote_id' => $request->quote_id,
                'type_id' => $request->type_id,
            ]);

            $local_charge->sumarize();
            $local_charge->totalize();
        }

        return response()->json(['success' => 'Ok']);
    }

    public function removeCommas($array)
    {
        $containers = Container::all();

        if ($array != null || $array != '') {
            foreach ($array as $k => $amount) {
                foreach ($containers as $container) {
                    if ($k == 'c' . $container->code) {
                        $array['c' . $container->code] = floatvalue($amount);
                    } else if ($k == 'm' . $container->code) {
                        $array['m' . $container->code] = floatvalue($amount);
                    }
                }
            }
        }

        return json_encode($array);
    }
}
