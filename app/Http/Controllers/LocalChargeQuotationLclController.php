<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Traits\QuoteV2Trait;
use App\QuoteV2;
use App\AutomaticRate;
use App\Charge;
use App\ChargeLclAir;
use App\Harbor;
use App\Http\Requests\StoreLocalChargeLclQuote;
use App\LocalChargeQuote;
use App\LocalChargeQuoteLcl;
use App\LocalChargeQuoteLclTotal;

class LocalChargeQuotationLclController extends Controller
{
    use QuoteV2Trait;
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
     * get previous stored local charges
     *
     * @param  mixed $request
     * @return void
     */
    public function storedCharges(Request $request)
    {
        $local_charge_quotes = LocalChargeQuoteLcl::where([
            'quote_id' => $request->quote_id, 'type_id' => $request->type_id,
            'port_id' => $request->port_id
        ])->with('surcharge', 'calculation_type', 'currency')->get();

        return $local_charge_quotes;
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
        $charges = ChargeLclAir::select('*')->where('type_id', 1)->whereHas('automatic_rate', function ($q) use ($port_id, $quote_id) {
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
        $charges = ChargeLclAir::select('*')->where('type_id', 2)->whereHas('automatic_rate', function ($q) use ($port_id, $quote_id) {
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
        $request->validate([
            'selectedCharges.*.surcharge_id' => 'required',
            'selectedCharges.*.surcharge' => 'required',
            'selectedCharges.*.calculation_type_id' => 'required',
            'selectedCharges.*.markup' => 'sometimes',
            'selectedCharges.*.units' => 'required',
            'selectedCharges.*.price_per_unit' => 'required',
            'selectedCharges.*.provider_name' => 'sometimes',
            'selectedCharges.*.currency_id' => 'required'
        ]);

        foreach ($request->selectedCharges as $localcharge) {
            if(!array_key_exists("automatic_rate_id", $localcharge)){
                $this->storeInCharges($request->quote_id, $request->type_id, $request->port_id, $localcharge);
            }
            $this->storeInLocalCharges($localcharge, $request->port_id, $request->quote_id, $request->type_id);
        }

        /**foreach ($selectedCharges['selectedCharges'] as $localcharge) {
            
            $charge = $localcharge['surcharge']['name'];

            $units = $localcharge['units'] == 0 ? 1:$localcharge['units'];

            $local_charge_lcl = LocalChargeQuoteLcl::create([
                'price' => (((float)$localcharge['price_per_unit'] * (float)$units) + (float)$localcharge['markup']) / (float)$units,
                'units' => $units,
                'profit' => $localcharge['markup'],
                'total' => ((float)$localcharge['price_per_unit'] * (float)$units) + (float)$localcharge['markup'],
                'charge' => $charge,
                'surcharge_id' => $localcharge['surcharge_id'],
                'calculation_type_id' => $localcharge['calculation_type_id'],
                'provider_name' => $localcharge['provider_name'],
                'currency_id' => $localcharge['currency_id'],
                'port_id' => $request->port_id,
                'quote_id' => $request->quote_id,
                'type_id' => $request->type_id,
            ]);

            $local_charge_lcl->totalize();
        }**/

        return response()->json(['success' => 'Ok']);
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

                $local_charge_quote = LocalChargeQuoteLcl::findOrFail($id);

                $local_charge_quote->delete();

                $local_charge_quote->totalize();
                break;
            case 2:
                ChargeLclAir::destroy($id);
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

        $total = LocalChargeQuoteLclTotal::where(['quote_id' => $request->quote_id, 'port_id' => $request->port_id])->with('currency')->first();

        return $total;
    }

        /**
     * store lcl's charge info
     *
     * @param  mixed $request
     * @return void
     */
    public function storeCharge(StoreLocalChargeLclQuote $request)
    {
        $request->validated();
        
        $quote = QuoteV2::findOrFail($request->quote_id);

        $rate = $quote->getRate($request->type_id, $request->port_id, $request->charges['carrier']['id']);

        ChargeLclAir::create([
            'automatic_rate_id' => $rate->id,
            'calculation_type_id' => $request->charges['calculation_type']['id'],
            'provider_name' => $request->charges['carrier']['name'],
            'currency_id' => $request->charges['currency']['id'],
            'surcharge_id' => $request->charges['surcharge']['id'],
            'type_id' => $request->type_id,
            'units' => $request->charges['units'],
            'price_per_unit' => $request->charges['price'],
            'markup' => $request->charges['profit'],
        ]);

        $local_charge_lcl = LocalChargeQuoteLcl::create([
            'price' => (((float)$request->charges['price'] * (float)$request->charges['units']) + (float)$request->charges['profit']) / (float)$request->charges['units'],
            'units' => $request->charges['units'],
            'profit' => $request->charges['profit'],
            'total' => ((float)$request->charges['price'] * (float)$request->charges['units']) + (float)$request->charges['profit'],
            'charge' => $request->charges['surcharge']['name'],
            'surcharge_id' => $request->charges['surcharge']['id'],
            'calculation_type_id' => $request->charges['calculation_type']['id'],
            'provider_name' => $request->charges['carrier']['name'],
            'currency_id' => $request->charges['currency']['id'],
            'port_id' => $request->port_id,
            'quote_id' => $request->quote_id,
            'type_id' => $request->type_id,
        ]);

        $local_charge_lcl->totalize();
    }

    public function storeInCharges($quote, $type, $port, $data)
    {
        $quote = QuoteV2::findOrFail($quote);

        $carrier_id = $data['carrier']['id'] ?? $data['automatic_rate']['carrier']['name'] ?? null;

        $rate = $quote->getRate($type, $port, $carrier_id);

        ChargeLclAir::create([
            'automatic_rate_id' => $rate->id,
            'calculation_type_id' => $data['calculation_type_id'],
            'currency_id' => $data['currency_id'],
            'surcharge_id' => $data['surcharge_id'],
            'type_id' => $type,
            'units' => $data['units'],
            'price_per_unit' => $data['price'],
            'markup' => $data['profit'],
            'provider_name' => $data['provider_name'] ?? $data['automatic_rate']['carrier']['name'] ?? null,
        ]);
    }

    public function storeInLocalCharges($localcharge, $port, $quote, $type)
    {
        $charge = $localcharge['surcharge']['name'];

        if (!empty($localcharge['sale_codes'])) {
            $charge = $localcharge['sale_codes']['name'];
            $units = $localcharge['units'] == 0 ? 1:$localcharge['units'];
            $previous_charge = LocalChargeQuoteLcl::where([
                'charge' => $charge,
                'port_id' => $port,
                //'calculation_type_id' => $localcharge['calculation_type_id'],
                'currency_id' => $localcharge['currency_id'],
                'quote_id' => $quote,
                'type_id' => $type
            ])->first();

            if ($previous_charge) {
                $previous_charge->groupingCharges($localcharge);
                //$previous_charge->sumarize();
                $previous_charge->totalize();
            } else {
                $local_charge = LocalChargeQuoteLcl::create([
                    'price' => (((float)$localcharge['price_per_unit'] * (float)$units) + (float)$localcharge['markup']) / (float)$units,
                    'units' => $units,
                    'profit' => $localcharge['markup'],
                    'total' => ((float)$localcharge['price_per_unit'] * (float)$units) + (float)$localcharge['markup'],
                    'charge' => $charge,
                    'surcharge_id' => $localcharge['surcharge_id'],
                    'calculation_type_id' => $localcharge['calculation_type_id'],
                    'provider_name' => $localcharge['provider_name'] ?? $localcharge['automatic_rate']['carrier']['name'] ?? null,
                    'currency_id' => $localcharge['currency_id'],
                    'port_id' => $port,
                    'quote_id' => $quote,
                    'type_id' => $type,
                ]);

                //$local_charge->sumarize();
                $local_charge->totalize();
            }
        } else {

            $charge = $localcharge['surcharge']['name'];

            $units = $localcharge['units'] == 0 ? 1:$localcharge['units'];

            $local_charge = LocalChargeQuoteLcl::create([
                'price' => (((float)$localcharge['price_per_unit'] * (float)$units) + (float)$localcharge['markup']) / (float)$units,
                'units' => $units,
                'profit' => $localcharge['markup'],
                'total' => ((float)$localcharge['price_per_unit'] * (float)$units) + (float)$localcharge['markup'],
                'charge' => $charge,
                'surcharge_id' => $localcharge['surcharge_id'],
                'calculation_type_id' => $localcharge['calculation_type_id'],
                'provider_name' => $localcharge['provider_name'] ?? $localcharge['automatic_rate']['carrier']['name'] ?? null,
                'currency_id' => $localcharge['currency_id'],
                'port_id' => $port,
                'quote_id' => $quote,
                'type_id' => $type,
            ]);

            //$local_charge->sumarize();
            $local_charge->totalize();
        }
    }
}
