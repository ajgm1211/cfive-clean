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
}
