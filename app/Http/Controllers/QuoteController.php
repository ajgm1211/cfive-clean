<?php

namespace App\Http\Controllers;

use App\Company;
use App\CompanyPrice;
use App\CompanyUser;
use App\Contact;
use App\Country;
use App\Currency;
use App\DestinationAmmount;
use App\DestinationAmount;
use App\FreightAmmount;
use App\OriginAmmount;
use App\OriginAmount;
use App\Price;
use App\Quote;
use App\User;
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

        $quotes = Quote::all();
        $companies = Company::all()->pluck('business_name','id');
        $harbors = Harbor::all()->pluck('business_name','id');
        $countries = Country::all()->pluck('name','id');
        return view('quotes/index', ['companies' => $companies,'quotes'=>$quotes,'countries'=>$countries,'harbors'=>$harbors]);

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
  /*
        if($delivery_type == "2"){
            $inlands = Inland::whereHas('inlandports', function($q) use($destiny_port) {
                $q->whereIn('port', $destiny_port);
            })->with('inlandports.ports','inlanddetails.currency')->get();

            foreach($inlands as $inlandsValue){
                foreach($inlandsValue->inlandports as $ports){
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
                                $distance[] = array("port_id" => $ports->ports->id,"port_name" =>  $ports->ports->name ,"km" => $km[0] );
                            }
                        }

                    }
                }


                $collection = Collection::make($distance);

                $distancia = "72";

                foreach($inlandsValue->inlanddetails as $details){

                    if($details->type == 'twuenty'){

                        foreach($collection as $key2 =>  $value){
                            echo $value["port_name"];echo "<br>";
                            echo $value["km"];echo "<br>";

                            if($distancia>= $details->lower && $distancia <= $details->upper){
                                echo "im here";
                                $monto = $request->input('twuenty') * $details->ammount;
                                echo "lower ".$details->lower;
                                echo "<br>";
                                echo "up ".$details->upper;
                                echo "<br>"; echo "<br>";

                            }


                        }
                        dd($collection);

                        if($distancia >= $details->lower && $distancia <= $details->upper){

                            $monto = $request->input('twuenty') * $details->ammount;
                            echo "lower ".$details->lower;
                            echo "<br>";
                            echo "up ".$details->upper;
                            echo "<br>"; echo "<br>";

                        }
                    }

                }
                dd($inlands);
            }
        }*/

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
        $quotes = Quote::all();
        $companies = Company::all()->pluck('business_name','id');
        $harbors = Harbor::all()->pluck('name','id');
        $countries = Country::all()->pluck('name','id');
        $prices = Price::all()->pluck('name','id');
        $company_user = User::where('id',\Auth::id())->first();
        $currency_name = Currency::where('id',$company_user->companyUser->currency_id)->first();
        $currencies = Currency::all()->pluck('alphacode','id');
        return view('quotes/add', ['companies' => $companies,'quotes'=>$quotes,'countries'=>$countries,'harbors'=>$harbors,'prices'=>$prices,'company_user'=>$company_user,'currencies'=>$currencies,'currency_name'=>$currency_name]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $quote = Quote::findOrFail($id);
        $companies = Company::all()->pluck('business_name','id');
        $harbors = Harbor::all()->pluck('name','id');
        $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
        $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
        $prices = Price::all()->pluck('name','id');
        $contacts = Contact::where('company_id',$quote->company_id)->pluck('first_name','id');
        $origin_ammounts = OriginAmmount::where('quote_id',$quote->id)->get();
        $freight_ammounts = FreightAmmount::where('quote_id',$quote->id)->get();
        $destination_ammounts = DestinationAmmount::where('quote_id',$quote->id)->get();
        return view('quotes/edit', ['companies' => $companies,'quote'=>$quote,'harbors'=>$harbors,
            'prices'=>$prices,'contacts'=>$contacts,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,
            'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts]);
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
            $origin_total_markup = array_values( array_filter($input['origin_ammount_markup']) );
            foreach ($origin_ammount_charge as $key => $item) {
                $origin_ammount = new OriginAmmount();
                $origin_ammount->quote_id = $quote->id;
                if ((isset($origin_ammount_charge[$key])) && (!empty($origin_ammount_charge[$key]))) {
                    $origin_ammount->charge = $origin_ammount_charge[$key];
                }
                if ((isset($origin_ammount_detail[$key])) && (!empty($origin_ammount_detail[$key]))) {
                    $origin_ammount->detail = $origin_ammount_detail[$key];
                }
                if ((isset($origin_total_units[$key])) && (!empty($origin_total_units[$key]))) {
                    $origin_ammount->units = $origin_total_units[$key];
                }
                if ((isset($origin_total_markup[$key])) && (!empty($origin_total_markup[$key]))) {
                    $origin_ammount->markup = $origin_total_markup[$key];
                }
                if ((isset($origin_ammount_price_per_unit[$key])) && ($origin_ammount_price_per_unit[$key]) != '') {
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
            $freight_total_markup = array_values( array_filter($input['freight_ammount_markup']) );
            foreach ($freight_ammount_charge as $key => $item) {
                $freight_ammount = new FreightAmmount();
                $freight_ammount->quote_id = $quote->id;
                if ((isset($freight_ammount_charge[$key])) && (!empty($freight_ammount_charge[$key]))) {
                    $freight_ammount->charge = $freight_ammount_charge[$key];
                }
                if ((isset($freight_ammount_detail[$key])) && (!empty($freight_ammount_detail[$key]))) {
                    $freight_ammount->detail = $freight_ammount_detail[$key];
                }
                if ((isset($freight_total_units[$key])) && (!empty($freight_total_units[$key]))) {
                    $freight_ammount->units = $freight_total_units[$key];
                }
                if ((isset($freight_total_markup[$key])) && (!empty($freight_total_markup[$key]))) {
                    $freight_ammount->markup = $freight_total_markup[$key];
                }
                if ((isset($freight_ammount_price_per_unit[$key])) && ($freight_ammount_price_per_unit[$key]) != '') {
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = Input::all();
        $quote = Quote::find($id);
        $quote->update($request->all());

        OriginAmmount::where('quote_id',$quote->id)->delete();
        FreightAmmount::where('quote_id',$quote->id)->delete();
        DestinationAmmount::where('quote_id',$quote->id)->delete();

        if($input['origin_ammount_charge']!=[null]) {
            $origin_ammount_charge = array_values( array_filter($input['origin_ammount_charge']) );
            $origin_ammount_detail = array_values( array_filter($input['origin_ammount_detail']) );
            $origin_ammount_price_per_unit = array_values( array_filter($input['origin_price_per_unit']) );
            $origin_ammount_currency = array_values( array_filter($input['origin_ammount_currency']) );
            $origin_total_units = array_values( array_filter($input['origin_ammount_units']) );
            $origin_total_ammount = array_values( array_filter($input['origin_total_ammount']) );
            $origin_total_ammount_2 = array_values( array_filter($input['origin_total_ammount_2']) );
            $origin_total_markup = array_values( array_filter($input['origin_ammount_markup']) );
            foreach ($origin_ammount_charge as $key => $item) {
                $origin_ammount = new OriginAmmount();
                $origin_ammount->quote_id = $quote->id;
                if ((isset($origin_ammount_charge[$key])) && (!empty($origin_ammount_charge[$key]))) {
                    $origin_ammount->charge = $origin_ammount_charge[$key];
                }
                if ((isset($origin_ammount_detail[$key])) && (!empty($origin_ammount_detail[$key]))) {
                    $origin_ammount->detail = $origin_ammount_detail[$key];
                }
                if ((isset($origin_total_units[$key])) && (!empty($origin_total_units[$key]))) {
                    $origin_ammount->units = $origin_total_units[$key];
                }
                if ((isset($origin_total_markup[$key])) && (!empty($origin_total_markup[$key]))) {
                    $origin_ammount->markup = $origin_total_markup[$key];
                }
                if ((isset($origin_ammount_price_per_unit[$key])) && ($origin_ammount_price_per_unit[$key]) != '') {
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
            $freight_total_markup = array_values( array_filter($input['freight_ammount_markup']) );
            foreach ($freight_ammount_charge as $key => $item) {
                $freight_ammount = new FreightAmmount();
                $freight_ammount->quote_id = $quote->id;
                if ((isset($freight_ammount_charge[$key])) && (!empty($freight_ammount_charge[$key]))) {
                    $freight_ammount->charge = $freight_ammount_charge[$key];
                }
                if ((isset($freight_ammount_detail[$key])) && (!empty($freight_ammount_detail[$key]))) {
                    $freight_ammount->detail = $freight_ammount_detail[$key];
                }
                if ((isset($freight_total_units[$key])) && (!empty($freight_total_units[$key]))) {
                    $freight_ammount->units = $freight_total_units[$key];
                }
                if ((isset($freight_total_markup[$key])) && (!empty($freight_total_markup[$key]))) {
                    $freight_ammount->markup = $freight_total_markup[$key];
                }
                if ((isset($freight_ammount_price_per_unit[$key])) && ($freight_ammount_price_per_unit[$key]) != '') {
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
        $request->session()->flash('message.content', 'Register updated successfully!');
        return redirect()->route('quotes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
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

    public function duplicate($id)
    {
        $quotes = Quote::all();
        $quote = Quote::findOrFail($id);
        $companies = Company::all()->pluck('business_name','id');
        $harbors = Harbor::all()->pluck('name','id');
        $countries = Country::all()->pluck('name','id');
        $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
        $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
        $prices = Price::all()->pluck('name','id');
        $contacts = Contact::where('company_id',$quote->company_id)->pluck('first_name','id');
        $origin_ammounts = OriginAmmount::where('quote_id',$quote->id)->get();
        $freight_ammounts = FreightAmmount::where('quote_id',$quote->id)->get();
        $destination_ammounts = DestinationAmmount::where('quote_id',$quote->id)->get();

        $quote_duplicate = new Quote();
        $quote_duplicate->owner=\Auth::id();
        $quote_duplicate->incoterm=$quote->incoterm;
        $quote_duplicate->modality=$quote->modality;
        $quote_duplicate->pick_up_date=$quote->pick_up_date;
        if($quote->origin_address){
            $quote_duplicate->origin_address=$quote->origin_address;
        }
        if($quote->destination_address){
            $quote_duplicate->destination_address=$quote->destination_address;
        }
        if($quote->company_id){
            $quote_duplicate->company_id=$quote->company_id;
        }
        if($quote->origin_harbor_id){
            $quote_duplicate->origin_harbor_id=$quote->origin_harbor_id;
        }
        if($quote->destination_harbor_id){
            $quote_duplicate->destination_harbor_id=$quote->destination_harbor_id;
        }
        if($quote->price_id){
            $quote_duplicate->price_id=$quote->price_id;
        }
        if($quote->contact_id){
            $quote_duplicate->contact_id=$quote->contact_id;
        }
        if($quote->qty_20){
            $quote_duplicate->qty_20=$quote->qty_20;
        }
        if($quote->qty_40){
            $quote_duplicate->qty_40=$quote->qty_40;
        }
        if($quote->qty_40_hc){
            $quote_duplicate->qty_40_hc=$quote->qty_40_hc;
        }
        if($quote->delivery_type){
            $quote_duplicate->delivery_type=$quote->delivery_type;
        }
        if($quote->sub_total_origin){
            $quote_duplicate->sub_total_origin=$quote->sub_total_origin;
        }
        if($quote->sub_total_freight){
            $quote_duplicate->sub_total_freight=$quote->sub_total_freight;
        }
        if($quote->sub_total_destination){
            $quote_duplicate->sub_total_destination=$quote->sub_total_destination;
        }
        $quote_duplicate->status_id=$quote->status_id;
        $quote_duplicate->type=$quote->type;
        $quote_duplicate->save();

        foreach ($origin_ammounts as $origin){
            $origin_ammount_duplicate = new OriginAmmount();
            $origin_ammount_duplicate->charge=$origin->charge;
            $origin_ammount_duplicate->detail=$origin->detail;
            $origin_ammount_duplicate->units=$origin->units;
            $origin_ammount_duplicate->price_per_unit=$origin->price_per_unit;
            $origin_ammount_duplicate->markup=$origin->markup;
            $origin_ammount_duplicate->currency_id=$origin->currency_id;
            $origin_ammount_duplicate->total_ammount=$origin->total_ammount;
            if($origin->total_ammount_2){
                $origin_ammount_duplicate->total_ammount_2=$origin->total_ammount_2;
            }
            $origin_ammount_duplicate->quote_id=$quote_duplicate->id;
            $origin_ammount_duplicate->save();
        }

        foreach ($freight_ammounts as $freight){
            $freight_ammount_duplicate = new FreightAmmount();
            $freight_ammount_duplicate->charge=$freight->charge;
            $freight_ammount_duplicate->detail=$freight->detail;
            $freight_ammount_duplicate->units=$freight->units;
            $freight_ammount_duplicate->price_per_unit=$freight->price_per_unit;
            $freight_ammount_duplicate->markup=$freight->markup;
            $freight_ammount_duplicate->currency_id=$freight->currency_id;
            $freight_ammount_duplicate->total_ammount=$freight->total_ammount;
            if($freight->total_ammount_2){
                $freight_ammount_duplicate->total_ammount_2=$freight->total_ammount_2;
            }
            $freight_ammount_duplicate->quote_id=$quote_duplicate->id;
            $freight_ammount_duplicate->save();
        }

        foreach ($destination_ammounts as $destination){
            $destination_ammount_duplicate = new DestinationAmmount();
            $destination_ammount_duplicate->charge=$destination->charge;
            $destination_ammount_duplicate->detail=$destination->detail;
            $destination_ammount_duplicate->units=$destination->units;
            $destination_ammount_duplicate->price_per_unit=$destination->price_per_unit;
            $destination_ammount_duplicate->markup=$destination->markup;
            $destination_ammount_duplicate->currency_id=$destination->currency_id;
            $destination_ammount_duplicate->total_ammount=$destination->total_ammount;
            if($destination->total_ammount_2){
                $destination_ammount_duplicate->total_ammount_2=$destination->total_ammount_2;
            }
            $destination_ammount_duplicate->quote_id=$quote_duplicate->id;
            $destination_ammount_duplicate->save();
        }

        return redirect()->route('quotes.index', compact(['companies' => $companies,'quotes'=>$quotes,'countries'=>$countries,'harbors'=>$harbors]));
    }
}
