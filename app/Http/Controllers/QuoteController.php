<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contract;
use App\Rate;
use App\Harbor;
use App\LocalCharge;
use App\LocalCharCarrier;
use App\LocalCharPort;
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
            if(!empty($request->input('twuenty'))) {
                $subtotal = ($data->twuenty * $request->input('twuenty')) + $subtotal;
                $carrier[] = $data->carrier_id;

                $localTwuenty = LocalCharge::where('calculationtype_id','=','2')->whereHas('localcharcarriers', function($q) use($carrier) {
                    $q->whereIn('carrier_id', $carrier);
                })->whereHas('localcharports', function($q) {
                    $q->whereIn('port', [1,2]);

                })->with('localcharports.ports','localcharcarriers.carrier','currency')->get();

            }
            if(!empty($request->input('forty'))) {
                $subtotal = ($data->forty * $request->input('forty')) + $subtotal;

                $carrierForty[] = $data->carrier_id;                
                $localForty = LocalCharge::where('calculationtype_id','=','1')->whereHas('localcharcarriers', function($q) use($carrierForty) {
                    $q->whereIn('carrier_id', $carrierForty);
                })->whereHas('localcharports', function($q) {
                    $q->whereIn('port', [1,2]);
                })->with('localcharports.ports')->get();

            }
            if(!empty($request->input('fortyhc'))) {
                $subtotal = ($data->fortyhc * $request->input('fortyhc')) + $subtotal;
                $sub[] =   $subtotal;

                $carrierFortyHc[] = $data->carrier_id;                
                $localFortyHc = LocalCharge::where('calculationtype_id','=','3')->whereHas('localcharcarriers', function($q) use($carrierFortyHc) {
                    $q->whereIn('carrier_id', $carrierFortyHc);
                })->whereHas('localcharports', function($q) {
                    $q->whereIn('port', [1,2]);
                })->with('localcharports.ports')->get();
            }

        }
       // dd($localFortyHc);
        $objharbor = new Harbor();
        $harbor = $objharbor->all()->pluck('name','id');
        return view('quotation/index', compact('harbor','arreglo','formulario','sub','localTwuenty','localForty','localFortyHc'));


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
