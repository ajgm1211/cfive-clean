<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleTermCharge;
use App\Http\Resources\SaleTermChargeResource;
use App\SaleTermCharge;
use App\SaleTermV3;
use Illuminate\Http\Request;

class SaleTermChargeController extends Controller
{
    function list(Request $request, SaleTermV3 $saleterm)
    {
        $results = SaleTermCharge::filterBySaleTerm($saleterm->id)->filter($request);
        return SaleTermChargeResource::collection($results);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SaleTermCharge  $charge
     * @return \Illuminate\Http\Response
     */
    public function retrieve(SaleTermCharge $charge)
    {
        return new SaleTermChargeResource($charge);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSaleTermCharge $request, $charge)
    {

        $charge = SaleTermCharge::create([
            'calculation_type_id' => $request->calculation_type,
            'amount' => $request->amount,
            'currency_id' => $request->currency,
            'sale_term_id' => $charge,
            'sale_term_code_id' => $request->sale_term_code
        ]);

        $charge->jsonTotal();

        return new SaleTermChargeResource($charge);
    }

    /**
     * Duplicate the specified resource.
     *
     * @param  \App\SaleTermCharge $charge
     * @return \Illuminate\Http\Response
     */
    public function duplicate(SaleTermCharge $charge)
    {
        $new_charge = $charge->duplicate();

        return new SaleTermChargeResource($new_charge);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\SaleTermCharge $charge
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SaleTermCharge $charge)
    {
        $data = $request->validate([
            'calculation_type' => 'required',
            'amount' => 'required',
            'currency' => 'required',
            'sale_term_code' => 'required',
        ]);
        
        $charge->update([
            'calculation_type_id' => $data['calculation_type'],
            'amount' => $data['amount'],
            'currency_id' => $data['currency'],
            'sale_term_code_id' => $data['sale_term_code'],
        ]);

        return new SaleTermChargeResource($charge);
    }

    /**
     * Remove specific the resource from DB.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        SaleTermCharge::find($id)->delete();

        return response()->json(null, 204);
    }
}
