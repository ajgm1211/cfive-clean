<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contract;
use App\Rate;
use App\Harbor;
use App\LocalCharge;
use App\LocalCharCarrier;
use App\LocalCharPort;
use App\GlobalCharge;
use App\GlobalCharPort;
use App\GlobalCharCarrier;
class QuoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        /* $data = Contract::with('rates')->get();
        return view('quotation/index', ['arreglo' => $data]);*/

        $objharbor = new Harbor();
        $harbor = $objharbor->all()->pluck('name','id');
        return view('quotation/new', compact('harbor'));


    }
    public function listRate(Request $request)
    {


        $origin_port = $request->input('originport');
        $destiny_port = $request->input('destinyport');


        $date =  $request->input('date');
        $arreglo = Rate::whereIn('origin_port',$origin_port)->whereIn('destiny_port',$destiny_port)->with('port_origin','port_destiny','contract')->whereHas('contract', function($q) use($date)
                                      {
                                          $q->where('validity', '<=',$date)->where('expire', '>=', $date);

                                      })->get();

        $formulario = $request;

        foreach($arreglo as $data){
            $subtotal = 0;
            $merge = array($data->origin_port,$data->destiny_port);

            if($request->input('twuenty') != "0") {
                $subtotal = ($data->twuenty * $request->input('twuenty')) + $subtotal;
                $carrier[] = $data->carrier_id;

                $localTwuenty = LocalCharge::whereIn('calculationtype_id',[2,4,5])->whereHas('localcharcarriers', function($q) use($carrier) {
                    $q->whereIn('carrier_id', $carrier);
                })->whereHas('localcharports', function($q) use($merge) {
                    $q->whereIn('port', $merge);
                })->with('localcharports.ports','localcharcarriers.carrier','currency')->get();

                // Global charges twuenty 

                $globalTwuenty = GlobalCharge::whereIn('calculationtype_id',[2,4,5])->whereHas('globalcharcarrier', function($q) use($carrier) {
                    $q->whereIn('carrier_id', $carrier);
                })->whereHas('globalcharport', function($q) use($merge) {
                    $q->whereIn('port', $merge);
                })->with('globalcharport.ports','globalcharcarrier.carrier','currency')->get();

            }
            if($request->input('forty') != "0") {
                $subtotal = ($data->forty * $request->input('forty')) + $subtotal;

                $carrierForty[] = $data->carrier_id;                
                $localForty = LocalCharge::whereIn('calculationtype_id',[1,4,5])->whereHas('localcharcarriers', function($q) use($carrierForty) {
                    $q->whereIn('carrier_id', $carrierForty);
                })->whereHas('localcharports', function($q) use($merge) {
                    $q->whereIn('port', $merge);
                })->with('localcharports.ports')->get();

                // Global charges forty 

                $globalForty = GlobalCharge::whereIn('calculationtype_id',[1,4,5])->whereHas('globalcharcarrier', function($q) use($carrierForty) {
                    $q->whereIn('carrier_id', $carrierForty);
                })->whereHas('globalcharport', function($q) use($merge) {
                    $q->whereIn('port', $merge);
                })->with('globalcharport.ports','globalcharcarrier.carrier','currency')->get();

            }
            if($request->input('fortyhc') != "0") {
                $subtotal = ($data->fortyhc * $request->input('fortyhc')) + $subtotal;


                $carrierFortyHc[] = $data->carrier_id;                
                $localFortyHc = LocalCharge::whereIn('calculationtype_id',[3,4,5])->whereHas('localcharcarriers', function($q) use($carrierFortyHc) {
                    $q->whereIn('carrier_id', $carrierFortyHc);
                })->whereHas('localcharports', function($q) use($merge) {
                    $q->whereIn('port', $merge);
                })->with('localcharports.ports')->get();

                // GLobal Charges
                $globalFortyHc = GlobalCharge::whereIn('calculationtype_id',[3,4,5])->whereHas('globalcharcarrier', function($q) use($carrierFortyHc) {
                    $q->whereIn('carrier_id', $carrierFortyHc);
                })->whereHas('globalcharport', function($q) use($merge) {
                    $q->whereIn('port', $merge);
                })->with('globalcharport.ports','globalcharcarrier.carrier','currency')->get();

            }
            // PER SHIPTMENT LOCAL
            $sub[] =   $subtotal;
            $carrierShip[] = $data->carrier_id;
            $shipment = LocalCharge::where('calculationtype_id','=','6')->whereHas('localcharcarriers', function($q) use($carrierShip) {
                $q->whereIn('carrier_id', $carrierShip);
            })->whereHas('localcharports', function($q) use($merge) {
                $q->whereIn('port', $merge);
            })->with('localcharports.ports','localcharcarriers.carrier','currency')->get();

            // PER SHIPMENT GLOBAL 
            $globalshipment = GlobalCharge::where('calculationtype_id','=','6')->whereHas('globalcharcarrier', function($q) use($carrierShip) {
                $q->whereIn('carrier_id', $carrierShip);
            })->whereHas('globalcharport', function($q) use($merge) {
                $q->whereIn('port', $merge);
            })->with('globalcharport.ports','globalcharcarrier.carrier','currency')->get();

        }


        $objharbor = new Harbor();
        $harbor = $objharbor->all()->pluck('name','id');
        return view('quotation/index', compact('harbor','arreglo','formulario','sub','localTwuenty','localForty','localFortyHc','shipment','globalTwuenty','globalForty','globalFortyHc','globalshipment'));


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
        //
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
