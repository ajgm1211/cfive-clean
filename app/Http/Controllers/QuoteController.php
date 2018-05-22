<?php

namespace App\Http\Controllers;

use App\Company;
use App\Country;
use App\DestinationAmmount;
use App\DestinationAmount;
use App\FreightAmmount;
use App\Price;
use App\Quote;

use Illuminate\Http\Request;
use App\Contract;
use App\Rate;
use App\Harbor;
use App\LocalCharge;
use App\LocalCharCarrier;
use App\LocalCharPort;
use Illuminate\Support\Facades\Input;

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
        $quotes = Quote::all();
        $companies = Company::all()->pluck('business_name','id');
        $harbors = Harbor::all()->pluck('business_name','id');
        $countries = Country::all()->pluck('name','id');
        return view('quotes/index', ['companies' => $companies,'quotes'=>$quotes,'countries'=>$countries,'harbors'=>$harbors]);

        /*$objharbor = new Harbor();
        $harbor = $objharbor->all()->pluck('name','id');
        return view('quotation/new', compact('harbor'));*/

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

                $localTwuenty = LocalCharge::where('calculationtype_id','=','2')->orWhere('calculationtype_id','=','4')->orWhere('calculationtype_id','=','5')->whereHas('localcharcarriers', function($q) use($carrier) {
                    $q->whereIn('carrier_id', $carrier);
                })->whereHas('localcharports', function($q) {
                    $q->whereIn('port', [1,2]);

                })->with('localcharports.ports','localcharcarriers.carrier','currency')->get();

            }
            if(!empty($request->input('forty'))) {
                $subtotal = ($data->forty * $request->input('forty')) + $subtotal;

                $carrierForty[] = $data->carrier_id;
                $localForty = LocalCharge::where('calculationtype_id','=','1')->orWhere('calculationtype_id','=','4')->orWhere('calculationtype_id','=','5')->whereHas('localcharcarriers', function($q) use($carrierForty) {
                    $q->whereIn('carrier_id', $carrierForty);
                })->whereHas('localcharports', function($q) {
                    $q->whereIn('port', [1,2]);
                })->with('localcharports.ports')->get();

            }
            if(!empty($request->input('fortyhc'))) {
                $subtotal = ($data->fortyhc * $request->input('fortyhc')) + $subtotal;
                $sub[] =   $subtotal;

                $carrierFortyHc[] = $data->carrier_id;
                $localFortyHc = LocalCharge::where('calculationtype_id','=','3')->orWhere('calculationtype_id','=','4')->orWhere('calculationtype_id','=','5')->whereHas('localcharcarriers', function($q) use($carrierFortyHc) {
                    $q->whereIn('carrier_id', $carrierFortyHc);
                })->whereHas('localcharports', function($q) {
                    $q->whereIn('port', [1,2]);
                })->with('localcharports.ports')->get();
            }

        }

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
        $quotes = Quote::all();
        $companies = Company::all()->pluck('business_name','id');
        $harbors = Harbor::all()->pluck('name','id');
        $countries = Country::all()->pluck('name','id');
        $prices = Price::all()->pluck('name','id');
        return view('quotes/add', ['companies' => $companies,'quotes'=>$quotes,'countries'=>$countries,'harbors'=>$harbors,'prices'=>$prices]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $input = Input::all();
        $request->request->add(['owner' => \Auth::id()]);
        $quote=Quote::create($request->all());

        if($input['freight_ammount_description']!=[null]) {
            $freight_ammount_description = array_values( array_filter($input['freight_ammount_description']) );
            $freight_ammount_detail = array_values( array_filter($input['freight_ammount_detail']) );
            $freight_ammount_arr = array_values( array_filter($input['freight_ammount']) );
            $freight_ammount_currency = array_values( array_filter($input['freight_ammount_currency']) );
            $freight_total_ammount = array_values( array_filter($input['freight_total_ammount']) );
            foreach ($freight_ammount_description as $key => $item) {
                $freight_ammount = new FreightAmmount();
                $freight_ammount->quote_id = $quote->id;
                if ((isset($freight_ammount_description[$key])) && (!empty($freight_ammount_description[$key]))) {
                    $freight_ammount->cost = $freight_ammount_description[$key];
                }
                if ((isset($freight_ammount_detail[$key])) && (!empty($freight_ammount_detail[$key]))) {
                    $freight_ammount->detail = $freight_ammount_detail[$key];
                }
                if ((isset($freight_ammount_arr[$key])) && ($freight_ammount_arr[$key]) != '') {
                    $freight_ammount->ammount = $freight_ammount_arr[$key];
                    $freight_ammount->currency_id = $freight_ammount_currency[$key];
                }
                if ((isset($freight_total_ammount[$key])) && ($freight_total_ammount[$key] != '')) {
                    $freight_ammount->total_ammount = $freight_total_ammount[$key];
                }
                $freight_ammount->save();
            }
        }

        if($input['destination_ammount_description']!=[null]) {
            $destination_ammount_description = array_values( array_filter($input['destination_ammount_description']) );
            $destination_ammount_detail = array_values( array_filter($input['destination_ammount_detail']) );
            $destination_ammount_arr = array_values( array_filter($input['destination_ammount']) );
            $destination_ammount_currency = array_values( array_filter($input['destination_ammount_currency']) );
            $destination_total_ammount = array_values( array_filter($input['destination_total_ammount']) );
            foreach ($destination_ammount_description as $key => $item) {
                $destination_ammount = new DestinationAmmount();
                $destination_ammount->quote_id = $quote->id;
                if ((isset($destination_ammount_description[$key])) && (!empty($destination_ammount_description[$key]))) {
                    $destination_ammount->cost = $destination_ammount_description[$key];
                }
                if ((isset($destination_ammount_detail[$key])) && (!empty($destination_ammount_detail[$key]))) {
                    $destination_ammount->detail = $destination_ammount_detail[$key];
                }
                if ((isset($destination_ammount_arr[$key])) && (!empty($destination_ammount_arr[$key]))) {
                    $destination_ammount->ammount = $destination_ammount_arr[$key];
                    $destination_ammount->currency_id = $destination_ammount_currency[$key];
                }
                if ((isset($destination_total_ammount[$key])) && (!empty($destination_total_ammount[$key]))) {
                    $destination_ammount->total_ammount = $destination_total_ammount[$key];
                }
                $destination_ammount->save();
            }
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Register completed successfully!');
        return redirect()->route('quotes.index');
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
        $quote = Quote::find($id);
        $quote->update($request->all());

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Register updated successfully!');
        return redirect()->route('quotes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy(Request $request,$id)
    {
        $quote = Quote::find($id);
        $quote->delete();

        return $quote;
    }

    public function getHarborName($id)
    {
        $harbor = Harbor::findOrFail($id);
        return $harbor;
    }
}
