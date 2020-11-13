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

class LocalChargeQuotationLclController extends Controller
{
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
        $selectedCharges = $request->selectedCharges;

        foreach ($selectedCharges as $localcharge) {

            $charge = $localcharge['surcharge']['name'];

            $local_charge_lcl = LocalChargeQuoteLcl::create([
                'price' => $localcharge['price_per_unit'],
                'units' => $localcharge['units'],
                'profit' => $localcharge['markup'],
                'total' => ((float)$localcharge['price_per_unit'] * (float)$localcharge['units']) + (float)$localcharge['markup'],
                'charge' => $charge,
                'surcharge_id' => $localcharge['surcharge_id'],
                'calculation_type_id' => $localcharge['calculation_type_id'],
                'currency_id' => $localcharge['currency_id'],
                'port_id' => $request->port_id,
                'quote_id' => $request->quote_id,
                'type_id' => $request->type_id,
            ]);

            $local_charge_lcl->totalize();
        }

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
}
