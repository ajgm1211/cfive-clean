<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\GlobalCharge;
use App\Carrier;
use App\Harbor;
use App\Rate;
use App\Currency;
use App\CalculationType;
use App\Surcharge;
use App\GlobalCharPort;
use App\GlobalCharCarrier;
class GlobalChargesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $global =  GlobalCharge::whereHas('user', function($q)
                                          {
                                              $q->where('user_id', '=', Auth::user()->id);
                                          })->with('globalcharport.ports','GlobalCharCarrier.carrier')->get();


        $objcarrier = new Carrier();
        $objharbor = new Harbor();
        $objcurrency = new Currency();
        $objcalculation = new CalculationType();
        $objsurcharge = new Surcharge();
        $harbor = $objharbor->all()->pluck('name','id');
        $carrier = $objcarrier->all()->pluck('name','id');
        $currency = $objcurrency->all()->pluck('alphacode','id');
        $calculationT = $objcalculation->all()->pluck('name','id');
        $surcharge = $objsurcharge->where('user_id','=',Auth::user()->id)->pluck('name','id');

        return view('globalcharges/index', compact('global','carrier','harbor','currency','calculationT','surcharge'));
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
        //dd($request);
        $detailscharges = $request->input('type');
        $contador = 1;
        foreach($detailscharges as $key2 => $value)
        {

            // verificar si esto puede ser mas seguro

            if(!empty($request->input('ammount.'.$key2))) {
                $global = new GlobalCharge();
                $global->surcharge_id = $request->input('type.'.$key2);
                $global->changetype = $request->input('changetype.'.$key2);
                $global->calculationtype_id = $request->input('calculationtype.'.$key2);
                $global->ammount = $request->input('ammount.'.$key2);
                $global->currency_id = $request->input('localcurrency_id.'.$key2);
                $global->user_id = Auth::user()->id; 
                $global->save();

                // Detalles de puertos y carriers
                //$totalCarrier = count($request->input('localcarrier'.$contador));
                //$totalport =  count($request->input('port_id'.$contador));
                $detailport = $request->input('port_id'.$contador);
                $detailcarrier = $request->input('localcarrier'.$contador);


                foreach($detailcarrier as $c => $value)
                {
                    $detailcarrier = new GlobalCharCarrier();
                    $detailcarrier->carrier_id =$request->input('localcarrier'.$contador.'.'.$c);
                    $detailcarrier->globalcharge()->associate($global);
                    $detailcarrier->save();
                }
                foreach($detailport as $p => $value)
                {
                    $detailport = new GlobalCharPort();
                    $detailport->port = $request->input('port_id'.$contador.'.'.$p);
                    $detailport->globalcharge()->associate($global);
                    $detailport->save();
                }
                $contador++;
            }

        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully add this contract.');

        return redirect()->action('GlobalChargesController@index');
    }


    public function updateGlobalChar(Request $request, $id)
    {


        $global = GlobalCharge::find($id);
        $global->surcharge_id = $request->input('surcharge_id');
        $global->changetype = $request->input('changetype');
        $global->calculationtype_id = $request->input('calculationtype_id');
        $global->ammount = $request->input('ammount');
        $global->currency_id = $request->input('currency_id');


        $port = $request->input('port');
        $carrier = $request->input('carrier_id');
        $deleteCarrier = GlobalCharCarrier::where("globalcharge_id",$id);
        $deleteCarrier->delete();
        $deletePort = GlobalCharPort::where("globalcharge_id",$id);
        $deletePort->delete();
        foreach($port as $key2)
        {
            $detailport = new GlobalCharPort();
            $detailport->port = $key2;
            $detailport->globalcharge_id = $id;
            $detailport->save();
        }
        foreach($carrier as $key)
        {
            $detailcarrier = new GlobalCharCarrier();
            $detailcarrier->carrier_id = $key;
            $detailcarrier->globalcharge_id = $id;
            $detailcarrier->save();
        }

        $global->update();
        echo $request;

    }
    public function destroyGlobalCharges($id)
    {

        $global = GlobalCharge::find($id);
        $global->delete();

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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
