<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SaleTermV2;
use App\SaleTermV2Charge;
use App\Charge;
use App\AutomaticRate;
use App\Container;
use App\User;

class SaleTermV2Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $sale_term = SaleTermV2::create($request->all());

        $company_user = User::where('id', \Auth::id())->first();

        $sale_charge = new SaleTermV2Charge();
        $sale_charge->sale_term_id = $sale_term->id;
        $sale_charge->currency_id = $company_user->companyUser->currency_id;
        $sale_charge->save();

        $notification = array(
            'toastr' => 'Record saved successfully!',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Update charges by saleterms
     * @param Request $request 
     * @return array json
     */
    public function updateSaleCharges(Request $request)
    {
        $charge = SaleTermV2Charge::find($request->pk);
        $name = explode("->", $request->name);
        if (strpos($request->name, '->') == true) {
            if ($name[0] == 'rate') {
                $array = json_decode($charge->rate, true);
            }
            $field = (string) $name[0];
            $array[$name[1]] = $request->value;
            $array = json_encode($array);
            $charge->$field = $array;
        } else {
            $name = $request->name;
            $charge->$name = $request->value;
        }
        $charge->update();
        return response()->json(['success' => 'Ok']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        SaleTermV2::where('id', $id)->delete();
        return response()->json(['message' => 'Ok']);
    }

    public function destroyCharge($id)
    {
        SaleTermV2Charge::where('id', $id)->delete();
        return response()->json(['message' => 'Ok']);
    }

    public function storeSaleCharge(Request $request)
    {

        $containers = Container::all();

        $merge_amounts = array();

        foreach ($containers as $value) {
            ${'array_amount_' . $value->code} = array();
  
            foreach ($request->equipments as $key => $equipment) {
                if (($key == 'c'.$value->code) && $equipment != null) {
                    ${'array_amount_' . $value->code} = array('c'.$value->code => $equipment);
                }
            }
            $merge_amounts = array_merge($merge_amounts, ${'array_amount_' . $value->code});
        }

        $sale_charge = new SaleTermV2Charge();
        $sale_charge->sale_term_id = $request->sale_term_id;
        $sale_charge->charge = $request->charge;
        $sale_charge->detail = $request->detail;
        $sale_charge->rate = json_encode($merge_amounts);
        $sale_charge->amount = $request->amount;
        $sale_charge->units = $request->units;
        $sale_charge->total = $request->total;
        $sale_charge->currency_id = $request->currency_id;
        $sale_charge->save();

        return response()->json(['message' => 'Ok']);
    }
}
