<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use App\Company;
use App\CompanyPrice;
use App\CompanyUser;
use App\Contact;
use App\Country;
use App\Currency;
use App\Price;
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
use App\Carrier;
use App\TermAndCondition;
use App\TermsPort;
use Illuminate\Support\Facades\Input;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Schedule;
use App\Incoterm;
use App\SaleTerm;
use App\EmailTemplate;
use App\PackageLoad;
use App\Mail\SendQuotePdf;
use App\Quote;
use App\SearchRate;
use App\SearchPort;
use App\Repositories\Schedules;

class QuoteAutomaticController extends Controller
{

  protected $schedules;

  public function __construct(Schedules $schedules)
  {
    $this->schedules = $schedules;
  }
  public function automatic(){
    $quotes = Quote::all();
    $company_user_id=\Auth::user()->company_user_id;
    $incoterm = Incoterm::pluck('name','id');
    if(\Auth::user()->hasRole('subuser')){
      $companies = Company::where('company_user_id','=',$company_user_id)->whereHas('groupUserCompanies', function($q)  {
        $q->where('user_id',\Auth::user()->id);
      })->orwhere('owner',\Auth::user()->id)->pluck('business_name','id');
    }else{
      $companies = Company::where('company_user_id','=',$company_user_id)->pluck('business_name','id');
    }

    $harbors = Harbor::get()->pluck('display_name','id_complete');

    $countries = Country::all()->pluck('name','id');


    $prices = Price::all()->pluck('name','id');
    $company_user = User::where('id',\Auth::id())->first();
    if(count($company_user->companyUser)>0) {
      $currency_name = Currency::where('id', $company_user->companyUser->currency_id)->first();
    }else{
      $currency_name = '';
    }
    $currencies = Currency::all()->pluck('alphacode','id');
    return view('quotation/new2', ['companies' => $companies,'quotes'=>$quotes,'countries'=>$countries,'harbors'=>$harbors,'prices'=>$prices,'company_user'=>$company_user,'currencies'=>$currencies,'currency_name'=>$currency_name,'incoterm' => $incoterm]);
  }

  public function test(Request $request){
    $info =$request->input('info');
    $info = json_decode($info);
    $form =$request->input('form');
    $schedules = $request->input('schedules');
    $form = json_decode($form);
    $companiesInfo = Company::where('id','=',$form->company_id_quote)->first();
    $contactInfo = Contact::where('id','=',$form->contact_id)->first();
    $company_user_id=\Auth::user()->company_user_id;
    $quotes = Quote::all();
    $company_user=CompanyUser::find($company_user_id);
    $companies=Company::where('company_user_id',$company_user->id)->pluck('business_name','id');
    $harbors = Harbor::all()->pluck('display_name','id');
    $countries = Country::all()->pluck('name','id');
    $currency = Currency::all()->pluck('alphacode','id');
    $prices = Price::all()->pluck('name','id');
    $user = User::where('id',\Auth::id())->with('companyUser')->first();
    $currencies = Currency::all();
    $currency_cfg = Currency::find($company_user->currency_id);
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
    if(\Auth::user()->company_user_id){
      $port_all = harbor::where('name','ALL')->first();
      $terms_all = TermsPort::where('port_id',$port_all->id)->with('term')->whereHas('term', function($q)  {
        $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
      })->get();
      $terms_origin = TermsPort::where('port_id',$info->origin_port)->with('term')->whereHas('term', function($q)  {
        $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
      })->get();
      $terms_destination = TermsPort::where('port_id',$info->destiny_port)->with('term')->whereHas('term', function($q)  {
        $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
      })->get();
    }


    $emaildimanicdata = json_encode([
      'quote_bool'   => 'false',
      'company_id'   => $companiesInfo->id,
      'contact_id'   => $contactInfo->id,
      'quote_id'     => ''
    ]);

    return view('quotation/add', ['companies' => $companies,'quotes'=>$quotes,'countries'=>$countries,'harbors'=>$harbors,'prices'=>$prices,'company_user'=>$user,'currencies'=>$currencies,'currency_cfg'=>$currency_cfg,'info'=> $info,'form' => $form ,'currency' => $currency , 'schedules' => $schedules ,'exchange'=>$exchange ,'email_templates'=>$email_templates,'user'=>$user,'companyInfo' => $companiesInfo , 'contactInfo' => $contactInfo ,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'terms_all'=>$terms_all,'emaildimanicdata'=>$emaildimanicdata]);
  }


  public function skipPluck($pluck)
  {
    $skips = ["[","]","\""];
    return str_replace($skips, '',$pluck);

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

  // Search save 

  public function storeSearch($origPort,$destPort,$pickUpDate){

    $searchRate = new SearchRate();
    $searchRate->pick_up_date  = $pickUpDate;
    $searchRate->user_id = \Auth::id();
    $searchRate->save();
    foreach($origPort as $orig => $valueOrig)
    {
      foreach($destPort as $dest => $valueDest)
      {
        $detailport = new SearchPort();
        $detailport->port_orig =$valueOrig; // $request->input('port_origlocal'.$contador.'.'.$orig);
        $detailport->port_dest = $valueDest;//$request->input('port_destlocal'.$contador.'.'.$dest);
        $detailport->search_rate()->associate($searchRate);
        $detailport->save();
      }

    }


  }


  public function inlandDistance($deliveyType,$direccion,$port_id,$currency,$type){

    $isInland = false; // sirve para crear el arreglo solo si la persona eligio una opcion valida en el combo

    // Destination Address
    if($deliveyType == "2" || $deliveyType == "4" ){ 
      $harborRate  = Harbor::where('id',$port_id)->first();
      $origin = $harborRate->coordinates;
      $destination = $direccion;

      $isInland = true;
    }else if($deliveyType == "3" || $deliveyType == "4" ){ 
      $harborRate  = Harbor::where('id',$port_id)->first();
      $origin = $direccion;
      $destination = $harborRate->coordinates;

      $isInland = true;
    }

    if($isInland){
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
        }
      }
      if(isset($km)){
        $kilometros = $km[0];
      }else{
        $kilometros = 1;
      }


      $arreglo =  array("prov_id" => '' ,"provider" => "Inland Haulage","providerName" => 'Inland Haulage' ,"port_id" => $harborRate->id,"port_name" =>  $harborRate->name ,"km" => $kilometros  , "monto" => '0.00' ,'type' => $type,'type_currency' => $currency ,'idCurrency' => $currency );


      $arrayDetail = array("cant_cont" => '1' , "sub_in" => '0.00', "des_in" => 'Inlands' ,'amount' => '0.00','currency' => 'USD' ,'price_unit' => '0') ; 
      $arraymarkupCero = array("markup" => "0.00" , "markupConvert" => "0.00", "typemarkup" => $currency);

      $arrayDetail = array_merge($arraymarkupCero,$arrayDetail);
      $arrayFinal[] = $arrayDetail;
      $arreglo['inlandDetails'] = $arrayFinal;

      $data[] =$arreglo;
      $collection = Collection::make($data);



      return $collection;
    }

