<?php

namespace App\Http\Controllers;

use App\Company;
use App\Country;
use App\DestinationAmmount;
use App\DestinationAmount;
use App\FreightAmmount;
use App\OriginAmount;
use App\Price;
use App\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Collection as Collection;
use App\Contract;
use App\Rate;
use App\Harbor;
use App\LocalCharge;
use App\LocalCharCarrier;
use App\LocalCharPort;
use App\GlobalCharge;
use App\GlobalCharPort;
use App\GlobalCharCarrier;
use GoogleMaps;
use App\Inland;
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



        /*$data = Contract::with('rates')->get();
        return view('quotation/index', ['arreglo' => $data]);*/ 
        $quotes = Quote::all();
        $companies = Company::all()->pluck('business_name','id');
        $harbors = Harbor::all()->pluck('business_name','id');
        $countries = Country::all()->pluck('name','id');
        return view('quotes/index', ['companies' => $companies,'quotes'=>$quotes,'countries'=>$countries,'harbors'=>$harbors]);

        /*
        $objharbor = new Harbor();
        $harbor = $objharbor->all()->pluck('name','id');
        return view('quotation/new', compact('harbor'));*/

    }
    public function automatic(){

        $quotes = Quote::all();
        $companies = Company::all()->pluck('business_name','id');
        $harbors = Harbor::all()->pluck('name','id');
        $countries = Country::all()->pluck('name','id');
        $prices = Price::all()->pluck('name','id');
        return view('quotation/new2', ['companies' => $companies,'quotes'=>$quotes,'countries'=>$countries,'harbors'=>$harbors,'prices'=>$prices]);
    }
    public function listRate(Request $request)
    {
        $origin_port = $request->input('originport');
        $destiny_port = $request->input('destinyport');
        $delivery_type = $request->input('delivery_type');

        if($delivery_type == "2" || $delivery_type == "4" ){
            $inlands = Inland::whereHas('inlandports', function($q) use($destiny_port) {
                $q->whereIn('port', $destiny_port);
            })->with('inlandports.ports','inlanddetails.currency')->get();

            foreach($inlands as $inlandsValue){

                foreach($inlandsValue->inlandports as $ports){
                    $monto = 0;
                    $temporal = 0;
                    if (in_array($ports->ports->id, $destiny_port )) {
                        $origin =  $ports->ports->coordinates;
                        $destination = $request->input('destination_address');
                        $response = GoogleMaps::load('directions')
                            ->setParam([
                                'origin'          => $origin,
                                'destination'     => $destination,
                                'mode' => 'driving' ,
                                'language' => 'es',

                            ])->get();
                        $var = json_decode($response);
                        foreach($var->routes as $resp) {
                            foreach($resp->legs as $dist) {
                                $km = explode(" ",$dist->distance->text);

                                foreach($inlandsValue->inlanddetails as $details){
                                    if($details->type == 'twuenty' && $request->input('twuenty') != "0"){
                                        $distancia = intval($km[0]);  
                                        if( $distancia >= $details->lower && $distancia  <= $details->upper){
                                            $monto += $request->input('twuenty') * $details->ammount;

                                            //  echo $monto;
                                            //echo '<br>';

                                        }
                                    }
                                    if($details->type == 'forty' && $request->input('forty') != "0"){
                                        $distancia = intval($km[0]);  
                                        if( $distancia >= $details->lower && $distancia  <= $details->upper){
                                            $monto += $request->input('forty') * $details->ammount;

                                        }
                                    }
                                    if($details->type == 'fortyhc' && $request->input('fortyhc') != "0"){
                                        $distancia = intval($km[0]);  
                                        if( $distancia >= $details->lower && $distancia  <= $details->upper){
                                            $monto += $request->input('fortyhc') * $details->ammount;

                                        }
                                    }

                                }
                                if($monto > 0){
                                    $data[] = array("prov_id" => $inlandsValue->id ,"provider" => $inlandsValue->provider ,"port_id" => $ports->ports->id,"port_name" =>  $ports->ports->name ,"km" => $km[0] , "monto" => $monto ,'type' => 'Destiny Port To Door');
                                }
                            }
                        }
                    } // if ports
                }// foreach ports
            }//foreach inlands
            if(!empty($data)){
                $collection = Collection::make($data);
                // dd($collection); //  completo 
                $inlandDestiny = $collection->groupBy('port_id')->map(function($item){
                    $test = $item->where('monto', $item->min('monto'))->first(); 
                    return $test;
                });
                // dd($inlandDestiny); // filtraor por el minimo 
            }

        }
        if($delivery_type == "3" || $delivery_type == "4" ){
            $inlands = Inland::whereHas('inlandports', function($q) use($origin_port) {
                $q->whereIn('port', $origin_port);
            })->with('inlandports.ports','inlanddetails.currency')->get();

            foreach($inlands as $inlandsValue){

                foreach($inlandsValue->inlandports as $ports){
                    $monto = 0;
                    $temporal = 0;
                    if (in_array($ports->ports->id, $origin_port )) {
                        $origin = $request->input('origin_address');
                        $destination =  $ports->ports->coordinates;
                        $response = GoogleMaps::load('directions')
                            ->setParam([
                                'origin'          => $origin,
                                'destination'     => $destination,
                                'mode' => 'driving' ,
                                'language' => 'es',

                            ])->get();
                        $var = json_decode($response);
                        foreach($var->routes as $resp) {
                            foreach($resp->legs as $dist) {
                                $km = explode(" ",$dist->distance->text);

                                foreach($inlandsValue->inlanddetails as $details){
                                    if($details->type == 'twuenty' && $request->input('twuenty') != "0"){
                                        $distancia = intval($km[0]);  
                                        if( $distancia >= $details->lower && $distancia  <= $details->upper){
                                            $monto += $request->input('twuenty') * $details->ammount;
                                        }
                                    }
                                    if($details->type == 'forty' && $request->input('forty') != "0"){
                                        $distancia = intval($km[0]);  
                                        if( $distancia >= $details->lower && $distancia  <= $details->upper){
                                            $monto += $request->input('forty') * $details->ammount;

                                        }
                                    }
                                    if($details->type == 'fortyhc' && $request->input('fortyhc') != "0"){
                                        $distancia = intval($km[0]);  
                                        if( $distancia >= $details->lower && $distancia  <= $details->upper){
                                            $monto += $request->input('fortyhc') * $details->ammount;

                                        }
                                    }

                                }
                                if($monto > 0){
                                    $dataOrig[] = array("prov_id" => $inlandsValue->id ,"provider" => $inlandsValue->provider ,"port_id" => $ports->ports->id,"port_name" =>  $ports->ports->name ,"km" => $km[0] , "monto" => $monto ,'type' => 'Origin Port To Door');
                                }
                            }
                        }
                    } // if ports
                }// foreach ports
            }//foreach inlands
            
      
            if(!empty($dataOrig)){
                $collectionOrig = Collection::make($dataOrig);
                // dd($collection); //  completo 
                $inlandOrigin= $collectionOrig->groupBy('port_id')->map(function($item){
                    $test = $item->where('monto', $item->min('monto'))->first(); 
                    return $test;
                });
                // dd($inlandOrigin); // filtraor por el minimo 
            }

        }
        
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
        return view('quotation/index', compact('harbor','arreglo','formulario','sub','localTwuenty','localForty','localFortyHc','shipment','globalTwuenty','globalForty','globalFortyHc','globalshipment','inlandDestiny','inlandOrigin'));

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

        if($input['origin_ammount_charge']!=[null]) {
            $origin_ammount_charge = array_values( array_filter($input['origin_ammount_charge']) );
            $origin_ammount_detail = array_values( array_filter($input['origin_ammount_detail']) );
            $origin_ammount_price_per_unit = array_values( array_filter($input['origin_price_per_unit']) );
            $origin_ammount_currency = array_values( array_filter($input['origin_ammount_currency']) );
            $origin_total_units = array_values( array_filter($input['origin_ammount_units']) );
            $origin_total_ammount = array_values( array_filter($input['origin_total_ammount']) );
            $origin_total_ammount_2 = array_values( array_filter($input['origin_total_ammount_2']) );
            $origin_total_markup = array_values( array_filter($input['$origin_total_markup']) );
            foreach ($origin_ammount_charge as $key => $item) {
                $origin_ammount = new OriginAmount();
                $origin_ammount->quote_id = $quote->id;
                if ((isset($origin_ammount_description[$key])) && (!empty($origin_ammount_description[$key]))) {
                    $origin_ammount->charge = $origin_ammount_description[$key];
                }
                if ((isset($origin_ammount_detail[$key])) && (!empty($origin_ammount_detail[$key]))) {
                    $origin_ammount->detail = $origin_ammount_detail[$key];
                }
                if ((isset($origin_total_units[$key])) && (!empty($origin_total_units[$key]))) {
                    $origin_ammount->units = $origin_total_units[$key];
                }
                if ((isset($origin_total_markup[$key])) && (!empty($origin_total_markup[$key]))) {
                    $origin_ammount->units = $origin_total_markup[$key];
                }
                if ((isset($origin_ammount_arr[$key])) && ($origin_ammount_price_per_unit[$key]) != '') {
                    $origin_ammount->price_per_unit = $origin_ammount_price_per_unit[$key];
                    $origin_ammount->currency_id = $origin_ammount_currency[$key];
                }
                if ((isset($origin_total_ammount[$key])) && ($origin_total_ammount[$key] != '')) {
                    $origin_ammount->total_ammount = $origin_total_ammount[$key];
                }
                if ((isset($origin_total_ammount_2[$key])) && ($origin_total_ammount_2[$key] != '')) {
                    $origin_ammount->total_ammount_2 = $origin_total_ammount_2[$key];
                }
                $origin_ammount->save();
            }
        }

        if($input['freight_ammount_charge']!=[null]) {
            $freight_ammount_charge = array_values( array_filter($input['freight_ammount_charge']) );
            $freight_ammount_detail = array_values( array_filter($input['freight_ammount_detail']) );
            $freight_ammount_price_per_unit = array_values( array_filter($input['freight_price_per_unit']) );
            $freight_ammount_currency = array_values( array_filter($input['freight_ammount_currency']) );
            $freight_total_units = array_values( array_filter($input['freight_ammount_units']) );
            $freight_total_ammount = array_values( array_filter($input['freight_total_ammount']) );
            $freight_total_ammount_2 = array_values( array_filter($input['freight_total_ammount_2']) );
            $freight_total_markup = array_values( array_filter($input['$freight_total_markup']) );
            foreach ($freight_ammount_charge as $key => $item) {
                $freight_ammount = new FreightAmmount();
                $freight_ammount->quote_id = $quote->id;
                if ((isset($freight_ammount_description[$key])) && (!empty($freight_ammount_description[$key]))) {
                    $freight_ammount->charge = $freight_ammount_description[$key];
                }
                if ((isset($freight_ammount_detail[$key])) && (!empty($freight_ammount_detail[$key]))) {
                    $freight_ammount->detail = $freight_ammount_detail[$key];
                }
                if ((isset($freight_total_units[$key])) && (!empty($freight_total_units[$key]))) {
                    $freight_ammount->units = $freight_total_units[$key];
                }
                if ((isset($freight_total_markup[$key])) && (!empty($freight_total_markup[$key]))) {
                    $freight_ammount->units = $freight_total_markup[$key];
                }
                if ((isset($freight_ammount_arr[$key])) && ($freight_ammount_price_per_unit[$key]) != '') {
                    $freight_ammount->price_per_unit = $freight_ammount_price_per_unit[$key];
                    $freight_ammount->currency_id = $freight_ammount_currency[$key];
                }
                if ((isset($freight_total_ammount[$key])) && ($freight_total_ammount[$key] != '')) {
                    $freight_ammount->total_ammount = $freight_total_ammount[$key];
                }
                if ((isset($freight_total_ammount_2[$key])) && ($freight_total_ammount_2[$key] != '')) {
                    $freight_ammount->total_ammount_2 = $freight_total_ammount_2[$key];
                }
                $freight_ammount->save();
            }
        }

        if($input['destination_ammount_charge']!=[null]) {
            $destination_ammount_charge = array_values( array_filter($input['destination_ammount_charge']) );
            $destination_ammount_detail = array_values( array_filter($input['destination_ammount_detail']) );
            $destination_ammount_price_per_unit = array_values( array_filter($input['destination_price_per_unit']) );
            $destination_ammount_currency = array_values( array_filter($input['destination_ammount_currency']) );
            $destination_ammount_units = array_values( array_filter($input['destination_ammount_units']) );
            $destination_ammount_markup = array_values( array_filter($input['destination_ammount_markup']) );
            $destination_total_ammount = array_values( array_filter($input['destination_total_ammount']) );
            $destination_total_ammount_2 = array_values( array_filter($input['destination_total_ammount_2']) );
            foreach ($destination_ammount_charge as $key => $item) {
                $destination_ammount = new DestinationAmmount();
                $destination_ammount->quote_id = $quote->id;
                if ((isset($destination_ammount_charge[$key])) && (!empty($destination_ammount_charge[$key]))) {
                    $destination_ammount->charge = $destination_ammount_charge[$key];
                }
                if ((isset($destination_ammount_detail[$key])) && (!empty($destination_ammount_detail[$key]))) {
                    $destination_ammount->detail = $destination_ammount_detail[$key];
                }
                if ((isset($destination_ammount_units[$key])) && (!empty($destination_ammount_units[$key]))) {
                    $destination_ammount->units = $destination_ammount_units[$key];
                }
                if ((isset($destination_ammount_markup[$key])) && (!empty($destination_ammount_markup[$key]))) {
                    $destination_ammount->markup = $destination_ammount_markup[$key];
                }
                if ((isset($destination_ammount_price_per_unit[$key])) && (!empty($destination_ammount_price_per_unit[$key]))) {
                    $destination_ammount->price_per_unit = $destination_ammount_price_per_unit[$key];
                    $destination_ammount->currency_id = $destination_ammount_currency[$key];
                }
                if ((isset($destination_total_ammount[$key])) && (!empty($destination_total_ammount[$key]))) {
                    $destination_ammount->total_ammount = $destination_total_ammount[$key];
                }
                if ((isset($destination_total_ammount_2[$key])) && (!empty($destination_total_ammount_2[$key]))) {
                    $destination_ammount->total_ammount_2 = $destination_total_ammount_2[$key];
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
