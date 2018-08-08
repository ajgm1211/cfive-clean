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
use App\MergeTag;
use GoogleMaps;
use App\Inland;
use App\TermAndCondition;
use App\TermsPort;
use App\StatusQuote;
use Illuminate\Support\Facades\Input;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Schedule;
use App\Incoterm;
use App\SaleTerm;
use App\EmailTemplate;
use App\PackageLoad;
use App\Mail\SendQuotePdf;

class QuoteController extends Controller
{
  /**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
  public function index()
  {


    $company_user_id = \Auth::user()->company_user_id;

    $quotes = Quote::whereHas('user', function($q) use($company_user_id){
      $q->where('company_user_id','=',$company_user_id);
    })->get();

    $companies = Company::all()->pluck('business_name','id');
    $harbors = Harbor::all()->pluck('business_name','id');
    $countries = Country::all()->pluck('name','id');

    if(\Auth::user()->company_user_id){
      $company_user=CompanyUser::find(\Auth::user()->company_user_id);
      $currency_cfg = Currency::find($company_user->currency_id);
    }else{
      $company_user='';
      $currency_cfg = '';
    }

    return view('quotes/index', ['companies' => $companies,'quotes'=>$quotes,'countries'=>$countries,'harbors'=>$harbors,'currency_cfg'=>$currency_cfg]);
  }

  public function automatic(){

    $quotes = Quote::all();
    $company_user_id=\Auth::user()->company_user_id;
    $companies = Company::where('company_user_id','=',$company_user_id)->pluck('business_name','id');
    $harbors = Harbor::all()->pluck('name','id');
    $countries = Country::all()->pluck('name','id');
    $prices = Price::all()->pluck('name','id');
    $company_user = User::where('id',\Auth::id())->first();
    if(count($company_user->companyUser)>0) {
      $currency_name = Currency::where('id', $company_user->companyUser->currency_id)->first();
    }else{
      $currency_name = '';
    }
    $currencies = Currency::all()->pluck('alphacode','id');
    return view('quotation/new2', ['companies' => $companies,'quotes'=>$quotes,'countries'=>$countries,'harbors'=>$harbors,'prices'=>$prices,'company_user'=>$company_user,'currencies'=>$currencies,'currency_name'=>$currency_name]);

  }
  public function test(Request $request){

    $info =$request->input('info');
    $info = json_decode($info);
    $form =$request->input('form');
    $schedules = $request->input('schedules');
    $form = json_decode($form);
    $company_user_id=\Auth::user()->company_user_id;
    $quotes = Quote::all();
    $company_user=CompanyUser::find($company_user_id);
    $companies=Company::where('company_user_id',$company_user->id)->pluck('business_name','id');
    $harbors = Harbor::all()->pluck('name','id');
    $countries = Country::all()->pluck('name','id');
    $currency = Currency::all()->pluck('alphacode','id');
    $prices = Price::all()->pluck('name','id');
    $user = User::where('id',\Auth::id())->first();


    $currencies = Currency::all();
    $currency_cfg = Currency::find($company_user->currency_id);
    return view('quotation/add', ['companies' => $companies,'quotes'=>$quotes,'countries'=>$countries,'harbors'=>$harbors,'prices'=>$prices,'company_user'=>$user,'currencies'=>$currencies,'currency_cfg'=>$currency_cfg,'info'=> $info,'form' => $form ,'currency' => $currency , 'schedules' => $schedules  ]);

  }

  public function skipPluck($pluck)
  {
    $skips = ["[","]","\""];
    return str_replace($skips, ' ',$pluck);
  }
  public function ratesCurrency($id,$typeCurrency){

    $rates = Currency::where('id','=',$id)->get();
    foreach($rates as $rate){
      if($typeCurrency == "USD"){
        $rateC = $rate->rates;
      }else{
        $rateC = $rate->rates_eur;
      }
    }

    return $rateC;
  }
  public function listRate(Request $request)
  {

    $company_user_id=\Auth::user()->company_user_id;
    $company = User::where('id',\Auth::id())->with('companyUser.currency')->first();
    $typeCurrency =  $company->companyUser->currency->alphacode ;
    $idCurrency = $company->companyUser->currency_id;
    //dd($company);
    $origin_port = $request->input('originport');
    $destiny_port = $request->input('destinyport');
    $delivery_type = $request->input('delivery_type');
    //$typeCurrency = 'USD';
    // valores de los markup en Freight 
    $price_id = $request->input('price_id');

    $fclMarkup = Price::whereHas('company_price', function($q) use($price_id) {
      $q->where('price_id', '=',$price_id);
    })->with('freight_markup','local_markup','inland_markup')->get();
    $freighPercentage = 0;
    $freighAmmount = 0;
    $localPercentage = 0;
    $localAmmount = 0;
    $inlandPercentage = 0;
    $inlandAmmount = 0;
    // Calculo de los markups
    foreach($fclMarkup as $freight){
      // Freight 
      $fclFreight = $freight->freight_markup->where('price_type_id','=',1);
      $freighPercentage = $this->skipPluck($fclFreight->pluck('percent_markup'));
      $freighAmmount =  $this->skipPluck($fclFreight->pluck('fixed_markup'));
      // Local y global

      $fclLocal = $freight->local_markup->where('price_type_id','=',1);
      if($request->modality == "1"){
        $localPercentage = intval($this->skipPluck($fclLocal->pluck('percent_markup_export')));
        $localAmmount =  intval($this->skipPluck($fclLocal->pluck('fixed_markup_export')));
      }else{
        $localPercentage = intval($this->skipPluck($fclLocal->pluck('percent_markup_import')));
        $localAmmount =  intval($this->skipPluck($fclLocal->pluck('fixed_markup_import')));
      }

      // Inlands 

      $fclInland = $freight->inland_markup->where('price_type_id','=',1);

      if($request->modality == "1"){
        $inlandPercentage = intval($this->skipPluck($fclInland->pluck('percent_markup_export')));
        $inlandAmmount =  intval($this->skipPluck($fclInland->pluck('fixed_markup_export')));
      }else{
        $inlandPercentage = intval($this->skipPluck($fclInland->pluck('percent_markup_import')));
        $inlandAmmount =  intval($this->skipPluck($fclInland->pluck('fixed_markup_import')));
      }

    }


    //--------------------------------------
    // Calculo de los inlands
    if($delivery_type == "2" || $delivery_type == "4" ){
      $inlands = Inland::whereHas('inlandports', function($q) use($destiny_port) {
        $q->whereIn('port', $destiny_port);
      })->where('company_user_id','=',$company_user_id)->with('inlandports.ports','inlanddetails.currency')->get();

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


                  $rateI = $this->ratesCurrency($details->currency->id,$typeCurrency);
                  if($details->type == 'twuenty' && $request->input('twuenty') != "0"){
                    $distancia = intval($km[0]);
                    if( $distancia >= $details->lower && $distancia  <= $details->upper){
                      $monto += ($request->input('twuenty') * $details->ammount) / $rateI;

                      //  echo $monto;
                      //echo '<br>';

                    }
                  }
                  if($details->type == 'forty' && $request->input('forty') != "0"){
                    $distancia = intval($km[0]);
                    if( $distancia >= $details->lower && $distancia  <= $details->upper){
                      $monto += ($request->input('forty') * $details->ammount) / $rateI;

                    }
                  }
                  if($details->type == 'fortyhc' && $request->input('fortyhc') != "0"){
                    $distancia = intval($km[0]);
                    if( $distancia >= $details->lower && $distancia  <= $details->upper){
                      $monto += ($request->input('fortyhc') * $details->ammount) / $rateI;

                    }
                  }

                }
                // MARKUPS 
                if($inlandPercentage != 0){

                  $markup = ( $monto *  $inlandPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $monto += $markup ;
                  $arraymarkupT = array("markup" => $markup  , "typemarkup" => "$typeCurrency ($inlandPercentage%)") ;
                }else{
                  $markup =$inlandAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $monto += $markup;
                  $arraymarkupT = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                }


                $monto = number_format($monto, 2, '.', '');
                if($monto > 0){
                  $arregloInland =  array("prov_id" => $inlandsValue->id ,"provider" => $inlandsValue->provider ,"port_id" => $ports->ports->id,"port_name" =>  $ports->ports->name ,"km" => $km[0] , "monto" => $monto ,'type' => 'Destiny Port To Door','type_currency' => $typeCurrency ,'idCurrency' => $inlandsValue->currency_id );
                  $arregloInland = array_merge($arraymarkupT,$arregloInland);   
                  $data[] =$arregloInland;
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
      })->where('company_user_id','=',$company_user_id)->with('inlandports.ports','inlanddetails.currency')->get();

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

                  $rateI = $this->ratesCurrency($details->currency->id,$typeCurrency);
                  if($details->type == 'twuenty' && $request->input('twuenty') != "0"){
                    $distancia = intval($km[0]);
                    if( $distancia >= $details->lower && $distancia  <= $details->upper){
                      $monto += ($request->input('twuenty') * $details->ammount) / $rateI ;
                    }
                  }
                  if($details->type == 'forty' && $request->input('forty') != "0"){
                    $distancia = intval($km[0]);
                    if( $distancia >= $details->lower && $distancia  <= $details->upper){
                      $monto += ($request->input('forty') * $details->ammount)  / $rateI;
                    }
                  }
                  if($details->type == 'fortyhc' && $request->input('fortyhc') != "0"){
                    $distancia = intval($km[0]);
                    if( $distancia >= $details->lower && $distancia  <= $details->upper){
                      $monto += ($request->input('fortyhc') * $details->ammount) / $rateI;

                    }
                  }

                }
                // MARKUPS 
                if($inlandPercentage != 0){

                  $markup = ( $monto *  $inlandPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $monto += $markup ;
                  $arraymarkupT = array("markup" => $markup , "typemarkup" => "$typeCurrency ($inlandPercentage%)") ;
                }else{
                  $markup =$inlandAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $monto += $markup;
                  $arraymarkupT = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                }
                $monto = number_format($monto, 2, '.', '');
                if($monto > 0){
                  $arregloInland = array("prov_id" => $inlandsValue->id ,"provider" => $inlandsValue->provider ,"port_id" => $ports->ports->id,"port_name" =>  $ports->ports->name ,"km" => $km[0] , "monto" => $monto ,'type' => 'Origin Port To Door','type_currency' => $typeCurrency ,'idCurrency' => $inlandsValue->currency_id  );
                  $arregloInland = array_merge($arregloInland,$arraymarkupT);
                  $dataOrig[] = $arregloInland;
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
    // Fin del calculo de los inlands 

    $date =  $request->input('date');
    $user_id =  \Auth::id();
    $company_user_id =  \Auth::user()->company_user_id;
    $company_id = $request->input('company_id');
    $arreglo = Rate::whereIn('origin_port',$origin_port)->whereIn('destiny_port',$destiny_port)->with('port_origin','port_destiny','contract','carrier')->whereHas('contract', function($q) use($date,$user_id,$company_user_id,$company_id) 
    {
      $q->where('validity', '<=',$date)->where('expire', '>=', $date)->where('company_user_id','=',$company_user_id)->whereHas('contract_user_restriction', function($a) use($user_id){
        $a->where('user_id', '=',$user_id);
      })->orDoesntHave('contract_company_restriction')
      ->whereHas('contract_company_restriction', function($a) use($company_id)                  {
        $a->where('company_id', '=',$company_id);
      })->orDoesntHave('contract_company_restriction');
    })->get();

    $formulario = $request;
    $array20 = array('2','4','5');
    $array40 =  array('1','4','5');
    $array40Hc= array('3','4','5');
    $collectionLocal = new Collection();
    foreach($arreglo as $data){
      $totalFreight = 0;
      $FreightCharges = 0;
      $totalRates = 0;
      $totalOrigin = 0;
      $totalDestiny =0;
      $totalQuote= 0;

      $collectionOrig = new Collection();
      $collectionDest = new Collection();
      $collectionFreight = new Collection();

      $collectionGloOrig = new Collection();
      $collectionGloDest = new Collection();
      $collectionGloFreight = new Collection();

      $collectionRate = new Collection();

      $rateC = $this->ratesCurrency($data->currency->id,$typeCurrency);
      $subtotal = 0;
      $orig_port = array($data->origin_port);
      $dest_port = array($data->destiny_port);
      $carrier[] = $data->carrier_id;

      // Calculo de los rates
      if($request->input('twuenty') != "0") {
        $subtotalT = $formulario->twuenty *  $data->twuenty;
        $totalT = ($formulario->twuenty *  $data->twuenty) / $rateC ;
        // MARKUPS 
        if($freighPercentage != 0){
          $freighPercentage = intval($freighPercentage);
          $markup = ( $totalT *  $freighPercentage ) / 100 ;
          $markup = number_format($markup, 2, '.', '');
          $totalT += $markup ;
          $arraymarkupT = array("markup" => $markup , "typemarkup" => "$typeCurrency ($freighPercentage%)") ;
        }else{
          $markup =$freighAmmount;

          $markup = number_format($markup, 2, '.', '');
          $totalT += $markup;
          $arraymarkupT = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
        }

        $totalT =  number_format($totalT, 2, '.', '');
        $totalFreight += $totalT;
        $totalRates += $totalT;
        $array = array('type'=>'Ocean Freight 20 ', 'cantidad' => $formulario->twuenty,'detail'=>'Container 20', 'price' => $data->twuenty, 'currency' => $data->currency->alphacode ,'subtotal' => $subtotalT , 'total' =>$totalT." ". $typeCurrency , 'idCurrency' => $data->currency_id );
        $array = array_merge($array,$arraymarkupT);

        $collectionRate->push($array);

        $data->setAttribute('montT',$array);
      }
      if($request->input('forty') != "0") {
        $subtotalF = $formulario->forty *  $data->forty;
        $totalF = ($formulario->forty *  $data->forty)  / $rateC ;
        // MARKUPS 
        if($freighPercentage != 0){
          $freighPercentage = intval($freighPercentage);
          $markup = ( $totalF *  $freighPercentage ) / 100 ;
          $markup = number_format($markup, 2, '.', '');
          $totalF += $markup ;
          $arraymarkupF = array("markup" => $markup ,  "typemarkup" => "$typeCurrency ($freighPercentage%)") ;
        }else{
          $markup =$freighAmmount;
          $markup = number_format($markup, 2, '.', '');
          $totalF += $markup;
          $arraymarkupF = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
        }

        $totalF =  number_format($totalF, 2, '.', '');
        $totalFreight += $totalF;
        $totalRates += $totalF;
        $array = array('type'=>'Ocean Freight 40', 'cantidad' => $formulario->forty,'detail'=>'Container 40', 'price' => $data->forty, 'currency' => $data->currency->alphacode ,'subtotal' => $subtotalF , 'total' =>$totalF." ". $typeCurrency , 'idCurrency' => $data->currency_id);
        $array = array_merge($array,$arraymarkupF);
        $collectionRate->push($array);

        $data->setAttribute('montF',$array);
      }
      if($request->input('fortyhc') != "0") {
        $subtotalFHC = $formulario->fortyhc *  $data->fortyhc;
        $totalFHC = ($formulario->fortyhc *  $data->fortyhc)  / $rateC ;
        // MARKUPS 
        if($freighPercentage != 0){
          $freighPercentage = intval($freighPercentage);
          $markup = ( $totalFHC *  $freighPercentage ) / 100 ;
          $markup = number_format($markup, 2, '.', '');
          $totalFHC += $markup ;
          $arraymarkupFH = array("markup" => $markup  , "typemarkup" => "$typeCurrency ($freighPercentage%)") ;
        }else{
          $markup =$freighAmmount;
          $markup = number_format($markup, 2, '.', '');
          $totalFHC += $markup;
          $arraymarkupFH = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
        }

        $totalFHC =  number_format($totalFHC, 2, '.', '');
        $totalFreight += $totalFHC;
        $totalRates += $totalFHC;
        $array = array('type'=>'Ocean Freight 40HC ', 'cantidad' => $formulario->fortyhc,'detail'=>'Container 40HC', 'price' => $data->fortyhc, 'currency' => $data->currency->alphacode ,'subtotal' => $subtotalFHC , 'total' =>$totalFHC." ". $typeCurrency , 'idCurrency' => $data->currency_id);
        $array = array_merge($array,$arraymarkupFH);
        $data->setAttribute('montFHC',$array);
        $collectionRate->push($array);



      }

      $data->setAttribute('rates',$collectionRate);

      // id de los ALL 
      array_push($orig_port,742);
      array_push($dest_port,742);

      //  calculo de los local charges en freight , origin y destiny 
      $localChar = LocalCharge::where('contract_id','=',$data->contract_id)->whereHas('localcharcarriers', function($q) use($carrier) {
        $q->whereIn('carrier_id', $carrier);
      })->whereHas('localcharports', function($q) use($orig_port,$dest_port) {
        $q->whereIn('port_orig', $orig_port)->whereIn('port_dest',$dest_port);
      })->with('localcharports.portOrig','localcharcarriers.carrier','currency','surcharge.SaleTermSurcharges.saleterm')->get();


      foreach($localChar as $local){

        $rateMount = $this->ratesCurrency($local->currency->id,$typeCurrency);

        if(in_array($local->calculationtype_id, $array20)){
          if($request->input('twuenty') != "0") {
            foreach($local->localcharcarriers as $carrierGlobal){
              if($carrierGlobal->carrier_id == $data->carrier_id ){
                if($local->typedestiny_id == '1'){  

                  $subtotal_local = $formulario->twuenty *  $local->ammount;
                  $totalAmmount = ($formulario->twuenty *  $local->ammount) / $rateMount ;
                  // MARKUP
                  if($localPercentage != 0){

                    $markup = ( $totalAmmount *  $localPercentage ) / 100 ;

                    $markup = number_format($markup, 2, '.', '');

                    $totalAmmount += $markup ;
                    $arraymarkupT = array("markup" => $markup , "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                  }else{
                    $markup =$localAmmount;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup;
                    $arraymarkupT = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                  }


                  $totalOrigin += $totalAmmount ;
                  $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloOrig = array('surcharge_terms' => $local->surcharge->SaleTermSurcharges->saleterm->name,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->twuenty , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency , 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'20\'  Local ' , 'subtotal_local' => $subtotal_local , 'cantidadT' => $formulario->twuenty , 'idCurrency' => $local->currency->id);
                  $arregloOrig = array_merge($arregloOrig,$arraymarkupT);
                  $origTwuenty["origin"] = $arregloOrig;

                  $collectionOrig->push($origTwuenty);

                }

                if($local->typedestiny_id == '2'){
                  $subtotal_local = $formulario->twuenty *  $local->ammount;
                  $totalAmmount = ($formulario->twuenty *  $local->ammount) / $rateMount ;
                  // MARKUP
                  if($localPercentage != 0){
                    $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup ;
                    $arraymarkupT = array("markup" => $markup , "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                  }else{
                    $markup =$localAmmount;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup;
                    $arraymarkupT = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                  }
                  $totalDestiny += $totalAmmount;
                  $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloDest =  array('surcharge_terms' => $local->surcharge->SaleTermSurcharges->saleterm->name,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->twuenty , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'20\'  Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $formulario->twuenty , 'idCurrency' => $local->currency->id );
                  $arregloDest = array_merge($arregloDest,$arraymarkupT);
                  $destTwuenty["destiny"] =$arregloDest;
                  $collectionDest->push($destTwuenty);
                }
                if($local->typedestiny_id == '3'){
                  $subtotal_local = $formulario->twuenty *  $local->ammount;
                  $totalAmmount = ($formulario->twuenty *  $local->ammount) / $rateMount;

                  // MARKUP
                  if($localPercentage != 0){
                    $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup ;
                    $arraymarkupT = array("markup" => $markup , "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                  }else{
                    $markup =$localAmmount;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup;
                    $arraymarkupT = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                  }
                  $totalFreight += $totalAmmount;
                  $FreightCharges += $totalAmmount;

                  $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloFreight = array('surcharge_terms' => $local->surcharge->SaleTermSurcharges->saleterm->name,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->twuenty , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'20\'  Local ' , 'subtotal_local' => $subtotal_local , 'cantidadT' => $formulario->twuenty , 'idCurrency' => $local->currency->id);
                  $arregloFreight = array_merge($arregloFreight,$arraymarkupT);
                  $freighTwuenty["freight"] = $arregloFreight;
                  $collectionFreight->push($freighTwuenty);
                }
              }
            }
          }
        }


        if(in_array($local->calculationtype_id, $array40)){
          if($request->input('forty') != "0") {
            foreach($local->localcharcarriers as $carrierGlobal){
              if($carrierGlobal->carrier_id == $data->carrier_id ){
                if($local->typedestiny_id == '1'){
                  if($local->calculationtype_id == "4"  ){
                    $subtotal_local = ($formulario->forty *  $local->ammount) * 2 ;
                    $totalAmmount = (($formulario->forty *  $local->ammount) * 2 ) / $rateMount ;
                    $cantidadT = $formulario->forty + $formulario->forty;
                    // MARKUP
                    if($localPercentage != 0){
                      $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup ;
                      $arraymarkupF = array("markup" => $markup , "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup;
                      $arraymarkupF = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                    }
                    $totalOrigin += $totalAmmount ;
                  }else{
                    $subtotal_local = $formulario->forty *  $local->ammount;
                    $totalAmmount = ($formulario->forty *  $local->ammount) *  $rateMount ;
                    $cantidadT = $formulario->forty;
                    // MARKUP
                    if($localPercentage != 0){
                      $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup ;
                      $arraymarkupF = array("markup" => $markup , "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup;
                      $arraymarkupF = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                    }
                    $totalOrigin += $totalAmmount ;
                  }
                  $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloOrig =  array('surcharge_terms' => $local->surcharge->SaleTermSurcharges->saleterm->name,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->forty , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency , 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\'  Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id);
                  $arregloOrig = array_merge($arregloOrig,$arraymarkupF);

                  $origForty["origin"] =$arregloOrig;
                  $collectionOrig->push($origForty);



                }
                if($local->typedestiny_id == '2'){
                  if($local->calculationtype_id == "4"  ){
                    $subtotal_local = ($formulario->forty *  $local->ammount) * 2 ;
                    $totalAmmount = (($formulario->forty *  $local->ammount) * 2 ) / $rateMount ;
                    $cantidadT = $formulario->forty + $formulario->forty;
                    // MARKUP
                    if($localPercentage != 0){
                      $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup ;
                      $arraymarkupF = array("markup" => $markup , "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup;
                      $arraymarkupF = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                    }
                    $totalDestiny += $totalAmmount;
                  }else{
                    $subtotal_local = $formulario->forty *  $local->ammount;
                    $totalAmmount = ($formulario->forty *  $local->ammount) / $rateMount ;
                    $cantidadT = $formulario->forty ;
                    // MARKUP
                    if($localPercentage != 0){
                      $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup ;
                      $arraymarkupF = array("markup" => $markup , "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup;
                      $arraymarkupF = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                    }
                    $totalDestiny += $totalAmmount;
                  }
                  $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloDest =  array('surcharge_terms' => $local->surcharge->SaleTermSurcharges->saleterm->name,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->forty , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\'  Local ' , 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id  );
                  $arregloDest = array_merge($arregloDest,$arraymarkupF);
                  $destForty["destiny"] =$arregloDest;
                  $collectionDest->push($destForty);


                }
                if($local->typedestiny_id == '3'){
                  if($local->calculationtype_id == "4"  ){
                    $subtotal_local = ($formulario->forty *  $local->ammount) * 2 ;
                    $totalAmmount = (($formulario->forty *  $local->ammount) * 2 ) / $rateMount ;
                    $cantidadT = $formulario->forty + $formulario->forty;

                    // MARKUP
                    if($localPercentage != 0){
                      $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup ;
                      $arraymarkupF = array("markup" => $markup , "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup;
                      $arraymarkupF = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                    }
                    $totalFreight += $totalAmmount;
                    $FreightCharges += $totalAmmount;

                  }else{
                    $subtotal_local = $formulario->forty *  $local->ammount;
                    $totalAmmount = ($formulario->forty *  $local->ammount)/ $rateMount ;
                    $cantidadT = $formulario->forty;

                    // MARKUP
                    if($localPercentage != 0){
                      $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup ;
                      $arraymarkupF = array("markup" => $markup , "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup;
                      $arraymarkupF = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                    }
                    $totalFreight += $totalAmmount;
                    $FreightCharges += $totalAmmount;

                  }
                  $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloFreight = array('surcharge_terms' => $local->surcharge->SaleTermSurcharges->saleterm->name,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->forty , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'40\'  Local ' , 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id );
                  $arregloFreight = array_merge($arregloFreight,$arraymarkupF);
                  $freighForty["freight"] = $arregloFreight;
                  $collectionFreight->push($freighForty);


                }
              }
            }
          }
        }
        if(in_array($local->calculationtype_id, $array40Hc)){
          if($request->input('fortyhc') != "0") {
            foreach($local->localcharcarriers as $carrierGlobal){
              if($carrierGlobal->carrier_id == $data->carrier_id ){
                if($local->typedestiny_id == '1'){
                  if($local->calculationtype_id == "4"  ){
                    $subtotal_local = ($formulario->fortyhc *  $local->ammount) * 2 ;
                    $totalAmmount = (($formulario->fortyhc *  $local->ammount) * 2 ) / $rateMount ;
                    $cantidadT = $formulario->fortyhc + $formulario->fortyhc;
                    // MARKUP
                    if($localPercentage != 0){
                      $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup ;
                      $arraymarkupFH = array("markup" => $markup , "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup;
                      $arraymarkupFH = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                    }
                    $totalOrigin += $totalAmmount ;
                  }else{
                    $subtotal_local = $formulario->fortyhc *  $local->ammount;
                    $totalAmmount = ($formulario->fortyhc *  $local->ammount)  / $rateMount;
                    $cantidadT = $formulario->fortyhc ;
                    // MARKUP
                    if($localPercentage != 0){
                      $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup ;
                      $arraymarkupFH = array("markup" => $markup , "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup;
                      $arraymarkupFH = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                    }
                    $totalOrigin += $totalAmmount ;
                  }
                  $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloOrig =  array('surcharge_terms' => $local->surcharge->SaleTermSurcharges->saleterm->name,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->fortyhc , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency , 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\' HC Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id  );
                  $arregloOrig = array_merge($arregloOrig,$arraymarkupFH);
                  $origFortyHc["origin"] =$arregloOrig;
                  $collectionOrig->push($origFortyHc);



                }
                if($local->typedestiny_id == '2'){
                  if($local->calculationtype_id == "4"  ){
                    $subtotal_local = ($formulario->fortyhc *  $local->ammount) * 2 ;
                    $totalAmmount = (($formulario->fortyhc *  $local->ammount) * 2) / $rateMount ;
                    $cantidadT = $formulario->fortyhc + $formulario->fortyhc;
                    // MARKUP
                    if($localPercentage != 0){
                      $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup ;
                      $arraymarkupFH = array("markup" => $markup , "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup;
                      $arraymarkupFH = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                    }
                    $totalDestiny += $totalAmmount;
                  }else{
                    $subtotal_local = $formulario->fortyhc *  $local->ammount;
                    $totalAmmount = ($formulario->fortyhc *  $local->ammount) / $rateMount;
                    $cantidadT = $formulario->fortyhc;
                    // MARKUP
                    if($localPercentage != 0){
                      $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup ;
                      $arraymarkupFH = array("markup" => $markup , "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup;
                      $arraymarkupFH = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                    }
                    $totalDestiny += $totalAmmount;
                  }
                  $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloDest = array('surcharge_terms' => $local->surcharge->SaleTermSurcharges->saleterm->name,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->fortyhc , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\' HC Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id  );
                  $arregloDest  = array_merge($arregloDest,$arraymarkupFH);
                  $destFortyHc["destiny"] = $arregloDest;
                  $collectionDest->push($destFortyHc);


                }
                if($local->typedestiny_id == '3'){
                  if($local->calculationtype_id == "4"  ){
                    $subtotal_local = ($formulario->fortyhc *  $local->ammount) * 2 ;
                    $totalAmmount = (($formulario->fortyhc *  $local->ammount) * 2) / $rateMount ;
                    $cantidadT = $formulario->fortyhc + $formulario->fortyhc;

                    // MARKUP
                    if($localPercentage != 0){
                      $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup ;
                      $arraymarkupFH = array("markup" => $markup , "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup;
                      $arraymarkupFH = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                    }
                    $totalFreight += $totalAmmount;
                    $FreightCharges += $totalAmmount;

                  }else{
                    $subtotal_local = $formulario->fortyhc *  $local->ammount;
                    $totalAmmount = ($formulario->fortyhc *  $local->ammount) / $rateMount;
                    $cantidadT = $formulario->fortyhc;

                    // MARKUP
                    if($localPercentage != 0){
                      $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup ;
                      $arraymarkupFH = array("markup" => $markup , "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup;
                      $arraymarkupFH = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                    }
                    $totalFreight += $totalAmmount;
                    $FreightCharges += $totalAmmount;

                  }
                  $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloFreight = array('surcharge_terms' => $local->surcharge->SaleTermSurcharges->saleterm->name,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->fortyhc , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\' HC Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id );
                  $arregloFreight = array_merge($arregloFreight,$arraymarkupFH);
                  $freighFortyHc["freight"] = $arregloFreight;
                  $collectionFreight->push($freighFortyHc);

                }
              }
            }
          }
        }
        if($local->calculationtype_id == "6"){
          foreach($local->localcharcarriers as $carrierGlobal){
            if($carrierGlobal->carrier_id == $data->carrier_id ){
              if($local->typedestiny_id == '1'){

                $subtotal_local =  $local->ammount;
                $totalAmmount =  $local->ammount  / $rateMount;
                $cantidadT = 1;
                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupPC = array("markup" => $markup , "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup;
                  $arraymarkupPC = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                }
                $totalOrigin += $totalAmmount ;
                $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $arregloOrig =  array('surcharge_terms' => $local->surcharge->SaleTermSurcharges->saleterm->name,'surcharge_name' => $local->surcharge->name,'cantidad' => "-" , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>' Shipment Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id );
                $arregloOrig = array_merge($arregloOrig,$arraymarkupPC);
                $origPer["origin"] =$arregloOrig;
                $collectionOrig->push($origPer);

              }
              if($local->typedestiny_id == '2'){
                $subtotal_local =  $local->ammount;
                $totalAmmount =  $local->ammount  / $rateMount;
                $cantidadT = 1;
                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupPC = array("markup" => $markup , "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup;
                  $arraymarkupPC = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                }
                $totalDestiny += $totalAmmount;
                $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $arregloDest = array('surcharge_terms' => $local->surcharge->SaleTermSurcharges->saleterm->name,'surcharge_name' => $local->surcharge->name,'cantidad' => "-" , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>' Shipment Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT  , 'idCurrency' => $local->currency->id  );
                $arregloDest = array_merge($arregloDest,$arraymarkupPC);
                $destPer["destiny"] = $arregloDest;
                $collectionDest->push($destPer);

              }
              if($local->typedestiny_id == '3'){
                $subtotal_local =  $local->ammount;
                $cantidadT = 1;

                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupPC = array("markup" => $markup , "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup;
                  $arraymarkupPC = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                }

                $totalAmmount =  $local->ammout  / $rateMount;

                $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $totalFreight += $totalAmmount;
                $FreightCharges += $totalAmmount;

                $arregloPC = array('surcharge_terms' => $local->surcharge->SaleTermSurcharges->saleterm->name,'surcharge_name' => $local->surcharge->name,'cantidad' => "-" , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>' Shipment Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id  );
                $arregloPC = array_merge($arregloPC,$arraymarkupPC);
                $freightPer["freight"] = $arregloPC;
                $collectionFreight->push($freightPer);


              }
            }
          }
        }
      }
      // fin calculo local charges 
      //#######################################################################
      //  calculo de los global charges en freight , origin y destiny 
      $globalChar = GlobalCharge::whereHas('globalcharcarrier', function($q) use($carrier) {
        $q->whereIn('carrier_id', $carrier);
      })->whereHas('globalcharport', function($q) use($orig_port,$dest_port) {
        $q->whereIn('port_orig', $orig_port)->whereIn('port_dest', $dest_port);
      })->where('company_user_id','=',$company_user_id)->with('globalcharport.portOrig','globalcharport.portDest','globalcharcarrier.carrier','currency','surcharge.SaleTermSurcharges.saleterm')->get();

      foreach($globalChar as $global){

        $rateMountG = $this->ratesCurrency($global->currency->id,$typeCurrency);
        if(in_array($global->calculationtype_id, $array20)){
          if($request->input('twuenty') != "0") {
            foreach($global->globalcharcarrier as $carrierGlobal){
              if($carrierGlobal->carrier_id == $data->carrier_id ){
                if($global->typedestiny_id == '1'){

                  $subtotal_global = $formulario->twuenty *  $global->ammount;
                  $totalAmmount = ($formulario->twuenty *  $global->ammount) / $rateMountG ;
                  $cantidadT = $formulario->twuenty;
                  // MARKUP
                  if($localPercentage != 0){
                    $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup ;
                    $arraymarkupT = array("markup" => $markup , "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                  }else{
                    $markup =$localAmmount;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup;
                    $arraymarkupT = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                  }

                  $totalOrigin += $totalAmmount ;
                  $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloOrig = array('surcharge_terms' => $global->surcharge->SaleTermSurcharges->saleterm->name,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->twuenty , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'20\' Global '  , 'subtotal_global' => $subtotal_global , 'cantidadT' => $cantidadT , 'idCurrency' => $global->currency->id);
                  $arregloOrig = array_merge($arregloOrig,$arraymarkupT);

                  $origTwuentyGlo["origin"] = $arregloOrig;
                  $collectionGloOrig->push($origTwuentyGlo);

                }
                if($global->typedestiny_id == '2'){
                  $subtotal_global = $formulario->twuenty *  $global->ammount;
                  $totalAmmount = ($formulario->twuenty *  $global->ammount) / $rateMountG;
                  $cantidadT = $formulario->twuenty;
                  // MARKUP
                  if($localPercentage != 0){
                    $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup ;
                    $arraymarkupT = array("markup" => $markup , "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                  }else{
                    $markup =$localAmmount;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup;
                    $arraymarkupT = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                  }


                  $totalDestiny += $totalAmmount;
                  $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloDest = array('surcharge_terms' => $global->surcharge->SaleTermSurcharges->saleterm->name,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->twuenty , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'20\' Global ', 'subtotal_global' => $subtotal_global , 'cantidadT' => $cantidadT , 'idCurrency' => $global->currency->id);
                  $arregloDest = array_merge($arregloDest,$arraymarkupT);

                  $destTwuentyGlo["destiny"] = $arregloDest;
                  $collectionGloDest->push($destTwuentyGlo);
                }
                if($global->typedestiny_id == '3'){
                  $subtotal_global = $formulario->twuenty *  $global->ammount;
                  $totalAmmount = ($formulario->twuenty *  $global->ammount) / $rateMountG;
                  $cantidadT = $formulario->twuenty;
                  // MARKUP
                  if($localPercentage != 0){
                    $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup ;
                    $arraymarkupT = array("markup" => $markup , "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                  }else{
                    $markup =$localAmmount;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup;
                    $arraymarkupT = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                  }
                  $totalFreight += $totalAmmount;
                  $FreightCharges += $totalAmmount;

                  $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloFreight =  array('surcharge_terms' => $global->surcharge->SaleTermSurcharges->saleterm->name,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->twuenty , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'20\' Global ', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
                  $arregloFreight = array_merge($arregloFreight,$arraymarkupT);
                  $freighTwuentyGlo["freight"] =$arregloFreight;
                  $collectionGloFreight->push($freighTwuentyGlo);
                }
              }
            }
          }
        }
        if(in_array($global->calculationtype_id, $array40)){
          if($request->input('forty') != "0") {
            foreach($global->globalcharcarrier as $carrierGlobal){
              if($carrierGlobal->carrier_id == $data->carrier_id ){
                if($global->typedestiny_id == '1'){
                  if($global->calculationtype_id == "4"  ){
                    $subtotal_global = ($formulario->forty *  $global->ammount) * 2 ;
                    $totalAmmount = (($formulario->forty *  $global->ammount) * 2 ) / $rateMountG ;
                    $cantidadT = $formulario->forty +  $formulario->forty  ;

                    $totalOrigin += $totalAmmount ;
                  }else{
                    $subtotal_global = $formulario->forty *  $global->ammount;
                    $totalAmmount = ($formulario->forty *  $global->ammount) / $rateMountG;
                    $totalOrigin += $totalAmmount ;
                    $cantidadT = $formulario->forty;
                  }
                  // MARKUP
                  if($localPercentage != 0){
                    $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup ;
                    $arraymarkupF = array("markup" => $markup , "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                  }else{
                    $markup =$localAmmount;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup;
                    $arraymarkupF = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                  }
                  $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloOrig =  array('surcharge_terms' => $global->surcharge->SaleTermSurcharges->saleterm->name,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->forty , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\' Global ', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT , 'idCurrency' => $global->currency->id);
                  $arregloOrig = array_merge($arregloOrig,$arraymarkupF);
                  $origFortyGlo["origin"] =$arregloOrig;
                  $collectionGloOrig->push($origFortyGlo);

                }
                if($global->typedestiny_id == '2'){
                  if($global->calculationtype_id == "4"  ){
                    $subtotal_global = ($formulario->forty *  $global->ammount) * 2 ;
                    $totalAmmount = (($formulario->forty *  $global->ammount) * 2 ) / $rateMountG ;
                    $totalDestiny += $totalAmmount;
                    $cantidadT = $formulario->forty +  $formulario->forty  ;
                  }else{
                    $subtotal_global = $formulario->forty *  $global->ammount;
                    $totalAmmount = ($formulario->forty *  $global->ammount) / $rateMountG;
                    $totalDestiny += $totalAmmount;
                    $cantidadT = $formulario->forty;
                  }
                  // MARKUP
                  if($localPercentage != 0){
                    $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup ;
                    $arraymarkupF = array("markup" => $markup , "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                  }else{
                    $markup =$localAmmount;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup;
                    $arraymarkupF = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                  }
                  $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloDest =  array('surcharge_terms' => $global->surcharge->SaleTermSurcharges->saleterm->name,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->forty , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\' Global ', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
                  $arregloDest = array_merge($arregloDest,$arraymarkupF);
                  $destFortyGlo["destiny"] =$arregloDest;
                  $collectionGloDest->push($destFortyGlo);
                }
                if($global->typedestiny_id == '3'){
                  if($global->calculationtype_id == "4"  ){
                    $subtotal_global = ($formulario->forty *  $global->ammount) * 2 ;
                    $totalAmmount = (($formulario->forty *  $global->ammount) * 2 ) / $rateMountG ;
                    $cantidadT = $formulario->forty +  $formulario->forty  ;

                  }else{
                    $subtotal_global = $formulario->forty *  $global->ammount;
                    $totalAmmount = ($formulario->forty *  $global->ammount) / $rateMountG;
                    $cantidadT = $formulario->forty;

                  }
                  // MARKUP
                  if($localPercentage != 0){
                    $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup ;
                    $arraymarkupF = array("markup" => $markup , "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                  }else{
                    $markup =$localAmmount;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup;
                    $arraymarkupF = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                  }
                  $totalFreight += $totalAmmount;
                  $FreightCharges += $totalAmmount;
                  $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloFreight = array('surcharge_terms' => $global->surcharge->SaleTermSurcharges->saleterm->name,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->forty , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\' Global ' , 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
                  $arregloFreight = array_merge($arregloFreight,$arraymarkupF);
                  $freighFortyGlo["freight"] = $arregloFreight;
                  $collectionGloFreight->push($freighFortyGlo);
                }
              }
            }
          }
        }
        if(in_array($global->calculationtype_id, $array40Hc)){
          if($request->input('fortyhc') != "0") {
            foreach($global->globalcharcarrier as $carrierGlobal){
              if($carrierGlobal->carrier_id == $data->carrier_id ){
                if($global->typedestiny_id == '1'){
                  if($global->calculationtype_id == "4"  ){
                    $subtotal_global = ($formulario->fortyhc *  $global->ammount) * 2 ;
                    $totalAmmount = (($formulario->fortyhc *  $global->ammount) * 2)  / $rateMountG ;
                    $totalOrigin += $totalAmmount ;
                    $cantidadT = $formulario->fortyhc +  $formulario->fortyhc  ;

                  }else{
                    $subtotal_global =  $formulario->fortyhc *  $global->ammount;
                    $totalAmmount = ($formulario->fortyhc *  $global->ammount) / $rateMountG;
                    $totalOrigin += $totalAmmount ;
                    $cantidadT = $formulario->fortyhc;
                  }
                  // MARKUP
                  if($localPercentage != 0){
                    $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup ;
                    $arraymarkupFH = array("markup" => $markup , "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                  }else{
                    $markup =$localAmmount;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup;
                    $arraymarkupFH = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                  }
                  $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloOrig =  array('surcharge_terms' => $global->surcharge->SaleTermSurcharges->saleterm->name,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->fortyhc , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'40\' HC Global ', 'subtotal_global' => $subtotal_global , 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
                  $arregloOrig = array_merge($arregloOrig,$arraymarkupFH);
                  $origFortyHcGlo["origin"] =$arregloOrig;
                  $collectionGloOrig->push($origFortyHcGlo);

                }
                if($global->typedestiny_id == '2'){
                  if($global->calculationtype_id == "4"  ){
                    $subtotal_global = ($formulario->fortyhc *  $global->ammount) * 2 ;
                    $totalAmmount = (($formulario->fortyhc *  $global->ammount) * 2)  / $rateMountG ;
                    $totalDestiny += $totalAmmount;
                    $cantidadT = $formulario->fortyhc +  $formulario->fortyhc  ;

                  }else{
                    $subtotal_global =  $formulario->fortyhc *  $global->ammount;
                    $totalAmmount = ($formulario->fortyhc *  $global->ammount) / $rateMountG;
                    $totalDestiny += $totalAmmount;
                    $cantidadT = $formulario->fortyhc;
                  }
                  // MARKUP
                  if($localPercentage != 0){
                    $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup ;
                    $arraymarkupFH = array("markup" => $markup , "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                  }else{
                    $markup =$localAmmount;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup;
                    $arraymarkupFH = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                  }
                  $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloDest = array('surcharge_terms' => $global->surcharge->SaleTermSurcharges->saleterm->name,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->fortyhc , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\' HC Global ', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
                  $arregloDest = array_merge($arregloDest,$arraymarkupFH);
                  $destFortyHcGlo["destiny"] = $arregloDest;
                  $collectionGloDest->push($destFortyHcGlo);
                }
                if($global->typedestiny_id == '3'){
                  if($global->calculationtype_id == "4"  ){
                    $subtotal_global = ($formulario->fortyhc *  $global->ammount) * 2 ;
                    $totalAmmount = (($formulario->fortyhc *  $global->ammount) * 2)  / $rateMountG ;
                    $cantidadT = $formulario->fortyhc +  $formulario->fortyhc;
                  }else{
                    $subtotal_global =  $formulario->fortyhc *  $global->ammount;
                    $totalAmmount = ($formulario->fortyhc *  $global->ammount) / $rateMountG;
                    $cantidadT = $formulario->fortyhc;

                  }
                  // MARKUP
                  if($localPercentage != 0){
                    $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup ;
                    $arraymarkupFH = array("markup" => $markup , "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                  }else{
                    $markup =$localAmmount;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup;
                    $arraymarkupFH = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                  }
                  $totalFreight += $totalAmmount;
                  $FreightCharges += $totalAmmount;
                  $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloFreight =  array('surcharge_terms' => $global->surcharge->SaleTermSurcharges->saleterm->name,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->fortyhc , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\' HC Global ', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
                  $arregloFreight = array_merge($arregloFreight,$arraymarkupFH);
                  $freighFortyHcGlo["freight"] =$arregloFreight;
                  $collectionGloFreight->push($freighFortyHcGlo);
                }
              }
            }
          }
        }
        if($global->calculationtype_id == "6"){
          foreach($global->globalcharcarrier as $carrierGlobal){
            if($carrierGlobal->carrier_id == $data->carrier_id ){
              if($global->typedestiny_id == '1'){
                $subtotal_global = $global->ammount;
                $totalAmmount =  $global->ammount / $rateMountG;
                $cantidadT = 1;
                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupPC = array("markup" => $markup , "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup;
                  $arraymarkupPC = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                }
                $totalOrigin += $totalAmmount ;
                $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $arregloOrig =  array('surcharge_terms' => $global->surcharge->SaleTermSurcharges->saleterm->name,'surcharge_name' => $global->surcharge->name,'cantidad' => "-" , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=> 'Shipment Global ', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
                $arregloOrig = array_merge($arregloOrig,$arraymarkupPC);
                $origPerGlo["origin"] =$arregloOrig;
                $collectionGloOrig->push($origPerGlo);
              }
              if($global->typedestiny_id == '2'){
                $subtotal_global = $global->ammount;
                $totalAmmount =  $global->ammount / $rateMountG;
                $cantidadT = 1;
                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupPC = array("markup" => $markup , "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup;
                  $arraymarkupPC = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                }
                $totalDestiny += $totalAmmount;
                $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $arregloDest = array('surcharge_terms' => $global->surcharge->SaleTermSurcharges->saleterm->name,'surcharge_name' => $global->surcharge->name,'cantidad' => "-" , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=> 'Shipment Global ', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
                $arregloDest = array_merge($arregloDest,$arraymarkupPC);
                $destPerGlo["destiny"] = $arregloDest;
                $collectionGloDest->push($destPerGlo);
              }
              if($global->typedestiny_id == '3'){
                $subtotal_global = $global->ammount;
                $totalAmmount =  $global->ammount / $rateMountG;
                $cantidadT = 1;

                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupPC = array("markup" => $markup , "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup;
                  $arraymarkupPC = array("markup" => $markup , "typemarkup" => $typeCurrency) ;
                }
                $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $totalFreight += $totalAmmount;
                $FreightCharges += $totalAmmount;

                $arregloFreight = array('surcharge_terms' => $global->surcharge->SaleTermSurcharges->saleterm->name,'surcharge_name' => $global->surcharge->name,'cantidad' => "-" , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=> 'Shipment Global ', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
                $arregloFreight = array_merge($arregloFreight,$arraymarkupPC);
                $freightPerGlo["freight"] = $arregloFreight;
                $collectionGloFreight->push($freightPerGlo);
              }
            }
          }
        }
      }

      // fin calculo Global charges 
      //#######################################################################
      // Armar los schedules
      try{
        $url = "http://schedules.cargofive.com/schedule/".strtolower($data->carrier->name)."/".$data->port_origin->code."/".$data->port_destiny->code;

        $client = new Client();
        $res = $client->request('GET', $url, [

        ]);
        $schedules = Collection::make(json_decode($res->getBody()));
        //  $schedules= $schedules->where($schedules->schedules->Etd,'2018-07-16');
        $schedulesArr = new Collection();
        $schedulesFin = new Collection();
        if(!$schedules->isEmpty()){
          foreach($schedules['schedules'] as $schedules){
            $collectS = Collection::make($schedules);
            $days =  $this->dias_transcurridos($schedules->Eta,$schedules->Etd);
            $collectS->put('days',$days);
            if($schedules->Transfer > 1){
              $collectS->put('type','Scale');
            }else{
              $collectS->put('type','Direct');
            }

            $schedulesArr->push($collectS);
          }
          //'2018-07-24'
          $dateSchedule = strtotime($date);
          $dateSchedule =  date('Y-m-d',$dateSchedule);

          if(!$schedulesArr->isEmpty()){ 
            $schedulesArr =  $schedulesArr->where('Etd','>=', $dateSchedule)->first();
            $schedulesFin->push($schedulesArr);
          }
        }
      }catch (\Guzzle\Http\Exception\ConnectException $e) {

      }



      //#######################################################################

      //Formato subtotales y operacion total quote 
      $totalFreight =  number_format($totalFreight, 2, '.', '');
      $FreightCharges =  number_format($FreightCharges, 2, '.', '');
      $totalOrigin  =  number_format($totalOrigin, 2, '.', '');
      $totalDestiny =  number_format($totalDestiny, 2, '.', '');
      $totalQuote = $totalFreight + $totalOrigin + $totalDestiny;
      // Variables suma de todo los origin o destiny 

      $totalChargeDest = 0;
      $totalChargeOrig = 0;
      $totalInland = 0;
      if(!empty($inlandOrigin)){
        foreach($inlandOrigin as $inlandOrig){
          if($inlandOrig['port_id'] == $data->port_origin->id ){
            $totalQuote += $inlandOrig['monto'];
            $totalChargeOrig += $inlandOrig['monto'];
            $totalInland += $inlandOrig['monto'];
          }
        }
      }else{
        $inlandOrigin = array();
      }
      if(!empty($inlandDestiny)){
        foreach($inlandDestiny as $inlandDest){
          if($inlandDest['port_id'] == $data->port_destiny->id ){
            $totalQuote += $inlandDest['monto'];
            $totalChargeDest += $inlandDest['monto'];
            $totalInland +=  $inlandDest['monto'];
          }
        }
      }else{
        $inlandDestiny =  array();
      }

      $totalChargeOrig += $totalOrigin;
      $totalChargeDest += $totalDestiny;

      $totalFreight = $totalFreight." ".$typeCurrency;
      $totalOrigin = $totalOrigin." ".$typeCurrency;
      $totalDestiny = $totalDestiny." ".$typeCurrency;
      $totalQuoteSin = number_format($totalQuote, 2, ',', '');
      $totalQuote = $totalQuote." ".$typeCurrency;
      $quoteCurrency = $typeCurrency;
      $totalInland = number_format($totalInland, 2, ',', '');



      $data->setAttribute('globalOrig',$collectionGloOrig);
      $data->setAttribute('globalDest',$collectionGloDest);
      $data->setAttribute('globalFreight',$collectionGloFreight);

      $data->setAttribute('localOrig',$collectionOrig);
      $data->setAttribute('localDest',$collectionDest);
      $data->setAttribute('localFreight',$collectionFreight);
      $data->setAttribute('totalFreight',$totalFreight);
      $data->setAttribute('freightCharges',$FreightCharges);
      $data->setAttribute('totalrates',$totalRates);
      $data->setAttribute('totalOrigin',$totalOrigin);
      $data->setAttribute('totalDestiny',$totalDestiny);
      $data->setAttribute('totalQuote',$totalQuote);
      // INLANDS 
      $data->setAttribute('inlandDestiny',$inlandDestiny);
      $data->setAttribute('inlandOrigin',$inlandOrigin);
      $data->setAttribute('totalChargeOrig',$totalChargeOrig);
      $data->setAttribute('totalChargeDest',$totalChargeDest);
      $data->setAttribute('totalInland',$totalInland);
      //Total quote atributes
      $data->setAttribute('totalQuoteSin',$totalQuoteSin);
      $data->setAttribute('quoteCurrency',$quoteCurrency);
      $data->setAttribute('idCurrency',$idCurrency);
      // SCHEDULES
      $data->setAttribute('schedulesFin',$schedulesFin);

    }



    $form  = $request->all();
    $objharbor = new Harbor();
    $harbor = $objharbor->all()->pluck('name','id');
    return view('quotation/index', compact('harbor','formulario','arreglo','inlandDestiny','inlandOrigin','form'));


  }


  public function create()
  {
    $company_user='';
    $companies='';
    $saleterms = '';
    $currencies = '';
    $currency_cfg = '';
    $exchange = '';
    $email_templates = '';
    
    $company_user_id=\Auth::user()->company_user_id;
    $quotes = Quote::all();
    $harbors = Harbor::all()->pluck('name','id');
    $countries = Country::all()->pluck('name','id');
    $prices = Price::all()->pluck('name','id');
    $user = User::where('id',\Auth::id())->first();
    $incoterm = Incoterm::pluck('name','id');
    
    if($company_user_id){
      $company_user=CompanyUser::find($company_user_id);
      $email_templates = EmailTemplate::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');
      $companies=Company::where('company_user_id',$company_user->id)->pluck('business_name','id');
      $saleterms = SaleTerm::where('company_user_id','=',\Auth::user()->company_user_id)->pluck('name','id');
    }

    if($company_user){
      $currencies = Currency::pluck('alphacode','id');
      $currency_cfg = Currency::find($company_user->currency_id);
    }

    if(\Auth::user()->company_user_id && $currency_cfg != ''){
      if($currency_cfg->alphacode=='USD'){
        $exchange = Currency::where('api_code_eur','EURUSD')->first();
      }else{
        $exchange = Currency::where('api_code','USDEUR')->first();
      }
    }

    return view('quotes/add', ['companies' => $companies,'quotes'=>$quotes,'countries'=>$countries,'harbors'=>$harbors,'prices'=>$prices,'company_user'=>$user,'currencies'=>$currencies,'currency_cfg'=>$currency_cfg,'exchange'=>$exchange,'incoterm'=>$incoterm,'saleterms'=>$saleterms,'email_templates'=>$email_templates]);

  }


  /**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
  public function edit($id)
  {
    $email_templates='';

    $quote = Quote::findOrFail($id);
    $companies = Company::where('company_user_id',\Auth::user()->company_user_id)->pluck('business_name','id');
    $harbors = Harbor::all()->pluck('name','id');
    $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
    $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
    $prices = Price::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');
    $contacts = Contact::where('company_id',$quote->company_id)->pluck('first_name','id');
    $origin_ammounts = OriginAmmount::where('quote_id',$quote->id)->get();
    $freight_ammounts = FreightAmmount::where('quote_id',$quote->id)->get();
    $destination_ammounts = DestinationAmmount::where('quote_id',$quote->id)->get();
    $saleterms = SaleTerm::where('company_user_id','=',\Auth::user()->company_user_id)->pluck('name','id');
    $currencies = Currency::pluck('alphacode','id');
    if(\Auth::user()->company_user_id){
      $company_user=CompanyUser::find(\Auth::user()->company_user_id);
      $currency_cfg = Currency::find($company_user->currency_id);
      $email_templates = EmailTemplate::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');
      if($currency_cfg->alphacode=='USD'){
        $exchange = Currency::where('api_code_eur','EURUSD')->first();
      }else{
        $exchange = Currency::where('api_code','USDEUR')->first();
      }
    }
    $incoterm = Incoterm::pluck('name','id');

    return view('quotes/edit', ['companies' => $companies,'quote'=>$quote,'harbors'=>$harbors,
      'prices'=>$prices,'contacts'=>$contacts,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'currencies'=>$currencies,'currency_cfg'=>$currency_cfg,'exchange'=>$exchange,'incoterm'=>$incoterm,'saleterms'=>$saleterms,'email_templates'=>$email_templates]);

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

    $total_markup_origin=array_values( array_filter($input['origin_ammount_markup']) );
    $total_markup_freight=array_values( array_filter($input['freight_ammount_markup']) );
    $total_markup_destination=array_values( array_filter($input['destination_ammount_markup']) );

    $sum_markup_origin=array_sum($total_markup_origin);
    $sum_markup_freight=array_sum($total_markup_freight);
    $sum_markup_destination=array_sum($total_markup_destination);

    $currency = CompanyUser::where('id',\Auth::user()->company_user_id)->first();
    $request->request->add(['owner' => \Auth::id(),'currency_id'=>$currency->currency_id,'total_markup_origin'=>$sum_markup_origin,'total_markup_freight'=>$sum_markup_freight,'total_markup_destination'=>$sum_markup_destination]);
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

    if(isset($input['schedule'])){
      if($input['schedule'] != 'null'){
        $schedules = json_decode($input['schedule']);

        foreach( $schedules as $schedule){ 
          $sche = json_decode($schedule);
          $dias = $this->dias_transcurridos($sche->Eta,$sche->Etd);

          $saveSchedule  = new Schedule();
          $saveSchedule->vessel = $sche->VesselName;
          $saveSchedule->etd = $sche->Etd;
          $saveSchedule->transit_time =  $dias;
          $saveSchedule->eta = $sche->Eta;
          $saveSchedule->type = 'direct';
          $saveSchedule->quotes()->associate($quote);
          $saveSchedule->save(); 
        }
      }
    }
    // Schedule manual 
    if(isset($input['schedule_manual'])){
      if($input['schedule_manual'] != 'null'){
        $sche = json_decode($input['schedule_manual']);
       // dd($sche);
        $dias = $this->dias_transcurridos($sche->Eta,$sche->Etd);

        $saveSchedule  = new Schedule();
        $saveSchedule->vessel = $sche->VesselName;
        $saveSchedule->etd = $sche->Etd;
        $saveSchedule->transit_time =  $dias;
        $saveSchedule->eta = $sche->Eta;
        $saveSchedule->type = 'direct';
        $saveSchedule->quotes()->associate($quote);
        $saveSchedule->save(); 

      }
    }

    if(isset($input['type_load_cargo']) && isset($input['quantity'])){
      $quantity = array_values( array_filter($input['quantity']) );
      $type_cargo = array_values( array_filter($input['type_load_cargo']) );
      $height = array_values( array_filter($input['height']) );
      $width = array_values( array_filter($input['width']) );
      $large = array_values( array_filter($input['large']) );
      $weight = array_values( array_filter($input['weight']) );
      $volume = array_values( array_filter($input['volume']) );
      
      foreach($type_cargo as $key=>$item){
        $package_load = new PackageLoad();
        $package_load->quote_id = $quote->id;
        $package_load->type_cargo = $type_cargo[$key];
        $package_load->quantity = $quantity[$key];
        $package_load->height = $height[$key];
        $package_load->width = $width[$key];
        $package_load->large = $large[$key];
        $package_load->weight = $weight[$key];
        $package_load->volume = $volume[$key];
        
        $package_load->save();
      }
    }

    $request->session()->flash('message.nivel', 'success');
    $request->session()->flash('message.title', 'Well done!');
    $request->session()->flash('message.content', 'Register completed successfully!');
    //return redirect()->route('quotes.index');
    return redirect()->action('QuoteController@show',$quote->id);
  }

    /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
    public function storeWithEmail(Request $request)
    {

      $input = Input::all();

      $currency = CompanyUser::where('id',\Auth::user()->company_user_id)->first();
      $request->request->add(['owner' => \Auth::id(),'currency_id'=>$currency->currency_id,'status_quote_id'=>2]);
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

      if(isset($input['schedule'])){
        if($input['schedule'] != 'null'){
          $schedules = json_decode($input['schedule']);

          foreach( $schedules as $schedule){ 
            $sche = json_decode($schedule);
            $dias = $this->dias_transcurridos($sche->Eta,$sche->Etd);

            $saveSchedule  = new Schedule();
            $saveSchedule->vessel = $sche->VesselName;
            $saveSchedule->etd = $sche->Etd;
            $saveSchedule->transit_time =  $dias;
            $saveSchedule->eta = $sche->Eta;
            $saveSchedule->type = 'direct';
            $saveSchedule->quotes()->associate($quote);
            $saveSchedule->save(); 
          }
        }
      }
    // Schedule manual 
      if(isset($input['schedule_manual'])){
        if($input['schedule_manual'] != 'null'){
          $sche = json_decode($input['schedule_manual']);
       // dd($sche);
          $dias = $this->dias_transcurridos($sche->Eta,$sche->Etd);

          $saveSchedule  = new Schedule();
          $saveSchedule->vessel = $sche->VesselName;
          $saveSchedule->etd = $sche->Etd;
          $saveSchedule->transit_time =  $dias;
          $saveSchedule->eta = $sche->Eta;
          $saveSchedule->type = 'direct';
          $saveSchedule->quotes()->associate($quote);
          $saveSchedule->save(); 

        }
      }

      //Storing packages loads details

      if(isset($input['type_load_cargo']) && isset($input['quantity'])){
        $quantity = array_values( array_filter($input['quantity']) );
        $type_cargo = array_values( array_filter($input['type_load_cargo']) );
        $height = array_values( array_filter($input['height']) );
        $width = array_values( array_filter($input['width']) );
        $large = array_values( array_filter($input['large']) );
        $weight = array_values( array_filter($input['weight']) );
        $volume = array_values( array_filter($input['volume']) );

        foreach($type_cargo as $key=>$item){
          $package_load = new PackageLoad();
          $package_load->quote_id = $quote->id;
          $package_load->type_cargo = $type_cargo[$key];
          $package_load->quantity = $quantity[$key];
          $package_load->height = $height[$key];
          $package_load->width = $width[$key];
          $package_load->large = $large[$key];
          $package_load->weight = $weight[$key];
          $package_load->volume = $volume[$key];

          $package_load->save();
        }
      }
      
      //Sending email

      if(isset($input['subject']) && isset($input['body'])){
        $subject = $input['subject'];
        $body = $input['body'];
        $contact_email = Contact::find($quote->contact_id);
        $companies = Company::all()->pluck('business_name','id');
        $harbors = Harbor::all()->pluck('name','id');
        $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
        $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
        $prices = Price::all()->pluck('name','id');
        $contacts = Contact::where('company_id',$quote->company_id)->pluck('first_name','id');
        $origin_ammounts = OriginAmmount::where('quote_id',$quote->id)->get();
        $freight_ammounts = FreightAmmount::where('quote_id',$quote->id)->get();
        $destination_ammounts = DestinationAmmount::where('quote_id',$quote->id)->get();
        $user = User::where('id',\Auth::id())->with('companyUser')->first();
        if(\Auth::user()->company_user_id){
          $company_user=CompanyUser::find(\Auth::user()->company_user_id);
          $currency_cfg = Currency::find($company_user->currency_id);
        }        
        $view = \View::make('quotes.pdf.index', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,
          'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg]);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->save('pdf/temp_'.$quote->id.'.pdf');

        \Mail::to($contact_email->email)->send(new SendQuotePdf($subject,$body,$quote));
      }

      $request->session()->flash('message.nivel', 'success');
      $request->session()->flash('message.title', 'Well done!');
      $request->session()->flash('message.content', 'Register completed successfully!');

      return redirect()->action('QuoteController@show',$quote->id);
    }


    function dias_transcurridos($fecha_i,$fecha_f)
    {
      $dias	= (strtotime($fecha_i)-strtotime($fecha_f))/86400;
      $dias 	= abs($dias); $dias = floor($dias);		
      return intval($dias);
    }

  /**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
  public function show($id)
  {

    $currency_cfg='';
    $company_user='';
    $email_templates='';
    $exchange='';
    $companies='';
    $prices='';
    $terms_origin='';
    $terms_destination='';

    $quote = Quote::findOrFail($id);
    $harbors = Harbor::all()->pluck('name','id');
    $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
    $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
    $contacts = Contact::where('company_id',$quote->company_id)->pluck('first_name','id');
    $origin_ammounts = OriginAmmount::where('quote_id',$quote->id)->get();
    $freight_ammounts = FreightAmmount::where('quote_id',$quote->id)->get();
    $destination_ammounts = DestinationAmmount::where('quote_id',$quote->id)->get();
    $user = User::where('id',\Auth::id())->with('companyUser')->first();
    $status_quotes=StatusQuote::all()->pluck('name','id');
    $currencies = Currency::pluck('alphacode','id');

    $package_loads = PackageLoad::where('quote_id',$id)->get();

    if(\Auth::user()->company_user_id){
      $terms_origin = TermsPort::where('port_id',$quote->origin_harbor_id)->with('term')->whereHas('term', function($q)  {
        $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
      })->get();
      $terms_destination = TermsPort::where('port_id',$quote->destination_harbor_id)->with('term')->whereHas('term', function($q)  {
        $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
      })->get();
      $email_templates=EmailTemplate::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');
      $company_user=CompanyUser::find(\Auth::user()->company_user_id);
      $currency_cfg = Currency::find($company_user->currency_id);
      $prices = Price::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');
      $companies = Company::where('company_user_id',\Auth::user()->company_user_id)->pluck('business_name','id');
      if($currency_cfg->alphacode=='USD'){
        $exchange = Currency::where('api_code_eur','EURUSD')->first();
      }else{
        $exchange = Currency::where('api_code','USDEUR')->first();
      }
    }


    return view('quotes/show', ['companies' => $companies,'quote'=>$quote,'harbors'=>$harbors,
      'prices'=>$prices,'contacts'=>$contacts,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,
      'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'currencies'=>$currencies,'currency_cfg'=>$currency_cfg,'user'=>$user,'status_quotes'=>$status_quotes,'exchange'=>$exchange,'email_templates'=>$email_templates,'package_loads'=>$package_loads]);

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

  public function getQuoteTerms($id)
  {
    $terms = TermAndCondition::where('harbor_id',$id)->first();
    return $terms;

  }

  public function duplicate(Request $request,$id)
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
    $quote_duplicate->currency_id=$quote->currency_id;
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
    $quote_duplicate->status_quote_id=$quote->status_quote_id;
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
    if($request->ajax()){
      return response()->json(['message' => 'Ok']);
    }else{
      return redirect()->route('quotes.index', compact(['companies' => $companies,'quotes'=>$quotes,'countries'=>$countries,'harbors'=>$harbors]));
    }
  }

  public function updateStatus(Request $request,$id)
  {
    $quote=Quote::findOrFail($id);
    $quote->status_quote_id=$request->status_quote_id;
    $quote->update();

    $quotes = Quote::all();
    $companies = Company::all()->pluck('business_name','id');
    $harbors = Harbor::all()->pluck('name','id');
    $countries = Country::all()->pluck('name','id');

    if($request->ajax()){
      return response()->json(['message' => 'Ok']);
    }else{
      return redirect()->route('quotes.index', compact(['companies' => $companies,'quotes'=>$quotes,'countries'=>$countries,'harbors'=>$harbors]));
    }

  }
  public function changeStatus(Request $request)
  {
    $id = $request->id;
    $quote=Quote::findOrFail($id);
    $status_quotes=StatusQuote::pluck('name','id');

    return view('quotes.changeStatus',compact('quote','status_quotes'));
  }
  public function scheduleManual($orig_port,$dest_port,$date_pick)
  {

    $code_orig = $this->getHarborName($orig_port);
    $code_dest = $this->getHarborName($dest_port);
    $date  = $date_pick;
    $carrier = 'maersk';

    // Armar los schedules
    try{
      $url = "http://schedules.cargofive.com/schedule/".$carrier."/".$code_orig->code."/".$code_dest->code;


      $client = new Client();
      $res = $client->request('GET', $url, [

      ]);
      $schedules = Collection::make(json_decode($res->getBody()));
      //  $schedules= $schedules->where($schedules->schedules->Etd,'2018-07-16');
      $schedulesArr = new Collection();
      $schedulesFin = new Collection();
      if(!$schedules->isEmpty()){
        foreach($schedules['schedules'] as $schedules){
          $collectS = Collection::make($schedules);
          $days =  $this->dias_transcurridos($schedules->Eta,$schedules->Etd);
          $collectS->put('days',$days);
          if($schedules->Transfer > 1){
            $collectS->put('type','Scale');
          }else{
            $collectS->put('type','Direct');
          }

          $schedulesArr->push($collectS);

        }
        //'2018-07-24'
        $dateSchedule = strtotime($date);
        $dateSchedule =  date('Y-m-d',$dateSchedule);

        if(!$schedulesArr->isEmpty()){ 

          $schedulesArr =  $schedulesArr->where('Etd','>=', $dateSchedule)->first();
          $schedulesFin->push($schedulesArr);
        }
      }
    }catch (\Guzzle\Http\Exception\ConnectException $e) {

    }


    return view('quotes.scheduleInfo',compact('code_orig','code_dest','schedulesFin'));
  }
}