    return array();
  }


  // COTIZACION AUTOMATICA

  public function listRate(Request $request){
    $company_user_id=\Auth::user()->company_user_id;
    $company = User::where('id',\Auth::id())->with('companyUser.currency')->first();
    $typeCurrency =  $company->companyUser->currency->alphacode ;
    $idCurrency = $company->companyUser->currency_id;

    //dd($company);
    // se separa el pais y el puerto 

    foreach($request->input('originport') as $origP){

      $infoOrig = explode("-", $origP);
      $origin_port[] = $infoOrig[0];
      $origin_country[] = $infoOrig[1];
    }
    foreach($request->input('destinyport') as $destP){

      $infoDest = explode("-", $destP);
      $destiny_port[] = $infoDest[0];
      $destiny_country[] = $infoDest[1];
    }

    // Save search 

    $this->storeSearch($origin_port,$destiny_port,$request->input('date'));

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
    $freighMarkup= 0;
    $localMarkup = 0;
    $inlandMarkup =0;
    $markupFreightCurre = $typeCurrency;
    $markupLocalCurre = $typeCurrency;
    $markupInlandCurre = $typeCurrency;
    // Calculo de los markups
    foreach($fclMarkup as $freight){
      // Freight
      $fclFreight = $freight->freight_markup->where('price_type_id','=',1);
      $freighPercentage = $this->skipPluck($fclFreight->pluck('percent_markup'));

      // markup currency
      $markupFreightCurre =  $this->skipPluck($fclFreight->pluck('currency'));
      // markup con el monto segun la moneda
      $freighMarkup = $this->ratesCurrency($markupFreightCurre,$typeCurrency);
      // Objeto con las propiedades del currency
      $markupFreightCurre = Currency::find($markupFreightCurre);
      $markupFreightCurre = $markupFreightCurre->alphacode;
      // Monto original
      $freighAmmount =  $this->skipPluck($fclFreight->pluck('fixed_markup'));
      // monto aplicado al currency
      $freighMarkup = $freighAmmount / $freighMarkup;
      $freighMarkup = number_format($freighMarkup, 2, '.', '');

      // Local y global
      $fclLocal = $freight->local_markup->where('price_type_id','=',1);
      // markup currency





      if($request->modality == "1"){
        $markupLocalCurre =  $this->skipPluck($fclLocal->pluck('currency_export'));
        // valor de la conversion segun la moneda
        $localMarkup = $this->ratesCurrency($markupLocalCurre,$typeCurrency);
        // Objeto con las propiedades del currency por monto fijo
        $markupLocalCurre = Currency::find($markupLocalCurre);
        $markupLocalCurre = $markupLocalCurre->alphacode;
        // En caso de ser Porcentaje
        $localPercentage = intval($this->skipPluck($fclLocal->pluck('percent_markup_export')));
        // Monto original
        $localAmmount =  intval($this->skipPluck($fclLocal->pluck('fixed_markup_export')));
        // monto aplicado al currency
        $localMarkup = $localAmmount / $localMarkup;
        $localMarkup = number_format($localMarkup, 2, '.', '');
      }else{
        $markupLocalCurre =  $this->skipPluck($fclLocal->pluck('currency_import'));
        // valor de la conversion segun la moneda
        $localMarkup = $this->ratesCurrency($markupLocalCurre,$typeCurrency);
        // Objeto con las propiedades del currency por monto fijo
        $markupLocalCurre = Currency::find($markupLocalCurre);
        $markupLocalCurre = $markupLocalCurre->alphacode;
        // en caso de ser porcentake
        $localPercentage = intval($this->skipPluck($fclLocal->pluck('percent_markup_import')));
        // monto original
        $localAmmount =  intval($this->skipPluck($fclLocal->pluck('fixed_markup_import')));
        // monto aplicado al currency
        $localMarkup = $localAmmount / $localMarkup;
        $localMarkup = number_format($localMarkup, 2, '.', '');
      }
      // Inlands
      $fclInland = $freight->inland_markup->where('price_type_id','=',1);
      if($request->modality == "1"){
        $markupInlandCurre =  $this->skipPluck($fclInland->pluck('currency_export'));
        // valor de la conversion segun la moneda
        $inlandMarkup = $this->ratesCurrency($markupInlandCurre,$typeCurrency);
        // Objeto con las propiedades del currency por monto fijo
        $markupInlandCurre = Currency::find($markupInlandCurre);
        $markupInlandCurre = $markupInlandCurre->alphacode;
        // en caso de ser porcentake
        $inlandPercentage = intval($this->skipPluck($fclInland->pluck('percent_markup_export')));
        // Monto original
        $inlandAmmount =  intval($this->skipPluck($fclInland->pluck('fixed_markup_export')));
        // monto aplicado al currency
        $inlandMarkup = $inlandAmmount / $inlandMarkup;
        $inlandMarkup = number_format($inlandMarkup, 2, '.', '');


      }else{
        $markupInlandCurre =  $this->skipPluck($fclInland->pluck('currency_import'));

        // valor de la conversion segun la moneda
        $inlandMarkup = $this->ratesCurrency($markupInlandCurre,$typeCurrency);

        // Objeto con las propiedades del currency por monto fijo
        $markupInlandCurre = Currency::find($markupInlandCurre);

        $markupInlandCurre = $markupInlandCurre->alphacode;
        // en caso de ser porcentake
        $inlandPercentage = intval($this->skipPluck($fclInland->pluck('percent_markup_import')));
        // monto original
        $inlandAmmount =  intval($this->skipPluck($fclInland->pluck('fixed_markup_import')));
        // monto aplicado al currency
        $inlandMarkup = $inlandAmmount / $inlandMarkup;

        $inlandMarkup = number_format($inlandMarkup, 2, '.', '');

      }
    }
    //--------------------------------------
    // Calculo de los inlands
    $modality_inland = $request->modality;
    $company_inland = $request->input('company_id_quote');
    $texto20 = 'Inland 20 x' .$request->input('twuenty'); 
    $texto40 = 'Inland 40 x' .$request->input('forty');
    $texto40hc = 'Inland 40HC x'. $request->input('fortyhc');
    // Destination Address
    if($delivery_type == "2" || $delivery_type == "4" ){ 

      $inlands = Inland::whereHas('inland_company_restriction', function($a) use($company_inland){
        $a->where('company_id', '=',$company_inland);
      })->orDoesntHave('inland_company_restriction')->whereHas('inlandports', function($q) use($destiny_port) {
        $q->whereIn('port', $destiny_port);
      })->where('company_user_id','=',$company_user_id)->with('inlandadditionalkms','inlandports.ports','inlanddetails.currency');

      $inlands->where(function ($query) use($modality_inland)  {
        $query->where('type',$modality_inland)->orwhere('type','3');
      });
      $inlands = $inlands->get();

      // se agregan los aditional km

      foreach($inlands as $inlandsValue){


        $km20 = true;
        $km40 = true;
        $km40hc = true;
        $inlandDetails;

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
                $distancia = floatval($km[0]);
                if($distancia < 1){
                  $distancia = 1;
                }
                foreach($inlandsValue->inlanddetails as $details){


                  $rateI = $this->ratesCurrency($details->currency->id,$typeCurrency);
                  if($details->type == 'twuenty' && $request->input('twuenty') != "0"){

                    if( $distancia >= $details->lower && $distancia  <= $details->upper){
                      $sub_20 = ($request->input('twuenty') * $details->ammount) / $rateI;
                      $monto += $sub_20;

                      $amount_inland = $request->input('twuenty') * $details->ammount;
                      $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');
                      $km20 = false;

                      // CALCULO MARKUPS 

                      if($inlandPercentage != 0){
                        $markup = ( $sub_20 *  $inlandPercentage ) / 100 ;
                        $markup = number_format($markup, 2, '.', '');
                        $monto += $markup ;
                        $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($inlandPercentage%)") ;
                      }else{
                        $markup =$inlandAmmount;
                        $markup = number_format($markup, 2, '.', '');
                        $sub_20 += $inlandMarkup;
                        $monto += $inlandMarkup;
                        $arraymarkupT = array("markup" => $markup , "markupConvert" => $inlandMarkup, "typemarkup" => $markupInlandCurre) ;
                      }
                      // FIN CALCULO MARKUPS 


                      $arrayInland20 = array("cant_cont" => $request->input('twuenty') , "sub_in" => $sub_20, "des_in" => $texto20  ,'amount' => $amount_inland ,'currency' => $details->currency->alphacode , 'price_unit' => $price_per_unit) ; 
                      $arrayInland20 = array_merge($arraymarkupT,$arrayInland20);
                      $inlandDetails[] = $arrayInland20;

                    }
                  }
                  if($details->type == 'forty' && $request->input('forty') != "0"){

                    if( $distancia >= $details->lower && $distancia  <= $details->upper){
                      $sub_40 = ($request->input('forty') * $details->ammount) / $rateI;
                      $monto += $sub_40;

                      $amount_inland = $request->input('forty') * $details->ammount;
                      $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');
                      $km40 = false;
                      // CALCULO MARKUPS 

                      if($inlandPercentage != 0){
                        $markup = ( $sub_40 *  $inlandPercentage ) / 100 ;
                        $markup = number_format($markup, 2, '.', '');
                        $monto += $markup ;
                        $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($inlandPercentage%)") ;
                      }else{
                        $markup =$inlandAmmount;
                        $markup = number_format($markup, 2, '.', '');
                        $sub_40 += $inlandMarkup;
                        $monto += $inlandMarkup;
                        $arraymarkupT = array("markup" => $markup , "markupConvert" => $inlandMarkup, "typemarkup" => $markupInlandCurre) ;
                      }
                      // FIN CALCULO MARKUPS 
                      $arrayInland40 = array("cant_cont" => $request->input('forty') , "sub_in" => $sub_40, "des_in" => $texto40 ,'amount' => $amount_inland ,'currency' => $details->currency->alphacode , 'price_unit' => $price_per_unit ) ;
                      $arrayInland40 = array_merge($arraymarkupT,$arrayInland40);
                      $inlandDetails[] = $arrayInland40;
                    }
                  }
                  if($details->type == 'fortyhc' && $request->input('fortyhc') != "0"){

                    if( $distancia >= $details->lower && $distancia  <= $details->upper){
                      $sub_40hc = ($request->input('fortyhc') * $details->ammount) / $rateI;
                      $monto += $sub_40hc;
                      $price_per_unit = number_format($details->ammount / $distancia, 2, '.', '');
                      $amount_inland = $request->input('fortyhc') * $details->ammount;
                      $km40hc = false;
                      // CALCULO MARKUPS 

                      if($inlandPercentage != 0){
                        $markup = ( $sub_40hc *  $inlandPercentage ) / 100 ;
                        $markup = number_format($markup, 2, '.', '');
                        $monto += $markup ;
                        $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($inlandPercentage%)") ;
                      }else{
                        $markup =$inlandAmmount;
                        $markup = number_format($markup, 2, '.', '');
                        $sub_40hc += $inlandMarkup;
                        $monto += $inlandMarkup;
                        $arraymarkupT = array("markup" => $markup , "markupConvert" => $inlandMarkup, "typemarkup" => $markupInlandCurre) ;
                      }
                      // FIN CALCULO MARKUPS 
                      $arrayInland40hc = array("cant_cont" => $request->input('fortyhc') , "sub_in" => $sub_40hc, "des_in" => $texto40hc,'amount' => $amount_inland,'currency' => $details->currency->alphacode , 'price_unit' => $price_per_unit ) ;
                      $arrayInland40hc = array_merge($arraymarkupT,$arrayInland40hc);
                      $inlandDetails[] = $arrayInland40hc;
                    }
                  }
                }
                // KILOMETROS ADICIONALES 

                if(isset($inlandsValue->inlandadditionalkms)){
                  $rateGeneral = $this->ratesCurrency($inlandsValue->inlandadditionalkms->currency_id,$typeCurrency);
                  if($km20 && $request->input('twuenty') != "0" ){
                    $montoKm = ($distancia * $inlandsValue->inlandadditionalkms->km_20) / $rateGeneral;
                    $sub_20 = $request->input('twuenty') * $montoKm;
                    $monto += $sub_20;
                    $amount_inland = ($distancia * $inlandsValue->inlandadditionalkms->km_20) * $request->input('twuenty');
                    $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');
                    $amount_inland = number_format($amount_inland, 2, '.', '');
                    // CALCULO MARKUPS 
                    if($inlandPercentage != 0){
                      $markup = ( $sub_20 *  $inlandPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $monto += $markup ;
                      $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($inlandPercentage%)") ;
                    }else{
                      $markup =$inlandAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $sub_20 += $inlandMarkup;
                      $monto += $inlandMarkup;
                      $arraymarkupT = array("markup" => $markup , "markupConvert" => $inlandMarkup, "typemarkup" => $markupInlandCurre) ;
                    }
                    // FIN CALCULO MARKUPS 
                    $sub_20 = number_format($sub_20, 2, '.', '');
                    $arrayInland20 = array("cant_cont" => $request->input('twuenty') , "sub_in" => $sub_20, "des_in" => $texto20 ,'amount' => $amount_inland ,'currency' =>$inlandsValue->inlandadditionalkms->currency->alphacode, 'price_unit' => $price_per_unit ) ;
                    $arrayInland20 = array_merge($arraymarkupT,$arrayInland20);
                    $inlandDetails[] = $arrayInland20;
                  }
                  if($km40 && $request->input('forty') != "0" ){
                    $montoKm = ($distancia * $inlandsValue->inlandadditionalkms->km_40) / $rateGeneral;
                    $sub_40 = $request->input('forty') * $montoKm;
                    $monto += $sub_40;
                    $amount_inland = ($distancia * $inlandsValue->inlandadditionalkms->km_40) * $request->input('forty');
                    $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');
                    $amount_inland = number_format($amount_inland, 2, '.', '');
                    // CALCULO MARKUPS 
                    if($inlandPercentage != 0){
                      $markup = ( $sub_40 *  $inlandPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $monto += $markup ;
                      $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($inlandPercentage%)") ;
                    }else{
                      $markup =$inlandAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $sub_40 += $inlandMarkup;
                      $monto += $inlandMarkup;
                      $arraymarkupT = array("markup" => $markup , "markupConvert" => $inlandMarkup, "typemarkup" => $markupInlandCurre) ;
                    }
                    // FIN CALCULO MARKUPS
                    $sub_40 = number_format($sub_40, 2, '.', '');
                    $arrayInland40 = array("cant_cont" => $request->input('forty') , "sub_in" => $sub_40, "des_in" =>  $texto40,'amount' => $amount_inland ,'currency' => $inlandsValue->inlandadditionalkms->currency->alphacode , 'price_unit' => $price_per_unit ) ;
                    $arrayInland40 = array_merge($arraymarkupT,$arrayInland40);
                    $inlandDetails[] = $arrayInland40;
                  }
                  if($km40hc && $request->input('fortyhc') != "0"){
                    $montoKm = ($distancia * $inlandsValue->inlandadditionalkms->km_40hc) / $rateGeneral;
                    $sub_40hc = $request->input('fortyhc') * $montoKm;
                    $monto += $sub_40hc;

                    $amount_inland = ($distancia * $inlandsValue->inlandadditionalkms->km_40hc) * $request->input('fortyhc');
                    $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');
                    $amount_inland = number_format($amount_inland, 2, '.', '');
                    // CALCULO MARKUPS 
                    if($inlandPercentage != 0){
                      $markup = ( $sub_40hc *  $inlandPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $monto += $markup ;
                      $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($inlandPercentage%)") ;
                    }else{
                      $markup =$inlandAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $sub_40hc += $inlandMarkup;
                      $monto += $inlandMarkup;
                      $arraymarkupT = array("markup" => $markup , "markupConvert" => $inlandMarkup, "typemarkup" => $markupInlandCurre) ;
                    }
                    // FIN CALCULO MARKUPS
                    $sub_40hc = number_format($sub_40hc, 2, '.', '');
                    $arrayInland40hc = array("cant_cont" => $request->input('fortyhc') , "sub_in" => $sub_40hc, "des_in" => $texto40hc,'amount' => $amount_inland ,'currency' => $typeCurrency , 'price_unit' => $price_per_unit ) ;
                    $arrayInland40hc = array_merge($arraymarkupT,$arrayInland40hc);
                    $inlandDetails[] = $arrayInland40hc;
                  }
                }
                $monto = number_format($monto, 2, '.', '');
                if($monto > 0){
                  $inlandDetails = Collection::make($inlandDetails);
                  $arregloInland =  array("prov_id" => $inlandsValue->id ,"provider" => "Inland Haulage","providerName" => $inlandsValue->provider ,"port_id" => $ports->ports->id,"port_name" =>  $ports->ports->name ,"km" => $distancia, "monto" => $monto ,'type' => 'Destiny Port To Door','type_currency' => $inlandsValue->inlandadditionalkms->currency->alphacode ,'idCurrency' => $inlandsValue->currency_id );

                  $arregloInland['inlandDetails'] = $inlandDetails->groupBy('currency')->map(function($item){
                    $minimoDetails = $item->where('sub_in', $item->min('sub_in'))->first();

                    return $minimoDetails;
                  });

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
        //dd($inlandDestiny); // filtraor por el minimo
      }

    }
    // Origin Addrees
    if($delivery_type == "3" || $delivery_type == "4" ){
      $inlands = Inland::whereHas('inland_company_restriction', function($a) use($company_inland){
        $a->where('company_id', '=',$company_inland);
      })->orDoesntHave('inland_company_restriction')->whereHas('inlandports', function($q) use($origin_port) {
        $q->whereIn('port', $origin_port);
      })->where('company_user_id','=',$company_user_id)->with('inlandadditionalkms','inlandports.ports','inlanddetails.currency');

      $inlands->where(function ($query) use($modality_inland) {
        $query->where('type',$modality_inland)->orwhere('type','3');
      });

      $inlands = $inlands->get();

      foreach($inlands as $inlandsValue){
        $km20 = true;
        $km40 = true;
        $km40hc = true;
        $inlandDetailsOrig;
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
                $distancia = floatval($km[0]);
                if($distancia < 1){
                  $distancia = 1;
                }
                foreach($inlandsValue->inlanddetails as $details){

                  $rateI = $this->ratesCurrency($details->currency->id,$typeCurrency);

                  if($details->type == 'twuenty' && $request->input('twuenty') != "0"){

                    if( $distancia >= $details->lower && $distancia  <= $details->upper){
                      $sub_20 = ($request->input('twuenty') * $details->ammount) / $rateI ;
                      $monto += $sub_20;

                      $amount_inland = $request->input('twuenty') * $details->ammount;
                      $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');


                      $km20 = false;
                      // CALCULO MARKUPS 
                      if($inlandPercentage != 0){
                        $markup = ( $sub_20 *  $inlandPercentage ) / 100 ;
                        $markup = number_format($markup, 2, '.', '');
                        $monto += $markup ;
                        $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($inlandPercentage%)") ;
                      }else{
                        $markup =$inlandAmmount;
                        $markup = number_format($markup, 2, '.', '');
                        $sub_20 += $inlandMarkup;
                        $monto += $inlandMarkup;
                        $arraymarkupT = array("markup" => $markup , "markupConvert" => $inlandMarkup, "typemarkup" => $markupInlandCurre) ;
                      }
                      // FIN CALCULO MARKUPS 
                      $arrayInland20 = array("cant_cont" => $request->input('twuenty') , "sub_in" => $sub_20, "des_in" => $texto20 ,'amount' => $amount_inland ,'currency' => $details->currency->alphacode , 'price_unit' => $price_per_unit ) ; 
                      $arrayInland20 = array_merge($arraymarkupT,$arrayInland20);
                      $inlandDetailsOrig[] = $arrayInland20;
                    }
                  }
                  if($details->type == 'forty' && $request->input('forty') != "0"){

                    if( $distancia >= $details->lower && $distancia  <= $details->upper){
                      $sub_40 = ($request->input('forty') * $details->ammount)  / $rateI;
                      $monto += $sub_40;
                      $amount_inland = $request->input('forty') * $details->ammount;
                      $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');

                      $km40 = false;
                      // CALCULO MARKUPS 
                      if($inlandPercentage != 0){
                        $markup = ( $sub_40 *  $inlandPercentage ) / 100 ;
                        $markup = number_format($markup, 2, '.', '');
                        $monto += $markup ;
                        $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($inlandPercentage%)") ;
                      }else{
                        $markup =$inlandAmmount;
                        $markup = number_format($markup, 2, '.', '');
                        $sub_40 += $inlandMarkup;
                        $monto += $inlandMarkup;
                        $arraymarkupT = array("markup" => $markup , "markupConvert" => $inlandMarkup, "typemarkup" => $markupInlandCurre) ;
                      }
                      // FIN CALCULO MARKUPS 
                      $arrayInland40 = array("cant_cont" => $request->input('forty') , "sub_in" => $sub_40, "des_in" => $texto40,'amount' => $amount_inland ,'currency' => $details->currency->alphacode , 'price_unit' => $price_per_unit ) ;
                      $arrayInland40 = array_merge($arraymarkupT,$arrayInland40);
                      $inlandDetailsOrig[] = $arrayInland40;
                    }
                  }
                  if($details->type == 'fortyhc' && $request->input('fortyhc') != "0"){

                    if( $distancia >= $details->lower && $distancia  <= $details->upper){
                      $sub_40hc = ($request->input('fortyhc') * $details->ammount) / $rateI;
                      $monto += $sub_40hc;
                      $amount_inland = $request->input('fortyhc') * $details->ammount;
                      $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');

                      $km40hc = false;
                      // CALCULO MARKUPS 
                      if($inlandPercentage != 0){
                        $markup = ( $sub_40hc *  $inlandPercentage ) / 100 ;
                        $markup = number_format($markup, 2, '.', '');
                        $monto += $markup ;
                        $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($inlandPercentage%)") ;
                      }else{
                        $markup =$inlandAmmount;
                        $markup = number_format($markup, 2, '.', '');
                        $sub_40hc += $inlandMarkup;
                        $monto += $inlandMarkup;
                        $arraymarkupT = array("markup" => $markup , "markupConvert" => $inlandMarkup, "typemarkup" => $markupInlandCurre) ;
                      }
                      // FIN CALCULO MARKUPS 
                      $arrayInland40hc = array("cant_cont" => $request->input('fortyhc') , "sub_in" => $sub_40hc, "des_in" => $texto40hc,'amount' => $amount_inland ,'currency' => $details->currency->alphacode , 'price_unit' => $price_per_unit ) ;
                      $arrayInland40hc = array_merge($arraymarkupT,$arrayInland40hc);
                      $inlandDetailsOrig[] = $arrayInland40hc;

                    }
                  }

                }
                // KILOMETROS ADICIONALES 
                if(isset($inlandsValue->inlandadditionalkms)){

                  $rateGeneral = $this->ratesCurrency($inlandsValue->inlandadditionalkms->currency_id,$typeCurrency);
                  if($km20 && $request->input('twuenty') != "0"){
                    $montoKm = ($distancia * $inlandsValue->inlandadditionalkms->km_20) / $rateGeneral;
                    $sub_20 = $request->input('twuenty') * $montoKm;
                    $monto += $sub_20;
                    $amount_inland = ($distancia * $inlandsValue->inlandadditionalkms->km_20) * $request->input('twuenty');
                    $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');
                    $amount_inland =  number_format($amount_inland , 2, '.', '');
                    // CALCULO MARKUPS 
                    if($inlandPercentage != 0){
                      $markup = ( $sub_20 *  $inlandPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $monto += $markup ;
                      $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($inlandPercentage%)") ;
                    }else{
                      $markup =$inlandAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $sub_20 += $inlandMarkup;
                      $monto += $inlandMarkup;
                      $arraymarkupT = array("markup" => $markup , "markupConvert" => $inlandMarkup, "typemarkup" => $markupInlandCurre) ;
                    }
                    // FIN CALCULO MARKUPS 
                    $sub_20 =  number_format($sub_20 , 2, '.', '');
                    $arrayInland20 = array("cant_cont" => $request->input('twuenty') , "sub_in" => $sub_20, "des_in" => $texto20,'amount' => $amount_inland,'currency' => $inlandsValue->inlandadditionalkms->currency->alphacode , 'price_unit' => $price_per_unit ) ;
                    $arrayInland20 = array_merge($arraymarkupT,$arrayInland20);
                    $inlandDetailsOrig[] = $arrayInland20;
                  }
                  if($km40 && $request->input('forty') != "0"){
                    $montoKm = ($distancia * $inlandsValue->inlandadditionalkms->km_40) / $rateGeneral;
                    $sub_40 = $request->input('forty') * $montoKm;
                    $monto += $sub_40;
                    $amount_inland = ($distancia * $inlandsValue->inlandadditionalkms->km_40) * $request->input('forty');
                    $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');
                    $amount_inland =  number_format($amount_inland , 2, '.', '');
                    // CALCULO MARKUPS 
                    if($inlandPercentage != 0){
                      $markup = ( $sub_40 *  $inlandPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $monto += $markup ;
                      $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($inlandPercentage%)") ;
                    }else{
                      $markup =$inlandAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $sub_40 += $inlandMarkup;
                      $monto += $inlandMarkup;
                      $arraymarkupT = array("markup" => $markup , "markupConvert" => $inlandMarkup, "typemarkup" => $markupInlandCurre) ;
                    }
                    // FIN CALCULO MARKUPS 
                    $sub_40 =  number_format($sub_40 , 2, '.', '');
                    $arrayInland40 = array("cant_cont" => $request->input('forty') , "sub_in" => $sub_40, "des_in" => $texto40 ,'amount' => $amount_inland,'currency' => $inlandsValue->inlandadditionalkms->currency->alphacode  , 'price_unit' => $price_per_unit ) ;
                    $arrayInland40 = array_merge($arraymarkupT,$arrayInland40);
                    $inlandDetailsOrig[] = $arrayInland40;

                  }
                  if($km40hc && $request->input('fortyhc') != "0"){
                    $montoKm = ($distancia * $inlandsValue->inlandadditionalkms->km_40hc) / $rateGeneral;
                    $sub_40hc = $request->input('fortyhc') * $montoKm;
                    $monto += $sub_40hc;
                    $amount_inland = ($distancia * $inlandsValue->inlandadditionalkms->km_40hc) * $request->input('fortyhc');
                    $amount_inland =  number_format($amount_inland , 2, '.', '');
                    $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');

                    // CALCULO MARKUPS 
                    if($inlandPercentage != 0){
                      $markup = ( $sub_40hc *  $inlandPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $monto += $markup ;
                      $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($inlandPercentage%)") ;
                    }else{
                      $markup =$inlandAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $sub_40hc += $inlandMarkup;
                      $monto += $inlandMarkup;
                      $arraymarkupT = array("markup" => $markup , "markupConvert" => $inlandMarkup, "typemarkup" => $markupInlandCurre) ;
                    }
                    // FIN CALCULO MARKUPS 
                    $sub_40hc =  number_format($sub_40hc , 2, '.', '');
                    $arrayInland40hc = array("cant_cont" => $request->input('fortyhc') , "sub_in" => $sub_40hc, "des_in" => $texto40hc ,'amount' => $amount_inland,'currency' => $inlandsValue->inlandadditionalkms->currency->alphacode , 'price_unit' => $price_per_unit ) ;
                    $arrayInland40hc = array_merge($arraymarkupT,$arrayInland40hc);
                    $inlandDetailsOrig[] = $arrayInland40hc;
                  }
                }


                $monto = number_format($monto, 2, '.', '');
                if($monto > 0){
                  $inlandDetailsOrig = Collection::make($inlandDetailsOrig);

                  $arregloInland = array("prov_id" => $inlandsValue->id ,"provider" => "Inland Haulage","providerName" => $inlandsValue->provider ,"port_id" => $ports->ports->id,"port_name" =>  $ports->ports->name ,"km" => $distancia , "monto" => $monto ,'type' => 'Origin Port To Door','type_currency' => $typeCurrency ,'idCurrency' => $inlandsValue->currency_id  );

                  $arregloInland['inlandDetails'] = $inlandDetailsOrig->groupBy('currency')->map(function($item){

                    $minimoDetails = $item->where('sub_in', $item->min('sub_in'))->first();

                    return $minimoDetails;
                  });
                  $dataOrig[] = $arregloInland;
                }
              }//antes de esto 
            }
          } // if ports
        }// foreach ports
      }//foreach inlands
      if(!empty($dataOrig)){
        $collectionOrig = Collection::make($dataOrig);
        //dd($collectionOrig); //  completo
        $inlandOrigin= $collectionOrig->groupBy('port_id')->map(function($item){
          $test = $item->where('monto', $item->min('monto'))->first();

          return $test;
        });
        //dd($inlandOrigin); // filtraor por el minimo
      }
    }// Fin del calculo de los inlands

    $date =  $request->input('date');
    $user_id =  \Auth::id();
    $company_user_id =  \Auth::user()->company_user_id;
    $company_id = $request->input('company_id_quote');

    $arreglo = Rate::whereIn('origin_port',$origin_port)->whereIn('destiny_port',$destiny_port)->with('port_origin','port_destiny','contract','carrier')->whereHas('contract', function($q) use($date,$user_id,$company_user_id,$company_id)
        {
          $q->whereHas('contract_user_restriction', function($a) use($user_id){
            $a->where('user_id', '=',$user_id);
          })->orDoesntHave('contract_user_restriction');
        })->whereHas('contract', function($q) use($date,$user_id,$company_user_id,$company_id)
                     {
                       $q->whereHas('contract_company_restriction', function($b) use($company_id){
                         $b->where('company_id', '=',$company_id);
                       })->orDoesntHave('contract_company_restriction');
                     })->whereHas('contract', function($q) use($date,$company_user_id){
      $q->where('validity', '<=',$date)->where('expire', '>=', $date)->where('company_user_id','=',$company_user_id);
    });

    // Se agregan las condiciones para evitar traer rates con ceros dependiendo de lo seleccionado por el usuario
    if($request->input('twuenty') != "0" ){
      $arreglo->where('twuenty' , '!=' , "0");
    }
    if($request->input('forty') != "0"){
      $arreglo->where('forty' , '!=' , "0");
    }
    if($request->input('fortyhc') != "0"){  
      $arreglo->where('fortyhc' , '!=' , "0");
    }
    if($request->input('fortynor') != "0"){  
      $arreglo->where('fortynor' , '!=' , "0");
    }


    if($request->input('fortyfive') != "0"){
      $arreglo->where('fortyfive' , '!=' , "0"); 
    }

    /*if($request->input('fortyhc') != "0"){  
      $arreglo->where(function ($query) {
        $query->where('fortyhc' , '!=' , "0")->orWhere('fortynor' , '!=' , "0"); 
      });
    } */ 

   
    $arreglo = $arreglo->get();

    // Fin condiciones del cero
    $formulario = $request;
    $array20 = array('2','4','5'); // id  calculation type 2 = per 20 , 4= per teu , 5 per container
    $array40 =  array('1','4','5'); // id  calculation type 2 = per 40 
    $array40Hc= array('3','4','5'); // id  calculation type 3 = per 40HC 
    $array40Nor = array('7','4','5');  // id  calculation type 7 = per 40NOR
    $array45 = array('8','4','5');  // id  calculation type 8 = per 45
    $collectionLocal = new Collection();
    foreach($arreglo as $data){
      $totalFreight = 0;
      $FreightCharges = 0;
      $totalRates = 0;
      $totalOrigin = 0;
      $totalDestiny =0;
      $totalQuote= 0;
      $totalAmmount = 0;
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
          $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($freighPercentage%)") ;
        }else{

          $markup =trim($freighAmmount);
          $markup = number_format($markup, 2, '.', '');
          $totalT += $freighMarkup;
          $arraymarkupT = array("markup" => $markup , "markupConvert" => $freighMarkup, "typemarkup" => $markupFreightCurre) ;
        }
        $totalT =  number_format($totalT, 2, '.', '');
        $totalFreight += $totalT;
        $totalRates += $totalT;
        $array = array('type'=>'Ocean Freight 20', 'cantidad' => $formulario->twuenty,'detail'=>'Container 20', 'price' => $data->twuenty, 'currency' => $data->currency->alphacode ,'subtotal' => $subtotalT , 'total' =>$totalT." ". $typeCurrency , 'idCurrency' => $data->currency_id );
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
          $arraymarkupF = array("markup" => $markup , "markupConvert" => $markup,  "typemarkup" => "$typeCurrency ($freighPercentage%)") ;
        }else{
          $markup =trim($freighAmmount);
          $markup = number_format($markup, 2, '.', '');
          $totalF += $freighMarkup;
          $arraymarkupF = array("markup" => $markup , "markupConvert" => $freighMarkup, "typemarkup" => $markupFreightCurre) ;
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
          $arraymarkupFH = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($freighPercentage%)") ;
        }else{
          $markup =trim($freighAmmount);
          $markup = number_format($markup, 2, '.', '');
          $totalFHC += $freighMarkup;
          $arraymarkupFH = array("markup" => $markup , "markupConvert" => $freighMarkup, "typemarkup" => $markupFreightCurre) ;
        }
        $totalFHC =  number_format($totalFHC, 2, '.', '');
        $totalFreight += $totalFHC;
        $totalRates += $totalFHC;
        $array = array('type'=>'Ocean Freight 40HC', 'cantidad' => $formulario->fortyhc,'detail'=>'Container 40HC', 'price' => $data->fortyhc, 'currency' => $data->currency->alphacode ,'subtotal' => $subtotalFHC , 'total' =>$totalFHC." ". $typeCurrency , 'idCurrency' => $data->currency_id);
        $array = array_merge($array,$arraymarkupFH);
        $data->setAttribute('montFHC',$array);
        $collectionRate->push($array);
      }
      //NUEVOS CONTENEDORES RATES
      if($request->input('fortynor') != "0" ) {
        $subtotalNOR = $formulario->fortynor *  $data->fortynor;
        $totalNOR = ($formulario->fortynor *  $data->fortynor)  / $rateC ;
        // MARKUPS
        if($freighPercentage != 0){
          $freighPercentage = intval($freighPercentage);
          $markup = ( $totalNOR *  $freighPercentage ) / 100 ;
          $markup = number_format($markup, 2, '.', '');
          $totalNOR += $markup ;
          $arraymarkupNOR = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($freighPercentage%)") ;
        }else{
          $markup =trim($freighAmmount);
          $markup = number_format($markup, 2, '.', '');
          $totalNOR += $freighMarkup;
          $arraymarkupNOR = array("markup" => $markup , "markupConvert" => $freighMarkup, "typemarkup" => $markupFreightCurre) ;
        }
        $totalNOR =  number_format($totalNOR, 2, '.', '');
        $totalFreight += $totalNOR;
        $totalRates += $totalNOR;
        $array = array('type'=>'Ocean Freight 40NOR ', 'cantidad' => $formulario->fortynor,'detail'=>'Container 40NOR', 'price' => $data->fortynor, 'currency' => $data->currency->alphacode ,'subtotal' => $subtotalNOR , 'total' =>$totalNOR." ". $typeCurrency , 'idCurrency' => $data->currency_id);
        $array = array_merge($array,$arraymarkupNOR);
        $data->setAttribute('montNOR',$array);
        $collectionRate->push($array);
      }
      if($request->input('fortyfive') != "0") {
        $subtotalFIVE = $formulario->fortyfive *  $data->fortyfive;
        $totalFIVE = ($formulario->fortyfive *  $data->fortyfive)  / $rateC ;
        // MARKUPS
        if($freighPercentage != 0){
          $freighPercentage = intval($freighPercentage);
          $markup = ( $totalFIVE *  $freighPercentage ) / 100 ;
          $markup = number_format($markup, 2, '.', '');
          $totalFIVE += $markup ;
          $arraymarkupFIVE = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($freighPercentage%)") ;
        }else{
          $markup =trim($freighAmmount);
          $markup = number_format($markup, 2, '.', '');
          $totalFIVE += $freighMarkup;
          $arraymarkupFIVE = array("markup" => $markup , "markupConvert" => $freighMarkup, "typemarkup" => $markupFreightCurre) ;
        }
        $totalFIVE =  number_format($totalFIVE, 2, '.', '');
        $totalFreight += $totalFIVE;
        $totalRates += $totalFIVE;
        $array = array('type'=>'Ocean Freight 45', 'cantidad' => $formulario->fortyfive,'detail'=>'Container 45', 'price' => $data->fortyfive, 'currency' => $data->currency->alphacode ,'subtotal' => $subtotalFIVE , 'total' =>$totalFIVE." ". $typeCurrency , 'idCurrency' => $data->currency_id);
        $array = array_merge($array,$arraymarkupFIVE);
        $data->setAttribute('montFIVE',$array);
        $collectionRate->push($array);
      }
      //####################################################################################
      $data->setAttribute('rates',$collectionRate);
      // id de los port  ALL
      array_push($orig_port,1485);
      array_push($dest_port,1485);
      // id de los carrier ALL 
      $carrier_all = 26;
      array_push($carrier,$carrier_all);
      // Id de los paises 
      array_push($origin_country,250);
      array_push($destiny_country,250);




      //  calculo de los local charges en freight , origin y destiny
      $localChar = LocalCharge::where('contract_id','=',$data->contract_id)->whereHas('localcharcarriers', function($q) use($carrier) {
        $q->whereIn('carrier_id', $carrier);
      })->where(function ($query) use($orig_port,$dest_port,$origin_country,$destiny_country){
        $query->whereHas('localcharports', function($q) use($orig_port,$dest_port) {
          $q->whereIn('port_orig', $orig_port)->whereIn('port_dest',$dest_port);
        })->orwhereHas('localcharcountries', function($q) use($origin_country,$destiny_country) {
          $q->whereIn('country_orig', $origin_country)->whereIn('country_dest', $destiny_country);
        });
      })->with('localcharports.portOrig','localcharcarriers.carrier','currency','surcharge.saleterm')->get();

      foreach($localChar as $local){
        $rateMount = $this->ratesCurrency($local->currency->id,$typeCurrency);
        // Condicion para enviar los terminos de venta o compra
        if(isset($local->surcharge->saleterm->name)){
          $terminos = $local->surcharge->saleterm->name;
        }else{
          $terminos = $local->surcharge->name;
        }
        if(in_array($local->calculationtype_id, $array20)){
          if($request->input('twuenty') != "0") {
            foreach($local->localcharcarriers as $carrierGlobal){
              if($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id ==  $carrier_all ){
                if($local->typedestiny_id == '1'){
                  $subtotal_local = $formulario->twuenty *  $local->ammount;
                  $totalAmmount = ($formulario->twuenty *  $local->ammount) / $rateMount ;
                  // MARKUP
                  if($localPercentage != 0){
                    $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup ;
                    $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                  }else{
                    $markup =$localAmmount;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $localMarkup;
                    $arraymarkupT = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                  }
                  $totalOrigin += $totalAmmount ;
                  $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloOrig = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->twuenty , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency , 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'20\'  Local ' , 'subtotal_local' => $subtotal_local , 'cantidadT' => $formulario->twuenty , 'idCurrency' => $local->currency->id);
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
                    $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                  }else{
                    $markup =$localAmmount;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $localMarkup;
                    $arraymarkupT = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                  }
                  $totalDestiny += $totalAmmount;
                  $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloDest =  array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->twuenty , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'20\'  Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $formulario->twuenty , 'idCurrency' => $local->currency->id );
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
                    $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                  }else{
                    $markup =$localAmmount;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $localMarkup;
                    $arraymarkupT = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                  }
                  $totalFreight += $totalAmmount;
                  $FreightCharges += $totalAmmount;
                  $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->twuenty , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'20\'  Local ' , 'subtotal_local' => $subtotal_local , 'cantidadT' => $formulario->twuenty , 'idCurrency' => $local->currency->id);
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
              if($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id ==  $carrier_all ){
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
                      $arraymarkupF = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $localMarkup;
                      $arraymarkupF = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                    }
                    $totalOrigin += $totalAmmount ;
                  }else{
                    $subtotal_local = $formulario->forty *  $local->ammount;
                    $totalAmmount = ($formulario->forty *  $local->ammount) / $rateMount ;
                    $cantidadT = $formulario->forty;
                    // MARKUP
                    if($localPercentage != 0){
                      $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup ;
                      $arraymarkupF = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $localMarkup;
                      $arraymarkupF = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                    }
                    $totalOrigin += $totalAmmount ;
                  }
                  $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloOrig =  array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->forty , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency , 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\'  Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id);
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
                      $arraymarkupF = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $localMarkup;
                      $arraymarkupF = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
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
                      $arraymarkupF = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $localMarkup;
                      $arraymarkupF = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                    }
                    $totalDestiny += $totalAmmount;
                  }
                  $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloDest =  array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->forty , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\'  Local ' , 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id  );
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
                      $arraymarkupF = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $localMarkup;
                      $arraymarkupF = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
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
                      $arraymarkupF = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $localMarkup;
                      $arraymarkupF = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                    }
                    $totalFreight += $totalAmmount;
                    $FreightCharges += $totalAmmount;
                  }
                  $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->forty , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'40\'  Local ' , 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id );
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
              if($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id ==  $carrier_all ){
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
                      $arraymarkupFH = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $localMarkup;
                      $arraymarkupFH = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
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
                      $arraymarkupFH = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $localMarkup;
                      $arraymarkupFH = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                    }
                    $totalOrigin += $totalAmmount ;
                  }
                  $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloOrig =  array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->fortyhc , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency , 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\' HC Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id  );
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
                      $arraymarkupFH = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $localMarkup;
                      $arraymarkupFH = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
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
                      $arraymarkupFH = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $localMarkup;
                      $arraymarkupFH = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                    }
                    $totalDestiny += $totalAmmount;
                  }
                  $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloDest = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->fortyhc , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\' HC Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id  );
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
                      $arraymarkupFH = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $localMarkup;
                      $arraymarkupFH = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
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
                      $arraymarkupFH = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $localMarkup;
                      $arraymarkupFH = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                    }
                    $totalFreight += $totalAmmount;
                    $FreightCharges += $totalAmmount;
                  }
                  $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->fortyhc , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\' HC Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id );
                  $arregloFreight = array_merge($arregloFreight,$arraymarkupFH);
                  $freighFortyHc["freight"] = $arregloFreight;
                  $collectionFreight->push($freighFortyHc);
                }
              }
            }
          }
        }

        //#######################################################################
        //NUEVOS CONTENEDORES 40nor , 45 LOCALCHARGE

        if(in_array($local->calculationtype_id, $array40Nor)){
          if($request->input('fortynor') != "0") {
            foreach($local->localcharcarriers as $carrierGlobal){
              if($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id ==  $carrier_all ){
                if($local->typedestiny_id == '1'){
                  if($local->calculationtype_id == "4"  ){
                    $subtotal_local = ($formulario->fortynor *  $local->ammount) * 2 ;
                    $totalAmmount = (($formulario->fortynor *  $local->ammount) * 2 ) / $rateMount ;
                    $cantidadT = $formulario->fortynor + $formulario->fortynor;
                    // MARKUP
                    if($localPercentage != 0){
                      $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup ;
                      $arraymarkupNor = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $localMarkup;
                      $arraymarkupNor = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                    }
                    $totalOrigin += $totalAmmount ;
                  }else{
                    $subtotal_local = $formulario->fortynor *  $local->ammount;
                    $totalAmmount = ($formulario->fortynor *  $local->ammount) / $rateMount ;
                    $cantidadT = $formulario->fortynor;
                    // MARKUP
                    if($localPercentage != 0){
                      $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup ;
                      $arraymarkupNor = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $localMarkup;
                      $arraymarkupNor = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                    }
                    $totalOrigin += $totalAmmount ;
                  }
                  $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloOrig =  array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->fortynor , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency , 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\'  Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id);
                  $arregloOrig = array_merge($arregloOrig,$arraymarkupNor);
                  $origFortyNor["origin"] =$arregloOrig;
                  $collectionOrig->push($origFortyNor);
                }
                if($local->typedestiny_id == '2'){
                  if($local->calculationtype_id == "4"  ){
                    $subtotal_local = ($formulario->fortynor *  $local->ammount) * 2 ;
                    $totalAmmount = (($formulario->fortynor *  $local->ammount) * 2 ) / $rateMount ;
                    $cantidadT = $formulario->fortynor + $formulario->fortynor;
                    // MARKUP
                    if($localPercentage != 0){
                      $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup ;
                      $arraymarkupNor = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $localMarkup;
                      $arraymarkupNor = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                    }
                    $totalDestiny += $totalAmmount;
                  }else{
                    $subtotal_local = $formulario->fortynor *  $local->ammount;
                    $totalAmmount = ($formulario->fortynor *  $local->ammount) / $rateMount ;
                    $cantidadT = $formulario->fortynor ;
                    // MARKUP
                    if($localPercentage != 0){
                      $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup ;
                      $arraymarkupNor = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $localMarkup;
                      $arraymarkupNor = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                    }
                    $totalDestiny += $totalAmmount;
                  }
                  $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloDest =  array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->fortynor , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\'  Local ' , 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id  );
                  $arregloDest = array_merge($arregloDest,$arraymarkupNor);
                  $destFortyNor["destiny"] =$arregloDest;
                  $collectionDest->push($destFortyNor);
                }
                if($local->typedestiny_id == '3'){
                  if($local->calculationtype_id == "4"  ){
                    $subtotal_local = ($formulario->fortynor *  $local->ammount) * 2 ;
                    $totalAmmount = (($formulario->fortynor *  $local->ammount) * 2 ) / $rateMount ;
                    $cantidadT = $formulario->fortynor + $formulario->fortynor;
                    // MARKUP
                    if($localPercentage != 0){
                      $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup ;
                      $arraymarkupNor = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $localMarkup;
                      $arraymarkupNor = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                    }
                    $totalFreight += $totalAmmount;
                    $FreightCharges += $totalAmmount;
                  }else{
                    $subtotal_local = $formulario->fortynor *  $local->ammount;
                    $totalAmmount = ($formulario->fortynor *  $local->ammount)/ $rateMount ;
                    $cantidadT = $formulario->fortynor;
                    // MARKUP
                    if($localPercentage != 0){
                      $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup ;
                      $arraymarkupNor = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $localMarkup;
                      $arraymarkupNor = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                    }
                    $totalFreight += $totalAmmount;
                    $FreightCharges += $totalAmmount;
                  }
                  $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->fortynor , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'40\'  Local ' , 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id );
                  $arregloFreight = array_merge($arregloFreight,$arraymarkupNor);
                  $freighFortyNor["freight"] = $arregloFreight;
                  $collectionFreight->push($freighFortyNor);
                }
              }
            }
          }
        }

        if(in_array($local->calculationtype_id, $array45)){
          if($request->input('fortyfive') != "0"){
            foreach($local->localcharcarriers as $carrierGlobal){
              if($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id ==  $carrier_all ){
                if($local->typedestiny_id == '1'){
                  if($local->calculationtype_id == "4"  ){
                    $subtotal_local = ($formulario->fortyfive *  $local->ammount) * 2 ;
                    $totalAmmount = (($formulario->fortyfive *  $local->ammount) * 2 ) / $rateMount ;
                    $cantidadT = $formulario->fortyfive + $formulario->fortyfive;
                    // MARKUP
                    if($localPercentage != 0){
                      $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup ;
                      $arraymarkupFortyFive = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $localMarkup;
                      $arraymarkupFortyFive = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                    }
                    $totalOrigin += $totalAmmount ;
                  }else{
                    $subtotal_local = $formulario->fortyfive *  $local->ammount;
                    $totalAmmount = ($formulario->fortyfive *  $local->ammount) / $rateMount ;

                    $cantidadT = $formulario->fortyfive;
                    // MARKUP
                    if($localPercentage != 0){
                      $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup ;
                      $arraymarkupFortyFive = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $localMarkup;
                      $arraymarkupFortyFive = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                    }
                    $totalOrigin += $totalAmmount ;
                  }
                  $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloOrig =  array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->fortyfive , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency , 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\'  Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id);
                  $arregloOrig = array_merge($arregloOrig,$arraymarkupFortyFive);
                  $origFortyFive["origin"] =$arregloOrig;
                  $collectionOrig->push($origFortyFive);
                }
                if($local->typedestiny_id == '2'){
                  if($local->calculationtype_id == "4"  ){
                    $subtotal_local = ($formulario->fortyfive *  $local->ammount) * 2 ;
                    $totalAmmount = (($formulario->fortyfive *  $local->ammount) * 2 ) / $rateMount ;
                    $cantidadT = $formulario->fortyfive + $formulario->fortyfive;
                    // MARKUP
                    if($localPercentage != 0){
                      $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup ;
                      $arraymarkupFortyFive = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $localMarkup;
                      $arraymarkupFortyFive = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                    }
                    $totalDestiny += $totalAmmount;
                  }else{
                    $subtotal_local = $formulario->fortyfive *  $local->ammount;
                    $totalAmmount = ($formulario->fortyfive *  $local->ammount) / $rateMount ;
                    $cantidadT = $formulario->fortyfive ;
                    // MARKUP
                    if($localPercentage != 0){
                      $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup ;
                      $arraymarkupFortyFive = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $localMarkup;
                      $arraymarkupFortyFive = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                    }
                    $totalDestiny += $totalAmmount;
                  }
                  $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloDest =  array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->fortyfive , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\'  Local ' , 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id  );
                  $arregloDest = array_merge($arregloDest,$arraymarkupFortyFive);
                  $destFortyFive["destiny"] =$arregloDest;
                  $collectionDest->push($destFortyFive);
                }
                if($local->typedestiny_id == '3'){
                  if($local->calculationtype_id == "4"  ){
                    $subtotal_local = ($formulario->fortyfive *  $local->ammount) * 2 ;
                    $totalAmmount = (($formulario->fortyfive *  $local->ammount) * 2 ) / $rateMount ;
                    $cantidadT = $formulario->fortyfive + $formulario->fortyfive;
                    // MARKUP
                    if($localPercentage != 0){
                      $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup ;
                      $arraymarkupFortyFive = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $localMarkup;
                      $arraymarkupFortyFive = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                    }
                    $totalFreight += $totalAmmount;
                    $FreightCharges += $totalAmmount;
                  }else{
                    $subtotal_local = $formulario->fortyfive *  $local->ammount;
                    $totalAmmount = ($formulario->fortyfive *  $local->ammount) / $rateMount ;
                    $cantidadT = $formulario->fortyfive;
                    // MARKUP
                    if($localPercentage != 0){
                      $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $markup ;
                      $arraymarkupFortyFive = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                    }else{
                      $markup =$localAmmount;
                      $markup = number_format($markup, 2, '.', '');
                      $totalAmmount += $localMarkup;
                      $arraymarkupFortyFive = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                    }
                    $totalFreight += $totalAmmount;
                    $FreightCharges += $totalAmmount;
                  }
                  $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->fortyfive , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'40\'  Local ' , 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id );
                  $arregloFreight = array_merge($arregloFreight,$arraymarkupFortyFive);
                  $freighFortyFive["freight"] = $arregloFreight;
                  $collectionFreight->push($freighFortyFive);
                }
              }
            }
          }
        }

        //#######################################################################

        if($local->calculationtype_id == "6" || $local->calculationtype_id == "9" || $local->calculationtype_id == "10" ){

          $cantidadT = 1;

          foreach($local->localcharcarriers as $carrierGlobal){
            if($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id ==  $carrier_all ){
              if($local->typedestiny_id == '1'){
                $subtotal_local =  $local->ammount;
                $totalAmmount =  $local->ammount  / $rateMount;
                //$cantidadT = 1;
                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupPC = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $localMarkup;
                  $arraymarkupPC = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                }
                $totalOrigin += $totalAmmount ;
                $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $arregloOrig =  array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => "-" , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>' Shipment Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id );
                $arregloOrig = array_merge($arregloOrig,$arraymarkupPC);
                $origPer["origin"] =$arregloOrig;
                $collectionOrig->push($origPer);
              }
              if($local->typedestiny_id == '2'){
                $subtotal_local =  $local->ammount;
                $totalAmmount =  $local->ammount  / $rateMount;
                //$cantidadT = 1;
                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupPC = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $localMarkup;
                  $arraymarkupPC = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                }
                $totalDestiny += $totalAmmount;
                $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $arregloDest = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => "-" , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>' Shipment Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT  , 'idCurrency' => $local->currency->id  );
                $arregloDest = array_merge($arregloDest,$arraymarkupPC);
                $destPer["destiny"] = $arregloDest;
                $collectionDest->push($destPer);
              }
              if($local->typedestiny_id == '3'){
                $subtotal_local =  $local->ammount;
                $totalAmmount =  $local->ammount  / $rateMount;
                //$cantidadT = 1;
                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupPC = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $localMarkup;
                  $arraymarkupPC = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                }
                //$totalAmmount =  $local->ammout  / $rateMount;
                $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $totalFreight += $totalAmmount;
                $FreightCharges += $totalAmmount;
                $arregloPC = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => "-" , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>' Shipment Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id  );
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
      $globalChar = GlobalCharge::where('validity', '<=',$date)->where('expire', '>=', $date)->whereHas('globalcharcarrier', function($q) use($carrier) {
        $q->whereIn('carrier_id', $carrier);
      })->where(function ($query) use($orig_port,$dest_port,$origin_country,$destiny_country){
        $query->whereHas('globalcharport', function($q) use($orig_port,$dest_port) {
          $q->whereIn('port_orig', $orig_port)->whereIn('port_dest', $dest_port);
        })->orwhereHas('globalcharcountry', function($q) use($origin_country,$destiny_country) {
          $q->whereIn('country_orig', $origin_country)->whereIn('country_dest', $destiny_country);
        });
      })->where('company_user_id','=',$company_user_id)->with('globalcharport.portOrig','globalcharport.portDest','globalcharcarrier.carrier','currency','surcharge.saleterm')->get();


      foreach($globalChar as $global){
        $rateMountG = $this->ratesCurrency($global->currency->id,$typeCurrency);
        // Condicion para enviar los terminos de venta o compra
        if(isset($global->surcharge->saleterm->name)){
          $terminos = $global->surcharge->saleterm->name;
        }else{
          $terminos = $global->surcharge->name;
        }
        if(in_array($global->calculationtype_id, $array20)){
          if($request->input('twuenty') != "0") {
            foreach($global->globalcharcarrier as $carrierGlobal){
              if($carrierGlobal->carrier_id == $data->carrier_id  || $carrierGlobal->carrier_id ==  $carrier_all){
                if($global->typedestiny_id == '1'){
                  $subtotal_global = $formulario->twuenty *  $global->ammount;
                  $totalAmmount = ($formulario->twuenty *  $global->ammount) / $rateMountG ;
                  $cantidadT = $formulario->twuenty;
                  // MARKUP
                  if($localPercentage != 0){
                    $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup ;
                    $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                  }else{
                    $markup =$localAmmount;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $localMarkup;
                    $arraymarkupT = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                  }
                  $totalOrigin += $totalAmmount ;
                  $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloOrig = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->twuenty , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'20\' Global '  , 'subtotal_global' => $subtotal_global , 'cantidadT' => $cantidadT , 'idCurrency' => $global->currency->id);
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
                    $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                  }else{
                    $markup =$localAmmount;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $localMarkup;
                    $arraymarkupT = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                  }
                  $totalDestiny += $totalAmmount;
                  $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloDest = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->twuenty , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'20\' Global ', 'subtotal_global' => $subtotal_global , 'cantidadT' => $cantidadT , 'idCurrency' => $global->currency->id);
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
                    $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                  }else{
                    $markup =$localAmmount;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $localMarkup;
                    $arraymarkupT = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                  }
                  $totalFreight += $totalAmmount;
                  $FreightCharges += $totalAmmount;
                  $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloFreight =  array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->twuenty , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'20\' Global ', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
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
              if($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id ==  $carrier_all ){
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
                    $arraymarkupF = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                  }else{
                    $markup =$localAmmount;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $localMarkup;
                    $arraymarkupF = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                  }
                  $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloOrig =  array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->forty , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\' Global ', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT , 'idCurrency' => $global->currency->id);
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
                    $arraymarkupF = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                  }else{
                    $markup =$localAmmount;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $localMarkup;
                    $arraymarkupF = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                  }
                  $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloDest =  array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->forty , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\' Global ', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
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
                    $arraymarkupF = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                  }else{
                    $markup =$localAmmount;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $localMarkup;
                    $arraymarkupF = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                  }
                  $totalFreight += $totalAmmount;
                  $FreightCharges += $totalAmmount;
                  $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->forty , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\' Global ' , 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
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
              if($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id ==  $carrier_all ){
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
                    $arraymarkupFH = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                  }else{
                    $markup =$localAmmount;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $localMarkup;
                    $arraymarkupFH = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                  }
                  $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloOrig =  array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->fortyhc , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'40\' HC Global ', 'subtotal_global' => $subtotal_global , 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
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
                    $arraymarkupFH = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                  }else{
                    $markup =$localAmmount;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $localMarkup;
                    $arraymarkupFH = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                  }
                  $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloDest = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->fortyhc , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\' HC Global ', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
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
                    $arraymarkupFH = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                  }else{
                    $markup =$localAmmount;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $localMarkup;
                    $arraymarkupFH = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                  }
                  $totalFreight += $totalAmmount;
                  $FreightCharges += $totalAmmount;
                  $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloFreight =  array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->fortyhc , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\' HC Global ', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
                  $arregloFreight = array_merge($arregloFreight,$arraymarkupFH);
                  $freighFortyHcGlo["freight"] =$arregloFreight;
                  $collectionGloFreight->push($freighFortyHcGlo);
                }
              }
            }
          }
        }
        //##################################################################
        //Contenedores de 40 NOR y 45
        if(in_array($global->calculationtype_id, $array40Nor)){
          if($request->input('fortynor') != "0") {
            foreach($global->globalcharcarrier as $carrierGlobal){
              if($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id ==  $carrier_all ){
                if($global->typedestiny_id == '1'){
                  if($global->calculationtype_id == "4"  ){
                    $subtotal_global = ($formulario->fortynor *  $global->ammount) * 2 ;
                    $totalAmmount = (($formulario->fortynor *  $global->ammount) * 2)  / $rateMountG ;
                    $totalOrigin += $totalAmmount ;
                    $cantidadT = $formulario->fortynor +  $formulario->fortynor  ;
                  }else{
                    $subtotal_global =  $formulario->fortynor *  $global->ammount;
                    $totalAmmount = ($formulario->fortynor *  $global->ammount) / $rateMountG;
                    $totalOrigin += $totalAmmount ;
                    $cantidadT = $formulario->fortynor;
                  }
                  // MARKUP
                  if($localPercentage != 0){
                    $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup ;
                    $arraymarkupGNor = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                  }else{
                    $markup =$localAmmount;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $localMarkup;
                    $arraymarkupGNor = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                  }
                  $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloOrig =  array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->fortynor , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'40\' NOR Global ', 'subtotal_global' => $subtotal_global , 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
                  $arregloOrig = array_merge($arregloOrig,$arraymarkupGNor);
                  $origFortyNorGlo["origin"] =$arregloOrig;
                  $collectionGloOrig->push($origFortyNorGlo);
                }
                if($global->typedestiny_id == '2'){
                  if($global->calculationtype_id == "4"  ){
                    $subtotal_global = ($formulario->fortynor *  $global->ammount) * 2 ;
                    $totalAmmount = (($formulario->fortynor *  $global->ammount) * 2)  / $rateMountG ;
                    $totalDestiny += $totalAmmount;
                    $cantidadT = $formulario->fortynor +  $formulario->fortynor  ;
                  }else{
                    $subtotal_global =  $formulario->fortynor *  $global->ammount;
                    $totalAmmount = ($formulario->fortynor *  $global->ammount) / $rateMountG;
                    $totalDestiny += $totalAmmount;
                    $cantidadT = $formulario->fortynor;
                  }
                  // MARKUP
                  if($localPercentage != 0){
                    $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup ;
                    $arraymarkupGNor = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                  }else{
                    $markup =$localAmmount;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $localMarkup;
                    $arraymarkupGNor = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                  }
                  $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloDest = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->fortynor , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\' NOR Global ', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
                  $arregloDest = array_merge($arregloDest,$arraymarkupGNor);
                  $destFortyNorGlo["destiny"] = $arregloDest;
                  $collectionGloDest->push($destFortyNorGlo);
                }
                if($global->typedestiny_id == '3'){
                  if($global->calculationtype_id == "4"  ){
                    $subtotal_global = ($formulario->fortynor *  $global->ammount) * 2 ;
                    $totalAmmount = (($formulario->fortynor *  $global->ammount) * 2)  / $rateMountG ;
                    $cantidadT = $formulario->fortynor +  $formulario->fortynor;
                  }else{
                    $subtotal_global =  $formulario->fortynor *  $global->ammount;
                    $totalAmmount = ($formulario->fortynor *  $global->ammount) / $rateMountG;
                    $cantidadT = $formulario->fortynor;
                  }
                  // MARKUP
                  if($localPercentage != 0){
                    $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup ;
                    $arraymarkupGNor = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                  }else{
                    $markup =$localAmmount;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $localMarkup;
                    $arraymarkupGNor = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                  }
                  $totalFreight += $totalAmmount;
                  $FreightCharges += $totalAmmount;
                  $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloFreight =  array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->fortynor , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\' NOR Global ', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
                  $arregloFreight = array_merge($arregloFreight,$arraymarkupGNor);
                  $freighFortyNorGlo["freight"] =$arregloFreight;
                  $collectionGloFreight->push($freighFortyNorGlo);
                }
              }
            }
          }
        }

        if(in_array($global->calculationtype_id, $array45)){
          if($request->input('fortyfive') != "0") {
            foreach($global->globalcharcarrier as $carrierGlobal){
              if($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id ==  $carrier_all ){
                if($global->typedestiny_id == '1'){
                  if($global->calculationtype_id == "4"  ){
                    $subtotal_global = ($formulario->fortyfive *  $global->ammount) * 2 ;
                    $totalAmmount = (($formulario->fortyfive *  $global->ammount) * 2)  / $rateMountG ;
                    $totalOrigin += $totalAmmount ;
                    $cantidadT = $formulario->fortyfive +  $formulario->fortyfive  ;
                  }else{
                    $subtotal_global =  $formulario->fortyfive *  $global->ammount;
                    $totalAmmount = ($formulario->fortyfive *  $global->ammount) / $rateMountG;
                    $totalOrigin += $totalAmmount ;
                    $cantidadT = $formulario->fortyfive;
                  }
                  // MARKUP
                  if($localPercentage != 0){
                    $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup ;
                    $arraymarkupFortyfiveG = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                  }else{
                    $markup =$localAmmount;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $localMarkup;
                    $arraymarkupFortyfiveG = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                  }
                  $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloOrig =  array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->fortyfive , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'45\'  ', 'subtotal_global' => $subtotal_global , 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
                  $arregloOrig = array_merge($arregloOrig,$arraymarkupFortyfiveG);
                  $origFortyfiveGlo["origin"] =$arregloOrig;
                  $collectionGloOrig->push($origFortyfiveGlo);
                }
                if($global->typedestiny_id == '2'){
                  if($global->calculationtype_id == "4"  ){
                    $subtotal_global = ($formulario->fortyfive *  $global->ammount) * 2 ;
                    $totalAmmount = (($formulario->fortyfive *  $global->ammount) * 2)  / $rateMountG ;
                    $totalDestiny += $totalAmmount;
                    $cantidadT = $formulario->fortyfive +  $formulario->fortyfive  ;
                  }else{
                    $subtotal_global =  $formulario->fortyfive *  $global->ammount;
                    $totalAmmount = ($formulario->fortyfive *  $global->ammount) / $rateMountG;
                    $totalDestiny += $totalAmmount;
                    $cantidadT = $formulario->fortyfive;
                  }
                  // MARKUP
                  if($localPercentage != 0){
                    $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup ;
                    $arraymarkupFortyfiveG = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                  }else{
                    $markup =$localAmmount;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $localMarkup;
                    $arraymarkupFortyfiveG = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                  }
                  $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloDest = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->fortyfive , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'45\'  ', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
                  $arregloDest = array_merge($arregloDest,$arraymarkupFortyfiveG);
                  $destFortyfiveGlo["destiny"] = $arregloDest;
                  $collectionGloDest->push($destFortyfiveGlo);
                }
                if($global->typedestiny_id == '3'){
                  if($global->calculationtype_id == "4"  ){
                    $subtotal_global = ($formulario->fortyfive *  $global->ammount) * 2 ;
                    $totalAmmount = (($formulario->fortyfive *  $global->ammount) * 2)  / $rateMountG ;
                    $cantidadT = $formulario->fortyfive +  $formulario->fortyfive;
                  }else{
                    $subtotal_global =  $formulario->fortyfive *  $global->ammount;
                    $totalAmmount = ($formulario->fortyfive *  $global->ammount) / $rateMountG;
                    $cantidadT = $formulario->fortyfive;
                  }
                  // MARKUP
                  if($localPercentage != 0){
                    $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $markup ;
                    $arraymarkupFortyfiveG = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                  }else{
                    $markup =$localAmmount;
                    $markup = number_format($markup, 2, '.', '');
                    $totalAmmount += $localMarkup;
                    $arraymarkupFortyfiveG = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                  }
                  $totalFreight += $totalAmmount;
                  $FreightCharges += $totalAmmount;
                  $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                  $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                  $arregloFreight =  array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->fortyfive , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'45\'  ', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
                  $arregloFreight = array_merge($arregloFreight,$arraymarkupFortyfiveG);
                  $freighFortyfiveGlo["freight"] =$arregloFreight;
                  $collectionGloFreight->push($freighFortyfiveGlo);
                }
              }
            }
          }
        }

        //##################################################################

        if($global->calculationtype_id == "6"  || $global->calculationtype_id == "9" || $global->calculationtype_id == "10" ){

          $cantidadT = 1;

          foreach($global->globalcharcarrier as $carrierGlobal){
            if($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id ==  $carrier_all ){
              if($global->typedestiny_id == '1'){
                $subtotal_global = $global->ammount;
                $totalAmmount =  $global->ammount / $rateMountG;

                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupPC = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $localMarkup;
                  $arraymarkupPC = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                }
                $totalOrigin += $totalAmmount ;
                $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $arregloOrig =  array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => "-" , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=> 'Shipment Global ', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
                $arregloOrig = array_merge($arregloOrig,$arraymarkupPC);
                $origPerGlo["origin"] =$arregloOrig;
                $collectionGloOrig->push($origPerGlo);
              }
              if($global->typedestiny_id == '2'){
                $subtotal_global = $global->ammount;
                $totalAmmount =  $global->ammount / $rateMountG;

                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupPC = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $localMarkup;
                  $arraymarkupPC = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                }
                $totalDestiny += $totalAmmount;
                $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $arregloDest = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => "-" , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=> 'Shipment Global ', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
                $arregloDest = array_merge($arregloDest,$arraymarkupPC);
                $destPerGlo["destiny"] = $arregloDest;
                $collectionGloDest->push($destPerGlo);
              }
              if($global->typedestiny_id == '3'){
                $subtotal_global = $global->ammount;
                $totalAmmount =  $global->ammount / $rateMountG;

                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupPC = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $localMarkup;
                  $arraymarkupPC = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                }
                $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $totalFreight += $totalAmmount;
                $FreightCharges += $totalAmmount;
                $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => "-" , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=> 'Shipment Global ', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
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

      $schedulesFin = new Collection();
      // $access_token = $this->schedules->authentication();
      //$dataSchedule = $this->schedules->getSchedules($access_token->access_token,$data->carrier->name,$data->port_origin->code,$data->port_destiny->code);
      $dataSchedule = array();
      $schedules = Collection::make($dataSchedule);

      $schedulesArr = new Collection();
      $schedulesFin = new Collection();
      if(!$schedules->isEmpty()){
        foreach($schedules['data'] as $schedules){

          $collectS = Collection::make($schedules);

          $days =  $this->dias_transcurridos($schedules->eta,$schedules->etd);

          $collectS->put('days',$days);
          if($schedules->route_type > 1){
            $collectS->put('type','Scale');
          }else{
            $collectS->put('type','Direct');
          }
          $schedulesArr->push($collectS);

        }
        $dateSchedule = strtotime($date);
        $dateSchedule =  date('Y-m-d',$dateSchedule);
        if(!$schedulesArr->isEmpty()){
          $schedulesArr =  $schedulesArr->where('etd','>=', $dateSchedule)->first();
          $schedulesFin->push($schedulesArr);
        }
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
        if($delivery_type == "3" || $delivery_type == "4" ){ 
          $inlandOrigin = $this->inlandDistance($delivery_type,$request->input('origin_address'), $data->port_origin->id , $typeCurrency,'Origin Port To Door');

          foreach($inlandOrigin as $inlandOrig){
            $totalQuote += $inlandOrig['monto'];
            $totalChargeOrig += $inlandOrig['monto'];
            $totalInland += $inlandOrig['monto'];
          }

        }else{
          $inlandOrigin = array();
        }

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
        if($delivery_type == "2" || $delivery_type == "4" ){ 
          $inlandDestiny = $this->inlandDistance($delivery_type,$request->input('destination_address'), $data->port_destiny->id , $typeCurrency,'Destiny Port To Door');
          foreach($inlandDestiny as $inlandDest){
            $totalQuote += $inlandDest['monto'];
            $totalChargeDest += $inlandDest['monto'];
            $totalInland +=  $inlandDest['monto'];
          }
        }else{
          $inlandDestiny = array();
        }

      }


      $totalChargeOrig += $totalOrigin;
      $totalChargeDest += $totalDestiny;
      $totalFreight = $totalFreight." ".$typeCurrency;
      $totalOrigin = $totalOrigin." ".$typeCurrency;
      $totalDestiny = $totalDestiny." ".$typeCurrency;
      $tot = $totalQuote;
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
      $data->setAttribute('tot',$tot);
      $data->setAttribute('quoteCurrency',$quoteCurrency);
      $data->setAttribute('idCurrency',$idCurrency);
      // SCHEDULES
      $data->setAttribute('schedulesFin',$schedulesFin);

    }
    // dd($arreglo);
    $form  = $request->all();
    $objharbor = new Harbor();
    $harbor = $objharbor->all()->pluck('name','id');
    $arreglo  =  $arreglo->sortBy('tot');
    
    /*    $arreglo->setCollection(
      collect(
        collect($arreglo->items())->sortBy('tot')
      )->values()
    );*/


    return view('quotation/index', compact('harbor','formulario','arreglo','inlandDestiny','inlandOrigin','form'));
  }


  // FIN COTIZACION AUTOMATICA
  function dias_transcurridos($fecha_i,$fecha_f)
  {
    $dias	= (strtotime($fecha_i)-strtotime($fecha_f))/86400;
    $dias 	= abs($dias); $dias = floor($dias);
    return intval($dias);
  }

  public function show(){

  }

}
