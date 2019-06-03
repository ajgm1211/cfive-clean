<?php

namespace App\Http\Controllers;

use App\AutomaticRate;
use App\AutomaticInland;
use App\CalculationType;
use App\CalculationTypeLcl;
use App\Charge;
use App\Company;
use App\CompanyUser;
use App\Contact;
use App\Country;
use App\Currency;
use App\EmailTemplate;
use App\Harbor;
use App\Incoterm;
use App\Price;
use App\Inland;
use App\Quote;
use App\Carrier;
use App\QuoteV2;
use App\Surcharge;
use App\User;
use App\PdfOption;
use EventIntercom;
use App\Jobs\SendQuotes;
use App\SendQuote;
use App\Contract;
use App\Rate;
use App\LocalCharge;
use App\LocalCharCarrier;
use App\LocalCharPort;
use App\GlobalCharge;
use App\GlobalCharPort;
use App\GlobalCharCarrier;
use App\PackageLoad;
use App\ChargeLclAir;
use GoogleMaps;
use Illuminate\Support\Facades\Input;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Yajra\DataTables\Contracts\DataTable;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Collection as Collection;
use App\Repositories\Schedules;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use App\PackageLoadV2;
use App\Airline;
use App\TermsPort;
use App\TermsAndCondition;
class QuoteV2Controller extends Controller
{
  public function index(Request $request){
    $company_user='';
    $currency_cfg = '';
    $company_user_id = \Auth::user()->company_user_id;
    if(\Auth::user()->hasRole('subuser')){
      $quotes = QuoteV2::where('user_id',\Auth::user()->id)->whereHas('user', function($q) use($company_user_id){
        $q->where('company_user_id','=',$company_user_id);
      })->orderBy('created_at', 'desc')->get();
    }else{
      $quotes = QuoteV2::whereHas('user', function($q) use($company_user_id){
        $q->where('company_user_id','=',$company_user_id);
      })->orderBy('created_at', 'desc')->get();
    }
    $companies = Company::pluck('business_name','id');
    $harbors = Harbor::pluck('display_name','id');
    $countries = Country::pluck('name','id');
    if(\Auth::user()->company_user_id){
      $company_user=CompanyUser::find(\Auth::user()->company_user_id);
      $currency_cfg = Currency::find($company_user->currency_id);
    }

    return view('quotesv2/index', ['companies' => $companies,'quotes'=>$quotes,'countries'=>$countries,'harbors'=>$harbors,'currency_cfg'=>$currency_cfg]);
  }

  public function LoadDatatableIndex(){

    $company_user_id = \Auth::user()->company_user_id;
    if(\Auth::user()->hasRole('subuser')){
      $quotes = QuoteV2::where('user_id',\Auth::user()->id)->whereHas('user', function($q) use($company_user_id){
        $q->where('company_user_id','=',$company_user_id);
      })->orderBy('created_at', 'desc')->get();
    }else{
      $quotes = QuoteV2::whereHas('user', function($q) use($company_user_id){
        $q->where('company_user_id','=',$company_user_id);
      })->orderBy('created_at', 'desc')->get();
    }

    $colletions = collect([]);
    foreach($quotes as $quote){
      $custom_id      = '---';
      $company  = '---';
      $origin         = '';
      $destination    = '';
      if(isset($quote->company)){
        $company  = $quote->company->business_name;
      }

      /*if(!$quote->origin_address){
        $origin = $quote->origin_port->display_name;
      } else {
        $origin = $quote->origin_address;
      }

      if(!$quote->destination_address){
        $destination = $quote->destination_port->display_name;
      } else {
        $destination = $quote->destination_address;
      }*/
      if($quote->quote_id!=''){
        $id  = $quote->quote_id;
      }else{
        $id = $quote->quote_id;
      }
      $rates = AutomaticRate::where('quote_id',$quote->id)->get();
      $origin = '';
      foreach($rates as $rate){
        if($rate->origin_port_id!=''){
          $origin.='<li>'.$rate->origin_port->name.'</li>';
        }else if($rate->destination_airport_id!=''){
          $origin.='<li>'.$rate->origin_airport->name.'</li>';
        }else if($rate->origin_address!=''){
          $origin.='<li>'.$rate->origin_address.'</li>';
        }
      }
      $destination = '';
      foreach($rates as $rate){
        if($rate->destination_port_id!=''){
          $destination.='<li>'.$rate->destination_port->name.'</li>';
        }else if($rate->destination_airport_id!=''){
          $destination.='<li>'.$rate->destination_airport->name.'</li>';
        }else if($rate->destination_address!=''){
          $destination.='<li>'.$rate->destination_address.'</li>';
        }
      }

      if($quote->type=='AIR'){
        $img='<img src="/images/plane-blue.svg" class="img img-responsive" width="25">';
      }else{
        $img='<img src="/images/logo-ship-blue.svg" class="img img-responsive" width="25">';
      }

      $data = [
        'id'            => $id,
        'idSet'         => setearRouteKey($quote->id),
        'client'        => $company,
        'created'       => date_format($quote->created_at, 'M d, Y H:i'),
        'user'          => $quote->user->name.' '.$quote->user->lastname,
        'origin'        => '<ul>'.$origin.'</ul>',
        'destination'   => '<ul>'.$destination.'</ul>',
        'type'          => $quote->type,
        'img'          => $img,
      ];
      $colletions->push($data);
    }
    return DataTables::of($colletions)

      ->addColumn('type', function ($colletion) {
        return '<img src="/images/logo-ship-blue.svg" class="img img-responsive" width="25">';
      })->addColumn('action',function($colletion){
      return
        '<button class="btn btn-outline-light  dropdown-toggle quote-options" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      Options
      </button>
      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" >
      <a class="dropdown-item" href="/v2/quotes/show/'.$colletion['idSet'].'">
      <span>
      <i class="la la-eye"></i>
      &nbsp;
      Show
      </span>
      </a>
      <a href="/quotes/duplicate/'.$colletion['idSet'].'" class="dropdown-item" >
      <span>
      <i class="la la-plus"></i>
      &nbsp;
      Duplicate
      </span>
      </a>
      <a href="#" class="dropdown-item" id="delete-quote" data-quote-id="'.$colletion['id'].'" >
      <span>
      <i class="la la-eraser"></i>
      &nbsp;
      Delete
      </span>
      </a>
      </div>';
    })
      ->editColumn('id', '{{$id}}')->make(true);
  }

  public function show($id)
  {
    //Setting id
    $id = obtenerRouteKey($id);
    $origin_charges = new Collection();
    $freight_charges = new Collection();
    $destination_charges = new Collection();
    $equipmentHides = '';

    //Retrieving all data
    $company_user_id = \Auth::user()->company_user_id;
    $quote = QuoteV2::findOrFail($id);
    $package_loads = PackageLoadV2::where('quote_id',$quote->id)->get();
    $inlands = AutomaticInland::where('quote_id',$quote->id)->get();
    $rates = AutomaticRate::where('quote_id',$quote->id)->with('charge')->get();
    $harbors = Harbor::get()->pluck('display_name','id_complete');
    $countries = Country::pluck('name','id');

    $prices = Price::pluck('name','id');
    $carrierMan = Carrier::pluck('name','id');
    $airlines = Airline::pluck('name','id');
    $companies = Company::where('company_user_id',$company_user_id)->pluck('business_name','id');
    $contacts = Contact::where('company_id',$quote->company_id)->pluck('first_name','id');
    $incoterms = Incoterm::pluck('name','id');
    $users = User::where('company_user_id',$company_user_id)->pluck('name','id');
    $prices = Price::where('company_user_id',$company_user_id)->pluck('name','id');
    $currencies = Currency::pluck('alphacode','id');
    $company_user=CompanyUser::find(\Auth::user()->company_user_id);
    if(count($company_user->companyUser)>0) {
      $currency_name = Currency::where('id', $company_user->companyUser->currency_id)->first();
    }else{
      $currency_name = '';
    }
    $currency_cfg = Currency::find($company_user->currency_id);
    if($quote->equipment!=''){
      $equipmentHides = $this->hideContainer($quote->equipment,'BD');
    }
    $calculation_types = CalculationType::pluck('name','id');
    $calculation_types_lcl_air = CalculationTypeLcl::pluck('name','id');
    $surcharges = Surcharge::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');
    $email_templates = EmailTemplate::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');
    $hideO = 'hide';
    $hideD = 'hide';

    foreach ($rates as $item) {
      $sum20=0;
      $sum40=0;
      $sum40hc=0;
      $sum40nor=0;
      $sum45=0;

      $total_markup20=0;
      $total_markup40=0;
      $total_markup40hc=0;
      $total_markup40nor=0;
      $total_markup45=0;

      $total_rate20=0;
      $total_rate40=0;
      $total_rate40hc=0;
      $total_rate40nor=0;
      $total_rate45=0;

      $total_rate_markup20=0;
      $total_rate_markup40=0;
      $total_rate_markup40hc=0;
      $total_rate_markup40nor=0;
      $total_rate_markup45=0;

      $total_lcl_air_freight=0;
      $total_lcl_air_origin=0;
      $total_lcl_air_destination=0;

      $currency = Currency::find($item->currency_id);
      $item->currency_usd = $currency->rates;
      $item->currency_eur = $currency->rates_eur;

      $typeCurrency =  $currency_cfg->alphacode;

      $currency_rate=$this->ratesCurrency($item->currency_id,$typeCurrency);

      //Charges
      foreach ($item->charge as $value) {

        $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);

        $array_amounts = json_decode($value->amount,true);
        $array_markups = json_decode($value->markups,true);

        if(isset($array_amounts['c20'])){
          $amount20=$array_amounts['c20'];
          $total20=$amount20/$currency_rate;
          $sum20 = number_format($total20, 2, '.', '');
        }

        if(isset($array_markups['m20'])){
          $markup20=$array_markups['m20'];
          $total_markup20=$markup20/$currency_rate;
        }

        if(isset($array_amounts['c40'])){
          $amount40=$array_amounts['c40'];
          $total40=$amount40/$currency_rate;          
          $sum40 = number_format($total40, 2, '.', '');
        }

        if(isset($array_markups['m40'])){
          $markup40=$array_markups['m40'];
          $total_markup40=$markup40/$currency_rate;
        }

        if(isset($array_amounts['c40hc'])){
          $amount40hc=$array_amounts['c40hc'];
          $total40hc=$amount40hc/$currency_rate;          
          $sum40hc = number_format($total40hc, 2, '.', '');
        }

        if(isset($array_markups['m40hc'])){
          $markup40hc=$array_markups['m40hc'];
          $total_markup40hc=$markup40hc/$currency_rate;
        }

        if(isset($array_amounts['c40nor'])){
          $amount40nor=$array_amounts['c40nor'];
          $total40nor=$amount40nor/$currency_rate;
          $sum40nor = number_format($total40nor, 2, '.', '');
        }

        if(isset($array_markups['m40nor'])){
          $markup40nor=$array_markups['m40nor'];
          $total_markup40nor=$markup40nor/$currency_rate;
        }

        if(isset($array_amounts['c45'])){
          $amount45=$array_amounts['c45'];
          $total45=($amount45+$markup45)/$currency_rate;
          $sum45 = number_format($total45, 2, '.', '');
        }

        if(isset($array_markups['m45'])){
          $markup45=$array_markups['m45'];
          $total_markup45=$markup45/$currency_rate;
        }

        $value->total_20=number_format($sum20, 2, '.', '');
        $value->total_40=number_format($sum40, 2, '.', '');
        $value->total_40hc=number_format($sum40hc, 2, '.', '');
        $value->total_40nor=number_format($sum40nor, 2, '.', '');
        $value->total_45=number_format($sum45, 2, '.', '');

        $value->total_markup20=number_format($total_markup20, 2, '.', '');
        $value->total_markup40=number_format($total_markup40, 2, '.', '');
        $value->total_markup40hc=number_format($total_markup40hc, 2, '.', '');
        $value->total_markup40nor=number_format($total_markup40nor, 2, '.', '');
        $value->total_markup45=number_format($total_markup45, 2, '.', '');       

        $currency_charge = Currency::find($value->currency_id);
        $value->currency_usd = $currency_charge->rates;
        $value->currency_eur = $currency_charge->rates_eur;
      }

      //Charges
      foreach ($item->charge_lcl_air as $value) {

        $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);

        if($value->type_id==3){
          $value->total_freight=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');
        }elseif($value->type_id==1){
          $value->total_origin=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');
        }else{
          $value->total_destination=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');
        }
      }

      //Inland
      foreach ($item->inland as $inland) {
        $typeCurrency =  $currency_cfg->alphacode;
        $currency_rate=$this->ratesCurrency($inland->currency_id,$typeCurrency);
        $array_amounts = json_decode($inland->rate,true);
        $array_markups = json_decode($inland->markup,true);
        if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
          $amount20=$array_amounts['c20'];
          $markup20=$array_markups['m20'];
          $total20=($amount20+$markup20)/$currency_rate;
          $sum20 = number_format($total20, 2, '.', '');
        }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
          $amount20=$array_amounts['c20'];
          $total20=$amount20/$currency_rate;
          $sum20 = number_format($total20, 2, '.', '');
        }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
          $markup20=$array_markups['m20'];
          $total20=$markup20/$currency_rate;
          $sum20 = number_format($total20, 2, '.', '');
        }

        if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
          $amount40=$array_amounts['c40'];
          $markup40=$array_markups['m40'];
          $total40=($amount40+$markup40)/$currency_rate;
          $sum40 = number_format($total40, 2, '.', '');
        }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
          $amount40=$array_amounts['c40'];
          $total40=$amount40/$currency_rate;
          $sum40 = number_format($total40, 2, '.', '');
        }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
          $markup40=$array_markups['m40'];
          $total40=$markup40/$currency_rate;
          $sum40 = number_format($total40, 2, '.', '');
        }

        if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
          $amount40hc=$array_amounts['c40hc'];
          $markup40hc=$array_markups['m40hc'];
          $total40hc=($amount40hc+$markup40hc)/$currency_rate;
          $sum40hc = number_format($total40hc, 2, '.', '');
        }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
          $amount40hc=$array_amounts['c40hc'];
          $total40hc=$amount40hc/$currency_rate;
          $sum40hc = number_format($total40hc, 2, '.', '');
        }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
          $markup40hc=$array_markups['m40hc'];
          $total40hc=$markup40hc/$currency_rate;
          $sum40hc = number_format($total40hc, 2, '.', '');
        }

        if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
          $amount40nor=$array_amounts['c40nor'];
          $markup40nor=$array_markups['m40nor'];
          $total40nor=($amount40nor+$markup40nor)/$currency_rate;
          $sum40nor = number_format($total40nor, 2, '.', '');
        }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
          $amount40nor=$array_amounts['c40nor'];
          $total40nor=$amount40nor/$currency_rate;
          $sum40nor = number_format($total40nor, 2, '.', '');
        }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
          $markup40nor=$array_markups['m40nor'];
          $total40nor=$markup40nor/$currency_rate;
          $sum40nor = number_format($total40nor, 2, '.', '');
        }

        if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
          $amount45=$array_amounts['c45'];
          $markup45=$array_markups['m45'];
          $total45=($amount45+$markup45)/$currency_rate;
          $sum45 = number_format($total45, 2, '.', '');
        }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
          $amount45=$array_amounts['c45'];
          $total45=$amount45/$currency_rate;
          $sum45 = number_format($total45, 2, '.', '');
        }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
          $markup45=$array_markups['m45'];
          $total45=$markup45/$currency_rate;
          $sum45 = number_format($total45, 2, '.', '');
        }

        $inland->total_20=number_format($sum20, 2, '.', '');
        $inland->total_40=number_format($sum40, 2, '.', '');
        $inland->total_40hc=number_format($sum40hc, 2, '.', '');
        $inland->total_40nor=number_format($sum40nor, 2, '.', '');
        $inland->total_45=number_format($sum45, 2, '.', '');

        $currency_charge = Currency::find($inland->currency_id);
        $inland->currency_usd = $currency_charge->rates;
        $inland->currency_eur = $currency_charge->rates_eur;
      }

      $item->total_rate20=number_format($total_rate20+$total_rate_markup20, 2, '.', '');
      $item->total_rate40=number_format($total_rate40+$total_rate_markup40, 2, '.', '');
      $item->total_rate40hc=number_format($total_rate40hc+$total_rate_markup40hc, 2, '.', '');
      $item->total_rate40nor=number_format($total_rate40nor+$total_rate_markup40nor, 2, '.', '');
      $item->total_rate45=number_format($total_rate45+$total_rate_markup45, 2, '.', '');
      //dd($total_rate20);
      $item->total_rate_a20=number_format($total_rate20, 2, '.', '');
      $item->total_rate_a40=number_format($total_rate40, 2, '.', '');
      $item->total_rate_a40hc=number_format($total_rate40hc, 2, '.', '');
      $item->total_rate_a40nor=number_format($total_rate40nor, 2, '.', '');
      $item->total_rate_a45=number_format($total_rate45, 2, '.', '');

      $item->total_rate_m20=number_format($total_rate_markup20, 2, '.', '');
      $item->total_rate_m40=number_format($total_rate_markup40, 2, '.', '');
      $item->total_rate_m40hc=number_format($total_rate_markup40hc, 2, '.', '');
      $item->total_rate_m40nor=number_format($total_rate_markup40nor, 2, '.', '');
      $item->total_rate_m45=number_format($total_rate_markup45, 2, '.', '');  
    }

    //Adding country codes to rates collection


    foreach ($rates as $item) {
      $rates->map(function ($item) {
        if($item->origin_port_id!='' ){
          $item['origin_country_code'] = strtolower(substr($item->origin_port->code, 0, 2));
        }else{
          $item['origin_country_code'] = strtolower($item->origin_airport->code);
        }
        if($item->destination_port_id!='' ){
          $item['destination_country_code'] = strtolower(substr($item->destination_port->code, 0, 2));
        }else{
          $item['destination_country_code'] = strtolower($item->destination_airport->code); 
        }

        return $item;
      }); 
    }
    $emaildimanicdata = json_encode([
      'quote_bool'   => 'true',
      'company_id'   => '',
      'contact_id'   => '',
      'quote_id'     => $quote->id
    ]);

    return view('quotesv2/show', compact('quote','companies','incoterms','users','prices','contacts','currencies','currency_cfg','equipmentHides','freight_charges','origin_charges','destination_charges','calculation_types','calculation_types_lcl_air','rates','surcharges','email_templates','inlands','emaildimanicdata','package_loads','countries','harbors','prices','airlines','carrierMan','currency_name','hideO','hideD'));
  }

  //Actualiza cargos de la tabla rate
  public function updateRateCharges(Request $request)
  {
    $charge=AutomaticRate::find($request->pk);
    $name = explode("->", $request->name);
    if (strpos($request->name, '->') == true) {
      if ($name[0] == 'rates') {
        $array = json_decode($charge->rates, true);
      }else{
        $array = json_decode($charge->markups, true);
      }
      $field = (string) $name[0];
      $array[$name[1]]=$request->value;
      $array = json_encode($array);
      $charge->$field=$array;
    }else{
      $name = $request->name;
      $charge->$name=$request->value;
    }
    $charge->update();
    return response()->json(['success'=>'Ok']);
  }

  //Actualiza cargos por rate
  public function updateQuoteCharges(Request $request)
  {
    $charge=Charge::find($request->pk);
    $name = explode("->", $request->name);
    if (strpos($request->name, '->') == true) {
      if ($name[0] == 'amount') {
        $array = json_decode($charge->amount, true);
      }else{
        $array = json_decode($charge->markups, true);
      }
      $field = (string) $name[0];
      $array[$name[1]]=$request->value;
      $array = json_encode($array);
      $charge->$field=$array;
    }else{
      $name = $request->name;
      $charge->$name=$request->value;
    }
    $charge->update();
    return response()->json(['success'=>'Ok']);
  }

  //Actualiza cargos por inlands
  public function updateInlandCharges(Request $request)
  {
    $charge=AutomaticInland::find($request->pk);
    $name = explode("->", $request->name);
    if (strpos($request->name, '->') == true) {
      if ($name[0] == 'rate') {
        $array = json_decode($charge->rate, true);
      }else{
        $array = json_decode($charge->markup, true);
      }
      $field = (string) $name[0];
      $array[$name[1]]=$request->value;
      $array = json_encode($array);
      $charge->$field=$array;
    }else{
      $name = $request->name;
      $charge->$name=$request->value;
    }
    $charge->update();
    return response()->json(['success'=>'Ok']);
  }

  //Actualiza Cargos por rate en LCL y Aereo
  public function updateQuoteChargesLcl(Request $request)
  {
    $charge=ChargeLclAir::find($request->pk);
    $name = $request->name;
    $charge->$name=$request->value;
    $charge->update();
    return response()->json(['success'=>'Ok']);
  }

  //Actualiza opciones del PDF
  public function updatePdfFeature(Request $request){
    $name=$request->name;
    $quote = PdfOption::where('quote_id',$request->id)->first();
    $quote->$name=$request->value;
    $quote->update();
    return response()->json(['message'=>'Ok']);
  }

  //Actualiza la cotización
  public function update(Request $request,$id)
  {

    $validation = explode('/',$request->validity);
    $validity_start = $validation[0];
    $validity_end = $validation[1];

    $quote=QuoteV2::find($id);
    if($quote->quote_id!=$request->quote_id){
      $quote->custom_quote_id=$request->quote_id;
    }else{
      $quote->custom_quote_id='';
    }
    $quote->type=$request->type;
    $quote->company_id=$request->company_id;
    $quote->contact_id=$request->contact_id;
    $quote->delivery_type=$request->delivery_type;
    $quote->date_issued=$request->date_issued;
    $quote->incoterm_id=$request->incoterm_id;
    if($request->equipment!=''){
      $quote->equipment=json_encode($request->equipment);
    }
    $quote->validity_start=$validity_start;
    $quote->validity_end=$validity_end;
    $quote->price_id=$request->price_id;
    $quote->user_id=$request->user_id;
    $quote->kind_of_cargo=$request->kind_of_cargo;
    $quote->commodity=$request->commodity;
    $quote->status=$request->status;
    $quote->update();

    $contact_name=$quote->contact->first_name.' '.$quote->contact->last_name;
    $owner=$quote->user->name.' '.$quote->user->lastname;
    $price_name=$quote->price->name;

    return response()->json(['message'=>'Ok','quote'=>$quote,'contact_name'=>$contact_name,'owner'=>$owner,'price_name'=>$price_name]);
  }

  //Actualiza condiciones de pago
  public function updatePaymentConditions(Request $request,$id)
  {
    $quote=QuoteV2::find($id);

    $quote->payment_conditions=$request->payments;
    $quote->update();

    return response()->json(['message'=>'Ok','quote'=>$quote]);
  }

  //Actualiza terminos y condiciones
  public function updateTerms(Request $request,$id)
  {
    $quote=QuoteV2::find($id);

    $quote->terms_and_conditions=$request->terms;
    $quote->update();

    return response()->json(['message'=>'Ok','quote'=>$quote]);
  }

  public function updateRemarks(Request $request,$id)
  {
    $rate=AutomaticRate::find($id);

    $rate->remarks=$request->remarks;
    $rate->update();

    return response()->json(['message'=>'Ok','rate'=>$rate]);
  }

  public function duplicate(Request $request, $id){

    $id = obtenerRouteKey($id);
    $quote=QuoteV2::find($id);
    $quote_duplicate = new QuoteV2();
    $quote_duplicate->user_id=\Auth::id();
    $quote_duplicate->company_user_id=\Auth::user()->company_user_id;
    $quote_duplicate->quote_id=$this->idPersonalizado();
    $quote_duplicate->incoterm_id=$quote->incoterm_id;
    $quote_duplicate->type=$quote->type;
    $quote_duplicate->cargo_type=$quote->cargo_type;
    $quote_duplicate->total_quantity=$quote->total_quantity;
    $quote_duplicate->total_weight=$quote->total_weight;
    $quote_duplicate->total_volume=$quote->total_volume;
    $quote_duplicate->chargeable_weight=$quote->chargeable_weight;
    $quote_duplicate->delivery_type=$quote->delivery_type;
    $quote_duplicate->currency_id=$quote->currency_id;
    $quote_duplicate->contact_id=$quote->contact_id;
    $quote_duplicate->company_id=$quote->company_id;
    $quote_duplicate->validity_start=$quote->validity_start;
    $quote_duplicate->validity_end=$quote->validity_end;
    $quote_duplicate->equipment=$quote->equipment;
    $quote_duplicate->status=$quote->status;
    $quote_duplicate->date_issued=$quote->date_issued;
    $quote_duplicate->terms_and_conditions=$quote->terms_and_conditions;
    $quote_duplicate->payment_conditions=$quote->payment_conditions;
    if($quote->origin_address){
      $quote_duplicate->origin_address=$quote->origin_address;
    }
    if($quote->destination_address){
      $quote_duplicate->destination_address=$quote->destination_address;
    }
    if($quote->origin_port_id){
      $quote_duplicate->origin_port_id=$quote->origin_port_id;
    }
    if($quote->destination_port_id){
      $quote_duplicate->destination_port_id=$quote->destination_port_id;
    }
    if($quote->price_id){
      $quote_duplicate->price_id=$quote->price_id;
    }
    if($quote->custom_quote_id){
      $quote_duplicate->custom_quote_id=$quote->custom_quote_id;
    }
    if($quote->kind_of_cargo){
      $quote_duplicate->kind_of_cargo=$quote->kind_of_cargo;
    }
    if($quote->commodity){
      $quote_duplicate->commodity=$quote->commodity;
    }    
    $quote_duplicate->save();

    $pdf = PdfOption::where('quote_id',$quote->id)->first();
    $pdf_duplicate = new PdfOption();
    $pdf_duplicate->quote_id=$quote_duplicate->id;
    $pdf_duplicate->show_type=$pdf->show_type;
    $pdf_duplicate->grouped_total_currency=$pdf->grouped_total_currency;
    $pdf_duplicate->total_in_currency=$pdf->total_in_currency;
    $pdf_duplicate->grouped_freight_charges=$pdf->grouped_freight_charges;
    $pdf_duplicate->freight_charges_currency=$pdf->freight_charges_currency;
    $pdf_duplicate->grouped_origin_charges=$pdf->grouped_origin_charges;
    $pdf_duplicate->origin_charges_currency=$pdf->origin_charges_currency;
    $pdf_duplicate->grouped_destination_charges=$pdf->grouped_destination_charges;
    $pdf_duplicate->destination_charges_currency=$pdf->destination_charges_currency;
    $pdf_duplicate->language=$pdf->language;
    $pdf_duplicate->show_carrier=$pdf->show_carrier;
    $pdf_duplicate->show_logo=$pdf->show_logo;
    $pdf_duplicate->save();

    $rates = AutomaticRate::where('quote_id',$quote->id)->get();

    foreach ($rates as $rate){

      $rate_duplicate = new AutomaticRate();
      $rate_duplicate->quote_id=$quote_duplicate->id;
      $rate_duplicate->contract=$rate->contract;
      $rate_duplicate->validity_start=$rate->validity_start;
      $rate_duplicate->validity_end=$rate->validity_end;
      $rate_duplicate->origin_port_id=$rate->origin_port_id;
      $rate_duplicate->destination_port_id=$rate->destination_port_id;
      $rate_duplicate->carrier_id=$rate->carrier_id;
      $rate_duplicate->rates=$rate->rates;
      $rate_duplicate->markups=$rate->markups;
      $rate_duplicate->total=$rate->total;
      $rate_duplicate->currency_id=$rate->currency_id;
      $rate_duplicate->save();

      $charges=Charge::where('automatic_rate_id',$rate->id)->get();


      foreach ($charges as $charge){
        $charge_duplicate = new Charge();
        $charge_duplicate->automatic_rate_id=$rate_duplicate->id;
        $charge_duplicate->type_id=$charge->type_id;
        $charge_duplicate->surcharge_id=$charge->surcharge_id;
        $charge_duplicate->calculation_type_id=$charge->calculation_type_id;
        $charge_duplicate->amount=$charge->amount;
        $charge_duplicate->markups=$charge->markups;
        $charge_duplicate->total=$charge->total;
        $charge_duplicate->currency_id=$charge->currency_id;
        $charge_duplicate->save();
      }
    }

    if($request->ajax()){
      return response()->json(['message' => 'Ok']);
    }else{
      $request->session()->flash('message.nivel', 'success');
      $request->session()->flash('message.title', 'Well done!');
      $request->session()->flash('message.content', 'Quote duplicated successfully!');
      return redirect()->action('QuoteV2Controller@show', setearRouteKey($quote_duplicate->id));
    }
  }

  public function idPersonalizado(){
    $user_company = CompanyUser::where('id',\Auth::user()->company_user_id)->first();
    $iniciales =  strtoupper(substr($user_company->name,0, 2));
    $quote = QuoteV2::where('company_user_id',$user_company->id)->orderBy('created_at', 'desc')->first();

    if($quote == null){
      $iniciales = $iniciales."-1";
    }else{

      $numeroFinal = explode('-',$quote->quote_id);

      //dd($quote->quote_id);
      $numeroFinal = $numeroFinal[1] +1;

      $iniciales = $iniciales."-".$numeroFinal;
    }
    return $iniciales;
  }

  public function hideContainer($equipmentForm,$tipo){
    $equipment = new Collection();
    $hidden20 = 'hidden';
    $hidden40 = 'hidden';
    $hidden40hc = 'hidden';
    $hidden40nor = 'hidden';
    $hidden45 = 'hidden';
    // Clases para reordenamiento de la tabla y ajuste
    $originClass = 'col-md-2';
    $destinyClass = 'col-md-1';
    $dataOrigDest = 'col-md-3';

    if($tipo == 'BD'){
      $equipmentForm = json_decode($equipmentForm);
    }

    $countEquipment = count($equipmentForm);
    $countEquipment = 5 - $countEquipment;
    if($countEquipment == 1 ){
      $originClass = 'col-md-3';
      $destinyClass = 'col-md-1';
      $dataOrigDest = 'col-md-4';
    }
    if($countEquipment == 2 ){
      $originClass = 'col-md-3';
      $destinyClass = 'col-md-2';
      $dataOrigDest = 'col-md-5';
    }
    if($countEquipment == 3){
      $originClass = 'col-md-4';
      $destinyClass = 'col-md-2';
      $dataOrigDest = 'col-md-6';
    }
    if($countEquipment == 4){
      $originClass = 'col-md-5';
      $destinyClass = 'col-md-2';
      $dataOrigDest = 'col-md-7';
    }

    foreach($equipmentForm as $val){
      if($val == '20'){
        $hidden20 = '';
      }
      if($val == '40'){
        $hidden40 = '';
      }
      if($val == '40HC'){
        $hidden40hc = '';
      }
      if($val == '40NOR'){
        $hidden40nor = '';
      }
      if($val == '45'){
        $hidden45 = '';
      }
    }
    $equipment->put('originClass',$originClass);
    $equipment->put('destinyClass',$destinyClass);
    $equipment->put('dataOrigDest',$dataOrigDest);
    $equipment->put('20',$hidden20);
    $equipment->put('40',$hidden40);
    $equipment->put('40hc',$hidden40hc);
    $equipment->put('40nor',$hidden40nor);
    $equipment->put('45',$hidden45);
    return($equipment);
  }

  public function send_pdf_quote(Request $request)
  {
    $quote = QuoteV2::findOrFail($request->id);
    $rates = AutomaticRate::where('quote_id',$quote->id)->with('charge')->get();
    $origin_charges = AutomaticRate::whereHas('charge', function ($query) {
      $query->where('type_id', 1);
    })->where('quote_id',$quote->id)->get();
    $freight_charges = AutomaticRate::whereHas('charge', function ($query) {
      $query->where('type_id', 3);
    })->where('quote_id',$quote->id)->get();
    $destination_charges = AutomaticRate::whereHas('charge', function ($query) {
      $query->where('type_id', 2);
    })->where('quote_id',$quote->id)->get();
    $contact_email = Contact::find($quote->contact_id);
    $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
    $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
    $user = User::where('id',\Auth::id())->with('companyUser')->first();
    if($quote->equipment!=''){
      $equipmentHides = $this->hideContainer($quote->equipment,'BD');
    }

    if(\Auth::user()->company_user_id){
      $company_user=CompanyUser::find(\Auth::user()->company_user_id);
      $type=$company_user->type_pdf;
      $ammounts_type=$company_user->pdf_ammounts;
      $currency_cfg = Currency::find($company_user->currency_id);
    }

    foreach ($rates as $item) {
      $total_rate20=0;
      $total_rate40=0;
      $total_rate40hc=0;
      $total_rate40nor=0;
      $total_rate45=0;

      $total_rate_markup20=0;
      $total_rate_markup40=0;
      $total_rate_markup40hc=0;
      $total_rate_markup40nor=0;
      $total_rate_markup45=0;

      if($quote->pdf_option->grouped_total_currency==1){
        $typeCurrency = $quote->pdf_option->total_in_currency;
      }else{
        $typeCurrency =  $currency_cfg->alphacode;
      }

      $currency_rate=$this->ratesCurrency($item->currency_id,$typeCurrency);

      $currency = Currency::find($item->currency_id);
      $item->currency_usd = $currency->rates;
      $item->currency_eur = $currency->rates_eur;

      foreach ($item->charge as $value) {
        $currency_charge = Currency::find($value->currency_id);
        $value->currency_usd = $currency_charge->rates;
        $value->currency_eur = $currency_charge->rates_eur;
      }
      foreach ($item->inland as $inland) {
        $currency_charge = Currency::find($inland->currency_id);
        $inland->currency_usd = $currency_charge->rates;
        $inland->currency_eur = $currency_charge->rates_eur;
      }

      $item->total_rate20=number_format($total_rate20+$total_rate_markup20, 2, '.', '');
      $item->total_rate40=number_format($total_rate40+$total_rate_markup40, 2, '.', '');
      $item->total_rate40hc=number_format($total_rate40hc+$total_rate_markup40hc, 2, '.', '');
      $item->total_rate40nor=number_format($total_rate40nor+$total_rate_markup40nor, 2, '.', '');
      $item->total_rate45=number_format($total_rate45+$total_rate_markup45, 2, '.', ''); 
    }

    $origin_charges_grouped = collect($origin_charges);

    $origin_charges_grouped = $origin_charges_grouped->groupBy([

      function ($item) {
        return $item['origin_port']['name'].', '.$item['origin_port']['code'];
      },
      function ($item) {
        return $item['carrier']['name'];
      },

    ], $preserveKeys = true);
    foreach($origin_charges_grouped as $origin=>$detail){
      foreach($detail as $item){
        foreach($item as $rate){

          $sum20= 0;
          $sum40= 0;
          $sum40hc= 0;
          $sum40nor= 0;
          $sum45= 0;
          $inland20= 0;
          $inland40= 0;
          $inland40hc= 0;
          $inland40nor= 0;
          $inland45= 0;

          foreach($rate->charge as $value){

            if($value->type_id==1){
              if($quote->pdf_option->grouped_origin_charges==1){
                $typeCurrency =  $quote->pdf_option->origin_charges_currency;
              }else{
                $typeCurrency =  $currency_cfg->alphacode;
              }
              $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
              $array_amounts = json_decode($value->amount,true);
              $array_markups = json_decode($value->markups,true);
              if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                $amount20=$array_amounts['c20'];
                $markup20=$array_markups['m20'];
                $total20=($amount20+$markup20)/$currency_rate;
                $sum20 += number_format($total20, 2, '.', '');
              }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                $amount20=$array_amounts['c20'];
                $total20=$amount20/$currency_rate;
                $sum20 += number_format($total20, 2, '.', '');
              }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                $markup20=$array_markups['m20'];
                $total20=$markup20/$currency_rate;
                $sum20 += number_format($total20, 2, '.', '');
              }

              if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                $amount40=$array_amounts['c40'];
                $markup40=$array_markups['m40'];
                $total40=($amount40+$markup40)/$currency_rate;
                $sum40 += number_format($total40, 2, '.', '');
              }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                $amount40=$array_amounts['c40'];
                $total40=$amount40/$currency_rate;
                $sum40 += number_format($total40, 2, '.', '');
              }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                $markup40=$array_markups['m40'];
                $total40=$markup40/$currency_rate;
                $sum40 += number_format($total40, 2, '.', '');
              }

              if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                $amount40hc=$array_amounts['c40hc'];
                $markup40hc=$array_markups['m40hc'];
                $total40hc=($amount40hc+$markup40hc)/$currency_rate;
                $sum40hc += number_format($total40hc, 2, '.', '');
              }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                $amount40hc=$array_amounts['c40hc'];
                $total40hc=$amount40hc/$currency_rate;
                $sum40hc += number_format($total40hc, 2, '.', '');
              }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                $markup40hc=$array_markups['m40hc'];
                $total40hc=$markup40hc/$currency_rate;
                $sum40hc += number_format($total40hc, 2, '.', '');
              }

              if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                $amount40nor=$array_amounts['c40nor'];
                $markup40nor=$array_markups['m40nor'];
                $total40nor=($amount40nor+$markup40nor)/$currency_rate;
                $sum40nor += number_format($total40nor, 2, '.', '');
              }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                $amount40nor=$array_amounts['c40nor'];
                $total40nor=$amount40nor/$currency_rate;
                $sum40nor += number_format($total40nor, 2, '.', '');
              }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                $markup40nor=$array_markups['m40nor'];
                $total40nor=$markup40nor/$currency_rate;
                $sum40nor += number_format($total40nor, 2, '.', '');
              }

              if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                $amount45=$array_amounts['c45'];
                $markup45=$array_markups['m45'];
                $total45=($amount45+$markup45)/$currency_rate;
                $sum45 += number_format($total45, 2, '.', '');
              }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                $amount45=$array_amounts['c45'];
                $total45=$amount45/$currency_rate;
                $sum45 += number_format($total45, 2, '.', '');
              }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                $markup45=$array_markups['m45'];
                $total45=$markup45/$currency_rate;
                $sum45 += number_format($total45, 2, '.', '');
              }

              $value->total_20=number_format($sum20, 2, '.', '');
              $value->total_40=number_format($sum40, 2, '.', '');
              $value->total_40hc=number_format($sum40hc, 2, '.', '');
              $value->total_40nor=number_format($sum40nor, 2, '.', '');
              $value->total_45=number_format($sum45, 2, '.', '');
            }
          }
          if(!$rate->inland->isEmpty()){
            foreach($rate->inland as $value){
              if($quote->pdf_option->grouped_destination_charges==1){
                $typeCurrency =  $quote->pdf_option->destination_charges_currency;
              }else{
                $typeCurrency =  $currency_cfg->alphacode;
              }
              $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
              $array_amounts = json_decode($value->rate,true);
              $array_markups = json_decode($value->markup,true);
              if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                $amount20=$array_amounts['c20'];
                $markup20=$array_markups['m20'];
                $total20=($amount20+$markup20)/$currency_rate;
                $inland20 += number_format($total20, 2, '.', '');
              }
              if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                $amount40=$array_amounts['c40'];
                $markup40=$array_markups['m40'];
                $total40=($amount40+$markup40)/$currency_rate;
                $inland40 += number_format($total40, 2, '.', '');
              }
              if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                $amount40hc=$array_amounts['c40hc'];
                $markup40hc=$array_markups['m40hc'];
                $total40hc=($amount40hc+$markup40hc)/$currency_rate;
                $inland40hc += number_format($total40hc, 2, '.', '');
              }
              if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                $amount40nor=$array_amounts['c40nor'];
                $markup40nor=$array_markups['m40nor'];
                $total40nor=($amount40nor+$markup40nor)/$currency_rate;
                $inland40nor += number_format($total40nor, 2, '.', '');
              }
              if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                $amount45=$array_amounts['c45'];
                $markup45=$array_markups['m45'];
                $total45=($amount45+$markup45)/$currency_rate;
                $inland45 += number_format($total45, 2, '.', '');
              }
              $value->total_20=number_format($inland20, 2, '.', '');
              $value->total_40=number_format($inland40, 2, '.', '');
              $value->total_40hc=number_format($inland40hc, 2, '.', '');
              $value->total_40nor=number_format($inland40nor, 2, '.', '');
              $value->total_45=number_format($inland45, 2, '.', '');
            }
          } 
        }
      }
    }

    $origin_charges_detailed = collect($origin_charges);

    $origin_charges_detailed = $origin_charges_detailed->groupBy([

      function ($item) {
        return $item['carrier']['name'];
      },   
      function ($item) {
        return $item['origin_port']['name'].', '.$item['origin_port']['code'];
      },
      function ($item) {
        return $item['destination_port']['name'];
      },

    ], $preserveKeys = true);

    foreach($origin_charges_detailed as $origin=>$item){
      foreach($item as $destination=>$items){
        foreach($items as $carrier=>$itemsDetail){
          foreach ($itemsDetail as $value) {     
            foreach ($value->charge as $amounts) {
              $sum20=0;
              $sum40=0;
              $sum40hc=0;
              $sum40nor=0;
              $sum45=0;
              $total40=0;
              $total20=0;
              $total40hc=0;
              $total40nor=0;
              $total45=0;
              $inland20= 0;
              $inland40= 0;
              $inland40hc= 0;
              $inland40nor= 0;
              $inland45= 0;
              if($amounts->type_id==1){
                if($quote->pdf_option->grouped_origin_charges==1){
                  $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                }else{
                  $typeCurrency =  $currency_cfg->alphacode;
                }
                $currency_rate=$this->ratesCurrency($amounts->currency_id,$typeCurrency);
                $array_amounts = json_decode($amounts->amount,true);
                $array_markups = json_decode($amounts->markups,true);
                if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                  $sum20=$array_amounts['c20']+$array_markups['m20'];
                  $total20=$sum20/$currency_rate;
                }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                  $sum20=$array_amounts['c20'];
                  $total20=$sum20/$currency_rate;
                }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                  $sum20=$array_markups['m20'];
                  $total20=$sum20/$currency_rate;
                }

                if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                  $sum40=$array_amounts['c40']+$array_markups['m40'];
                  $total40=$sum40/$currency_rate;
                }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                  $sum40=$array_amounts['c40'];
                  $total40=$sum40/$currency_rate;
                }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                  $sum40=$array_markups['m40'];
                  $total40=$sum40/$currency_rate;
                }

                if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                  $sum40hc=$array_amounts['c40hc']+$array_markups['m40hc'];
                  $total40hc=$sum40hc/$currency_rate;
                }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                  $sum40hc=$array_amounts['c40hc'];
                  $total40hc=$sum40hc/$currency_rate;
                }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                  $sum40hc=$array_markups['m40hc'];
                  $total40hc=$sum40hc/$currency_rate;
                }

                if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                  $sum40nor=$array_amounts['c40nor']+$array_markups['m40nor'];
                  $total40nor=$sum40nor/$currency_rate;
                }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                  $sum40nor=$array_amounts['c40nor'];
                  $total40nor=$sum40nor/$currency_rate;
                }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                  $sum40nor=$array_markups['m40nor'];
                  $total40nor=$sum40nor/$currency_rate;
                }

                if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $sum45=$array_amounts['c45']+$array_markups['m45'];
                  $total45=$sum45/$currency_rate;
                }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                  $sum45=$array_amounts['c45'];
                  $total45=$sum45/$currency_rate;
                }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $sum45=$array_markups['m45'];
                  $total45=$sum45/$currency_rate;
                }

                $amounts->total_20=number_format($total20, 2, '.', '');
                $amounts->total_40=number_format($total40, 2, '.', '');
                $amounts->total_40hc=number_format($total40hc, 2, '.', '');
                $amounts->total_40nor=number_format($total40nor, 2, '.', '');
                $amounts->total_45=number_format($total45, 2, '.', '');
              }
            }
            if(!$value->inland->isEmpty()){
              foreach($value->inland as $value){
                if($quote->pdf_option->grouped_origin_charges==1){
                  $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                }else{
                  $typeCurrency =  $currency_cfg->alphacode;
                }
                $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
                $array_amounts = json_decode($value->rate,true);
                $array_markups = json_decode($value->markup,true);
                if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                  $amount20=$array_amounts['c20'];
                  $markup20=$array_markups['m20'];
                  $total20=($amount20+$markup20)/$currency_rate;
                  $inland20 = number_format($total20, 2, '.', '');
                }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                  $amount20=$array_amounts['c20'];
                  $total20=$amount20/$currency_rate;
                  $inland20 = number_format($total20, 2, '.', '');
                }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                  $markup20=$array_markups['m20'];
                  $total20=$markup20/$currency_rate;
                  $inland20 = number_format($total20, 2, '.', '');
                }

                if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                  $amount40=$array_amounts['c40'];
                  $markup40=$array_markups['m40'];
                  $total40=($amount40+$markup40)/$currency_rate;
                  $inland40 = number_format($total40, 2, '.', '');
                }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                  $amount40=$array_amounts['c40'];
                  $total40=$amount40/$currency_rate;
                  $inland40 = number_format($total40, 2, '.', '');
                }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                  $markup40=$array_markups['m40'];
                  $total40=$markup40/$currency_rate;
                  $inland40 = number_format($total40, 2, '.', '');
                }

                if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                  $amount40hc=$array_amounts['c40hc'];
                  $markup40hc=$array_markups['m40hc'];
                  $total40hc=($amount40hc+$markup40hc)/$currency_rate;
                  $inland40hc = number_format($total40hc, 2, '.', '');
                }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                  $amount40hc=$array_amounts['c40hc'];
                  $total40hc=$amount40hc/$currency_rate;
                  $inland40hc = number_format($total40hc, 2, '.', '');
                }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                  $markup40hc=$array_markups['m40hc'];
                  $total40hc=$markup40hc/$currency_rate;
                  $inland40hc = number_format($total40hc, 2, '.', '');
                }

                if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                  $amount40nor=$array_amounts['c40nor'];
                  $markup40nor=$array_markups['m40nor'];
                  $total40nor=($amount40nor+$markup40nor)/$currency_rate;
                  $inland40nor = number_format($total40nor, 2, '.', '');
                }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                  $amount40nor=$array_amounts['c40nor'];
                  $total40nor=$amount40nor/$currency_rate;
                  $inland40nor = number_format($total40nor, 2, '.', '');
                }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                  $markup40nor=$array_markups['m40nor'];
                  $total40nor=$markup40nor/$currency_rate;
                  $inland40nor = number_format($total40nor, 2, '.', '');
                }

                if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $amount45=$array_amounts['c45'];
                  $markup45=$array_markups['m45'];
                  $total45=($amount45+$markup45)/$currency_rate;
                  $inland45 = number_format($total45, 2, '.', '');
                }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                  $amount45=$array_amounts['c45'];
                  $total45=$amount45/$currency_rate;
                  $inland45 = number_format($total45, 2, '.', '');
                }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $markup45=$array_markups['m45'];
                  $total45=$markup45/$currency_rate;
                  $inland45 = number_format($total45, 2, '.', '');
                }

                $value->total_20=number_format($inland20, 2, '.', '');
                $value->total_40=number_format($inland40, 2, '.', '');
                $value->total_40hc=number_format($inland40hc, 2, '.', '');
                $value->total_40nor=number_format($inland40nor, 2, '.', '');
                $value->total_45=number_format($inland45, 2, '.', '');
              }
            }            
          }
        } 
      }
    }

    /*** DESTINATION CHARGES ***/

    $destination_charges_grouped = collect($destination_charges);

    $destination_charges_grouped = $destination_charges_grouped->groupBy([

      function ($item) {
        return $item['destination_port']['name'].', '.$item['destination_port']['code'];
      },
      function ($item) {
        return $item['carrier']['name'];
      },

    ], $preserveKeys = true);
    foreach($destination_charges_grouped as $origin=>$detail){
      foreach($detail as $item){
        foreach($item as $rate){

          $sum20= 0;
          $sum40= 0;
          $sum40hc= 0;
          $sum40nor= 0;
          $sum45= 0;
          $inland20= 0;
          $inland40= 0;
          $inland40hc= 0;
          $inland40nor= 0;
          $inland45= 0;

          foreach($rate->charge as $value){

            if($value->type_id==2){
              if($quote->pdf_option->grouped_destination_charges==1){
                $typeCurrency =  $quote->pdf_option->destination_charges_currency;
              }else{
                $typeCurrency =  $currency_cfg->alphacode;
              }
              $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
              $array_amounts = json_decode($value->amount,true);
              $array_markups = json_decode($value->markups,true);
              if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                $amount20=$array_amounts['c20'];
                $markup20=$array_markups['m20'];
                $total20=($amount20+$markup20)/$currency_rate;
                $sum20 += number_format($total20, 2, '.', '');
              }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                $amount20=$array_amounts['c20'];
                $total20=$amount20/$currency_rate;
                $sum20 += number_format($total20, 2, '.', '');
              }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                $markup20=$array_markups['m20'];
                $total20=$markup20/$currency_rate;
                $sum20 += number_format($total20, 2, '.', '');
              }

              if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                $amount40=$array_amounts['c40'];
                $markup40=$array_markups['m40'];
                $total40=($amount40+$markup40)/$currency_rate;
                $sum40 += number_format($total40, 2, '.', '');
              }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                $amount40=$array_amounts['c40'];
                $total40=$amount40/$currency_rate;
                $sum40 += number_format($total40, 2, '.', '');
              }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                $markup40=$array_markups['m40'];
                $total40=$markup40/$currency_rate;
                $sum40 += number_format($total40, 2, '.', '');
              }

              if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                $amount40hc=$array_amounts['c40hc'];
                $markup40hc=$array_markups['m40hc'];
                $total40hc=($amount40hc+$markup40hc)/$currency_rate;
                $sum40hc += number_format($total40hc, 2, '.', '');
              }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                $amount40hc=$array_amounts['c40hc'];
                $total40hc=$amount40hc/$currency_rate;
                $sum40hc += number_format($total40hc, 2, '.', '');
              }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                $markup40hc=$array_markups['m40hc'];
                $total40hc= $markup40hc/$currency_rate;
                $sum40hc += number_format($total40hc, 2, '.', '');
              }

              if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                $amount40nor=$array_amounts['c40nor'];
                $markup40nor=$array_markups['m40nor'];
                $total40nor=($amount40nor+$markup40nor)/$currency_rate;
                $sum40nor += number_format($total40nor, 2, '.', '');
              }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                $amount40nor=$array_amounts['c40nor'];
                $total40nor=$amount40nor/$currency_rate;
                $sum40nor += number_format($total40nor, 2, '.', '');
              }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                $markup40nor=$array_markups['m40nor'];
                $total40nor=$markup40nor/$currency_rate;
                $sum40nor += number_format($total40nor, 2, '.', '');
              }

              if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                $amount45=$array_amounts['c45'];
                $markup45=$array_markups['m45'];
                $total45=($amount45+$markup45)/$currency_rate;
                $sum45 += number_format($total45, 2, '.', '');
              }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                $markup45=$array_markups['m45'];
                $total45=$amount45/$currency_rate;
                $sum45 += number_format($total45, 2, '.', '');
              }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                $markup45=$array_markups['m45'];
                $total45=$markup45/$currency_rate;
                $sum45 += number_format($total45, 2, '.', '');
              }

              $value->total_20=number_format($sum20, 2, '.', '');
              $value->total_40=number_format($sum40, 2, '.', '');
              $value->total_40hc=number_format($sum40hc, 2, '.', '');
              $value->total_40nor=number_format($sum40nor, 2, '.', '');
              $value->total_45=number_format($sum45, 2, '.', '');
            }
          }
          if(!$rate->inland->isEmpty()){
            foreach($rate->inland as $value){
              if($quote->pdf_option->grouped_destination_charges==1){
                $typeCurrency =  $quote->pdf_option->destination_charges_currency;
              }else{
                $typeCurrency =  $currency_cfg->alphacode;
              }
              $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
              $array_amounts = json_decode($value->rate,true);
              $array_markups = json_decode($value->markup,true);
              if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                $amount20=$array_amounts['c20'];
                $markup20=$array_markups['m20'];
                $total20=($amount20+$markup20)/$currency_rate;
                $inland20 += number_format($total20, 2, '.', '');
              }
              if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                $amount40=$array_amounts['c40'];
                $markup40=$array_markups['m40'];
                $total40=($amount40+$markup40)/$currency_rate;
                $inland40 += number_format($total40, 2, '.', '');
              }
              if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                $amount40hc=$array_amounts['c40hc'];
                $markup40hc=$array_markups['m40hc'];
                $total40hc=($amount40hc+$markup40hc)/$currency_rate;
                $inland40hc += number_format($total40hc, 2, '.', '');
              }
              if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                $amount40nor=$array_amounts['c40nor'];
                $markup40nor=$array_markups['m40nor'];
                $total40nor=($amount40nor+$markup40nor)/$currency_rate;
                $inland40nor += number_format($total40nor, 2, '.', '');
              }
              if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                $amount45=$array_amounts['c45'];
                $markup45=$array_markups['m45'];
                $total45=($amount45+$markup45)/$currency_rate;
                $inland45 += number_format($total45, 2, '.', '');
              }
              $value->total_20=number_format($inland20, 2, '.', '');
              $value->total_40=number_format($inland40, 2, '.', '');
              $value->total_40hc=number_format($inland40hc, 2, '.', '');
              $value->total_40nor=number_format($inland40nor, 2, '.', '');
              $value->total_45=number_format($inland45, 2, '.', '');
            }
          } 
        }
      }
    }    

    $destination_charges = $destination_charges->groupBy([

      function ($item) {
        return $item['carrier']['name'];
      },   
      function ($item) {
        return $item['destination_port']['name'].', '.$item['destination_port']['code'];
      },
      function ($item) {
        return $item['origin_port']['name'];
      },

    ], $preserveKeys = true);

    foreach($destination_charges as $carrier=>$item){
      foreach($item as $destination=>$items){
        foreach($items as $origin=>$itemsDetail){
          foreach ($itemsDetail as $value) {     
            foreach ($value->charge as $amounts) {
              $sum20=0;
              $sum40=0;
              $sum40hc=0;
              $sum40nor=0;
              $sum45=0;
              $total40=0;
              $total20=0;
              $total40hc=0;
              $total40nor=0;
              $total45=0;
              $inland20= 0;
              $inland40= 0;
              $inland40hc= 0;
              $inland40nor= 0;
              $inland45= 0;          
              if($amounts->type_id==2){
                //dd($quote->pdf_option->destination_charges_currency);
                if($quote->pdf_option->grouped_destination_charges==1){
                  $typeCurrency =  $quote->pdf_option->destination_charges_currency;
                }else{
                  $typeCurrency =  $currency_cfg->alphacode;
                }
                $currency_rate=$this->ratesCurrency($amounts->currency_id,$typeCurrency);
                $array_amounts = json_decode($amounts->amount,true);
                $array_markups = json_decode($amounts->markups,true);
                if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                  $sum20=$array_amounts['c20']+$array_markups['m20'];
                  $total20=$sum20/$currency_rate;
                }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                  $sum20=$array_amounts['c20'];
                  $total20=$sum20/$currency_rate;
                }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                  $sum20=$array_markups['m20'];
                  $total20=$sum20/$currency_rate;
                }

                if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                  $sum40=$array_amounts['c40']+$array_markups['m40'];
                  $total40=$sum40/$currency_rate;
                }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                  $sum40=$array_amounts['c40'];
                  $total40=$sum40/$currency_rate;
                }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                  $sum40=$array_markups['m40'];
                  $total40=$sum40/$currency_rate;
                }

                if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                  $sum40hc=$array_amounts['c40hc']+$array_markups['m40hc'];
                  $total40hc=$sum40hc/$currency_rate;
                }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                  $sum40hc=$array_amounts['c40hc'];
                  $total40hc=$sum40hc/$currency_rate;
                }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                  $sum40hc=$array_markups['m40hc'];
                  $total40hc=$sum40hc/$currency_rate;
                }

                if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                  $sum40nor=$array_amounts['c40nor']+$array_markups['m40nor'];
                  $total40nor=$sum40nor/$currency_rate;
                }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                  $sum40nor=$array_amounts['c40nor'];
                  $total40nor=$sum40nor/$currency_rate;
                }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                  $sum40nor=$array_markups['m40nor'];
                  $total40nor=$sum40nor/$currency_rate;
                }

                if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $sum45=$array_amounts['c45']+$array_markups['m45'];
                  $total45=$sum45/$currency_rate;
                }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                  $sum45=$array_amounts['c45'];
                  $total45=$sum45/$currency_rate;
                }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $sum45=$array_markups['m45'];
                  $total45=$sum45/$currency_rate;
                }        

                $amounts->total_20=number_format($total20, 2, '.', '');
                $amounts->total_40=number_format($total40, 2, '.', '');
                $amounts->total_40hc=number_format($total40hc, 2, '.', '');
                $amounts->total_40nor=number_format($total40nor, 2, '.', '');
                $amounts->total_45=number_format($total45, 2, '.', '');
              }
            }
            if(!$value->inland->isEmpty()){
              foreach($value->inland as $value){
                if($quote->pdf_option->grouped_destination_charges==1){
                  $typeCurrency =  $quote->pdf_option->destination_charges_currency;
                }else{
                  $typeCurrency =  $currency_cfg->alphacode;
                }
                $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
                $array_amounts = json_decode($value->rate,true);
                $array_markups = json_decode($value->markup,true);
                if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                  $amount20=$array_amounts['c20'];
                  $markup20=$array_markups['m20'];
                  $total20=($amount20+$markup20)/$currency_rate;
                  $inland20 = number_format($total20, 2, '.', '');
                }
                if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                  $amount40=$array_amounts['c40'];
                  $markup40=$array_markups['m40'];
                  $total40=($amount40+$markup40)/$currency_rate;
                  $inland40 = number_format($total40, 2, '.', '');
                }
                if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                  $amount40hc=$array_amounts['c40hc'];
                  $markup40hc=$array_markups['m40hc'];
                  $total40hc=($amount40hc+$markup40hc)/$currency_rate;
                  $inland40hc = number_format($total40hc, 2, '.', '');
                }
                if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                  $amount40nor=$array_amounts['c40nor'];
                  $markup40nor=$array_markups['m40nor'];
                  $total40nor=($amount40nor+$markup40nor)/$currency_rate;
                  $inland40nor = number_format($total40nor, 2, '.', '');
                }
                if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $amount45=$array_amounts['c45'];
                  $markup45=$array_markups['m45'];
                  $total45=($amount45+$markup45)/$currency_rate;
                  $inland45 = number_format($total45, 2, '.', '');
                }
                $value->total_20=number_format($inland20, 2, '.', '');
                $value->total_40=number_format($inland40, 2, '.', '');
                $value->total_40hc=number_format($inland40hc, 2, '.', '');
                $value->total_40nor=number_format($inland40nor, 2, '.', '');
                $value->total_45=number_format($inland45, 2, '.', '');
              }
            } 
          }
        } 
      }
    }
    //dd(json_encode($destination_charges));

    /**** FREIGHT CHARGES ****/

    /*** Detailed ***/

    $freight_charges_detailed = collect($freight_charges);

    $freight_charges_detailed = $freight_charges_detailed->groupBy([   
      function ($item) {
        return $item['origin_port']['name'].', '.$item['origin_port']['code'];
      },
      function ($item) {
        return $item['destination_port']['name'].', '.$item['destination_port']['code'];
      },
      function ($item) {
        return $item['carrier']['name'];
      },      
    ], $preserveKeys = true);

    foreach($freight_charges_detailed as $origin=>$item){
      foreach($item as $destination=>$items){
        foreach($items as $carrier=>$itemsDetail){
          foreach ($itemsDetail as $value) {     
            foreach ($value->charge as $amounts) {
              if($amounts->type_id==3){
                $sum_freight_20=0;
                $sum_freight_40=0;
                $sum_freight_40hc=0;
                $sum_freight_40nor=0;
                $sum_freight_45=0;
                $total_freight_40=0;
                $total_freight_20=0;
                $total_freight_40hc=0;
                $total_freight_40nor=0;
                $total_freight_45=0;
                //dd($quote->pdf_option->destination_charges_currency);
                if($quote->pdf_option->grouped_freight_charges==1){
                  $typeCurrency =  $quote->pdf_option->freight_charges_currency;
                }else{
                  $typeCurrency =  $currency_cfg->alphacode;
                }
                $currency_rate=$this->ratesCurrency($amounts->currency_id,$typeCurrency);
                $array_amounts = json_decode($amounts->amount,true);
                $array_markups = json_decode($amounts->markups,true);
                if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                  $sum_freight_20=$array_amounts['c20']+$array_markups['m20'];
                  $total_freight_20=$sum_freight_20/$currency_rate;
                }
                if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                  $sum_freight_40=$array_amounts['c40']+$array_markups['m40'];
                  $total_freight_40=$sum_freight_40/$currency_rate;
                }
                if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                  $sum_freight_40hc=$array_amounts['c40hc']+$array_markups['m40hc'];
                  $total_freight_40hc=$sum_freight_40hc/$currency_rate;
                }
                if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                  $sum_freight_40nor=$array_amounts['c40nor']+$array_markups['m40nor'];
                  $total_freight_40nor=$sum_freight_40nor/$currency_rate;
                }
                if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $sum_freight_45=$array_amounts['c45']+$array_markups['m45'];
                  $total_freight_45=$sum_freight_45/$currency_rate;
                }            

                $amounts->total_20 = number_format($total_freight_20, 2, '.', '');
                $amounts->total_40 = number_format($total_freight_40, 2, '.', '');
                $amounts->total_40hc = number_format($total_freight_40hc, 2, '.', '');
                $amounts->total_40nor = number_format($total_freight_40nor, 2, '.', '');
                $amounts->total_45 = number_format($total_freight_45, 2, '.', '');
              }
            }
          }
        } 
      }
    }

    //dd($freight_charges_detailed);

    /*** Grouped in ***/

    $freight_charges_grouped = collect($freight_charges);

    $freight_charges_grouped = $freight_charges_grouped->groupBy([

      function ($item) {
        return $item['origin_port']['name'].', '.$item['origin_port']['code'];
      },
      function ($item) {
        return $item['destination_port']['name'].', '.$item['destination_port']['code'];
      },
      function ($item) {
        return $item['carrier']['name'];
      },

    ], $preserveKeys = true);

    foreach($freight_charges_grouped as $freight){
      foreach($freight as $detail){
        foreach($detail as $item){
          $total_rate20=0;
          $total_rate40=0;
          $total_rate40hc=0;
          $total_rate40nor=0;
          $total_rate45=0;

          $total_rate_markup20=0;
          $total_rate_markup40=0;
          $total_rate_markup40hc=0;
          $total_rate_markup40nor=0;
          $total_rate_markup45=0;

          foreach($item as $rate){
            $sum20=0;
            $sum40=0;
            $sum40hc=0;
            $sum40nor=0;
            $sum45=0;
            $total40=0;
            $total20=0;
            $total40hc=0;
            $total40nor=0;
            $total45=0;

            if($quote->pdf_option->grouped_freight_charges==1){
              $typeCurrency =  $quote->pdf_option->freight_charges_currency;
            }else{
              $typeCurrency =  $currency_cfg->alphacode;
            }

            $currency_rate=$this->ratesCurrency($rate->currency_id,$typeCurrency);

            $array_rate_amounts = json_decode($rate->rates,true);
            //$array_rate_amounts = json_decode($array_rate_amounts,true);
            $array_rate_markups = json_decode($rate->markups,true);

            $currency = Currency::find($rate->currency_id);
            $item->currency_usd = $currency->rates;
            $item->currency_eur = $currency->rates_eur;      

            if(isset($array_rate_amounts['c20'])){
              $amount_rate20=$array_rate_amounts['c20'];
              $total_rate20=$amount_rate20/$currency_rate;
            }

            if(isset($array_rate_markups['m20'])){
              $markup_rate20=$array_rate_markups['m20'];
              $total_rate_markup20=$markup_rate20/$currency_rate;
            }

            if(isset($array_rate_amounts['c40'])){
              $amount_rate40=$array_rate_amounts['c40'];
              $total_rate40=$amount_rate40/$currency_rate;
            }

            if(isset($array_rate_markups['m40'])){
              $markup_rate40=$array_rate_markups['m40'];
              $total_rate_markup40=$markup_rate40/$currency_rate;
            }

            if(isset($array_rate_amounts['c40hc'])){
              $amount_rate40hc=$array_rate_amounts['c40hc'];
              $total_rate40hc=$amount_rate40hc/$currency_rate;
            }
            if(isset($array_rate_markups['m40hc'])){
              $markup_rate40hc=$array_rate_markups['m40hc'];
              $total_rate_markup40hc=$markup_rate40hc/$currency_rate;
            }

            if(isset($array_rate_amounts['c40nor'])){
              $amount_rate40nor=$array_rate_amounts['c40nor'];
              $total_rate40nor=$amount_rate40nor/$currency_rate;
            }
            if(isset($array_rate_markups['m40nor'])){
              $markup_rate40nor=$array_rate_markups['m40nor'];
              $total_rate_markup40nor=$markup_rate40nor/$currency_rate;
            }

            if(isset($array_rate_amounts['c45'])){
              $amount_rate45=$array_rate_amounts['c45'];
              $total_rate45=$amount_rate45/$currency_rate;
            }
            if(isset($array_rate_markups['m45'])){
              $markup_rate45=$array_rate_markups['m45'];
              $total_rate_markup45=$markup_rate45/$currency_rate;
            } 

            $rate->total_rate20=number_format($total_rate20+$total_rate_markup20, 2, '.', '');
            $rate->total_rate40=number_format($total_rate40+$total_rate_markup40, 2, '.', '');
            $rate->total_rate40hc=number_format($total_rate40hc+$total_rate_markup40hc, 2, '.', '');
            $rate->total_rate40nor=number_format($total_rate40nor+$total_rate_markup40nor, 2, '.', '');
            $rate->total_rate45=number_format($total_rate45+$total_rate_markup45, 2, '.', '');

            $rate->total_rate_a20=number_format($total_rate20, 2, '.', '');
            $rate->total_rate_a40=number_format($total_rate40, 2, '.', '');
            $rate->total_rate_a40hc=number_format($total_rate40hc, 2, '.', '');
            $rate->total_rate_a40nor=number_format($total_rate40nor, 2, '.', '');
            $rate->total_rate_a45=number_format($total_rate45, 2, '.', '');

            $rate->total_rate_m20=number_format($total_rate_markup20, 2, '.', '');
            $rate->total_rate_m40=number_format($total_rate_markup40, 2, '.', '');
            $rate->total_rate_m40hc=number_format($total_rate_markup40hc, 2, '.', '');
            $rate->total_rate_m40nor=number_format($total_rate_markup40nor, 2, '.', '');
            $rate->total_rate_m45=number_format($total_rate_markup45, 2, '.', '');             

            foreach ($rate->charge as $amounts) {
              if($amounts->type_id==3){
                if($quote->pdf_option->grouped_freight_charges==1){
                  $typeCurrency =  $quote->pdf_option->freight_charges_currency;
                }else{
                  $typeCurrency =  $currency_cfg->alphacode;
                }
                $currency_rate=$this->ratesCurrency($amounts->currency_id,$typeCurrency);
                $array_amounts = json_decode($amounts->amount,true);
                $array_markups = json_decode($amounts->markups,true);
                if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                  $sum20=$array_amounts['c20']+$array_markups['m20'];
                  $total20=$sum20/$currency_rate;
                }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                  $sum20=$array_amounts['c20'];
                  $total20=$sum20/$currency_rate;
                }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                  $sum20=$array_markups['m20'];
                  $total20=$sum20/$currency_rate;
                }

                if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                  $sum40=$array_amounts['c40']+$array_markups['m40'];
                  $total40=$sum40/$currency_rate;
                }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                  $sum40=$array_amounts['c40'];
                  $total40=$sum40/$currency_rate;
                }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                  $sum40=$array_markups['m40'];
                  $total40=$sum40/$currency_rate;
                }

                if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                  $sum40hc=$array_amounts['c40hc']+$array_markups['m40hc'];
                  $total40hc=$sum40hc/$currency_rate;
                }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                  $sum40hc=$array_amounts['c40hc'];
                  $total40hc=$sum40hc/$currency_rate;
                }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                  $sum40hc=$array_markups['m40hc'];
                  $total40hc=$sum40hc/$currency_rate;
                }

                if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                  $sum40nor=$array_amounts['c40nor']+$array_markups['m40nor'];
                  $total40nor=$sum40nor/$currency_rate;
                }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                  $sum40nor=$array_amounts['c40nor'];
                  $total40nor=$sum40nor/$currency_rate;
                }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                  $sum40nor=$array_markups['m40nor'];
                  $total40nor=$sum40nor/$currency_rate;
                }

                if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $sum45=$array_amounts['c45']+$array_markups['m45'];
                  $total45=$sum45/$currency_rate;
                }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                  $sum45=$array_amounts['c45'];
                  $total45=$sum45/$currency_rate;
                }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $sum45=$array_markups['m45'];
                  $total45=$sum45/$currency_rate;
                }

                $amounts->total_20=number_format($total20, 2, '.', '');
                $amounts->total_40=number_format($total40, 2, '.', '');
                $amounts->total_40hc=number_format($total40hc, 2, '.', '');
                $amounts->total_40nor=number_format($total40nor, 2, '.', '');
                $amounts->total_45=number_format($total45, 2, '.', '');
              }
            }
          }
        }
      }
    }

    $contact_email = Contact::find($quote->contact_id);
    $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
    $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
    $user = User::where('id',\Auth::id())->with('companyUser')->first();

    if(\Auth::user()->company_user_id){
      $company_user=CompanyUser::find(\Auth::user()->company_user_id);
      $type=$company_user->type_pdf;
      $ammounts_type=$company_user->pdf_ammounts;
      $currency_cfg = Currency::find($company_user->currency_id);
    }

    $view = \View::make('quotesv2.pdf.index', ['quote'=>$quote,'rates'=>$rates,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'user'=>$user,'currency_cfg'=>$currency_cfg,'ammounts_type'=>$ammounts_type,'charges_type'=>$type,'equipmentHides'=>$equipmentHides,'freight_charges_grouped'=>$freight_charges_grouped,'destination_charges'=>$destination_charges,'origin_charges_grouped'=>$origin_charges_grouped,'origin_charges_detailed'=>$origin_charges_detailed,'destination_charges_grouped'=>$destination_charges_grouped,'freight_charges_detailed'=>$freight_charges_detailed]);

    // EVENTO INTERCOM 
    $event = new  EventIntercom();
    $event->event_quoteEmail();

    $pdf = \App::make('dompdf.wrapper');
    $pdf->loadHTML($view)->save('pdf/temp_'.$quote->id.'.pdf');

    $subject = $request->subject;
    $body = $request->body;
    $to = $request->to;

    if($to!=''){
      $explode=explode(';',$to);
      foreach($explode as $item) {
        $send_quote = new SendQuote();
        $send_quote->to = trim($item);
        $send_quote->from = \Auth::user()->email;
        $send_quote->subject = $subject;
        $send_quote->body = $body;
        $send_quote->quote_id = $quote->id;
        $send_quote->status = 0;
        $send_quote->save();
      }
    }else{
      $send_quote = new SendQuote();
      $send_quote->to = $contact_email->email;
      $send_quote->from = \Auth::user()->email;
      $send_quote->subject = $subject;
      $send_quote->body = $body;
      $send_quote->quote_id = $quote->id;
      $send_quote->status = 0;
      $send_quote->save();
    }
    //SendQuotes::dispatch($subject,$body,$to,$quote,$contact_email->email);

    $quote->status='Sent';
    $quote->update();
    return response()->json(['message' => 'Ok']);
  }

  public function pdf(Request $request,$id)
  {
    $id = obtenerRouteKey($id);
    $equipmentHides = '';
    $quote = QuoteV2::findOrFail($id);
    $rates = AutomaticRate::where('quote_id',$quote->id)->with('charge')->get();
    $origin_charges = AutomaticRate::whereHas('charge', function ($query) {
      $query->where('type_id', 1);
    })->where('quote_id',$quote->id)->get();
    $freight_charges = AutomaticRate::whereHas('charge', function ($query) {
      $query->where('type_id', 3);
    })->where('quote_id',$quote->id)->get();
    $destination_charges = AutomaticRate::whereHas('charge', function ($query) {
      $query->where('type_id', 2);
    })->where('quote_id',$quote->id)->get();
    $contact_email = Contact::find($quote->contact_id);
    $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
    $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
    $user = User::where('id',\Auth::id())->with('companyUser')->first();
    if($quote->equipment!=''){
      $equipmentHides = $this->hideContainer($quote->equipment,'BD');
    }

    if(\Auth::user()->company_user_id){
      $company_user=CompanyUser::find(\Auth::user()->company_user_id);
      $type=$company_user->type_pdf;
      $ammounts_type=$company_user->pdf_ammounts;
      $currency_cfg = Currency::find($company_user->currency_id);
    }

    foreach ($rates as $item) {
      $total_rate20=0;
      $total_rate40=0;
      $total_rate40hc=0;
      $total_rate40nor=0;
      $total_rate45=0;

      $total_rate_markup20=0;
      $total_rate_markup40=0;
      $total_rate_markup40hc=0;
      $total_rate_markup40nor=0;
      $total_rate_markup45=0;

      if($quote->pdf_option->grouped_total_currency==1){
        $typeCurrency = $quote->pdf_option->total_in_currency;
      }else{
        $typeCurrency =  $currency_cfg->alphacode;
      }

      $currency_rate=$this->ratesCurrency($item->currency_id,$typeCurrency);

      $currency = Currency::find($item->currency_id);
      $item->currency_usd = $currency->rates;
      $item->currency_eur = $currency->rates_eur;

      foreach ($item->charge as $value) {
        $currency_charge = Currency::find($value->currency_id);
        $value->currency_usd = $currency_charge->rates;
        $value->currency_eur = $currency_charge->rates_eur;
      }
      foreach ($item->inland as $inland) {
        $currency_charge = Currency::find($inland->currency_id);
        $inland->currency_usd = $currency_charge->rates;
        $inland->currency_eur = $currency_charge->rates_eur;
      }

      $item->total_rate20=number_format($total_rate20+$total_rate_markup20, 2, '.', '');
      $item->total_rate40=number_format($total_rate40+$total_rate_markup40, 2, '.', '');
      $item->total_rate40hc=number_format($total_rate40hc+$total_rate_markup40hc, 2, '.', '');
      $item->total_rate40nor=number_format($total_rate40nor+$total_rate_markup40nor, 2, '.', '');
      $item->total_rate45=number_format($total_rate45+$total_rate_markup45, 2, '.', ''); 
    }

    $origin_charges_grouped = collect($origin_charges);

    $origin_charges_grouped = $origin_charges_grouped->groupBy([

      function ($item) {
        return $item['origin_port']['name'].', '.$item['origin_port']['code'];
      },
      function ($item) {
        return $item['carrier']['name'];
      },

    ], $preserveKeys = true);
    foreach($origin_charges_grouped as $origin=>$detail){
      foreach($detail as $item){
        foreach($item as $rate){

          $sum20= 0;
          $sum40= 0;
          $sum40hc= 0;
          $sum40nor= 0;
          $sum45= 0;
          $inland20= 0;
          $inland40= 0;
          $inland40hc= 0;
          $inland40nor= 0;
          $inland45= 0;

          foreach($rate->charge as $value){

            if($value->type_id==1){
              if($quote->pdf_option->grouped_origin_charges==1){
                $typeCurrency =  $quote->pdf_option->origin_charges_currency;
              }else{
                $typeCurrency =  $currency_cfg->alphacode;
              }
              $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
              $array_amounts = json_decode($value->amount,true);
              $array_markups = json_decode($value->markups,true);
              if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                $amount20=$array_amounts['c20'];
                $markup20=$array_markups['m20'];
                $total20=($amount20+$markup20)/$currency_rate;
                $sum20 += number_format($total20, 2, '.', '');
              }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                $amount20=$array_amounts['c20'];
                $total20=$amount20/$currency_rate;
                $sum20 += number_format($total20, 2, '.', '');
              }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                $markup20=$array_markups['m20'];
                $total20=$markup20/$currency_rate;
                $sum20 += number_format($total20, 2, '.', '');
              }

              if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                $amount40=$array_amounts['c40'];
                $markup40=$array_markups['m40'];
                $total40=($amount40+$markup40)/$currency_rate;
                $sum40 += number_format($total40, 2, '.', '');
              }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                $amount40=$array_amounts['c40'];
                $total40=$amount40/$currency_rate;
                $sum40 += number_format($total40, 2, '.', '');
              }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                $markup40=$array_markups['m40'];
                $total40=$markup40/$currency_rate;
                $sum40 += number_format($total40, 2, '.', '');
              }

              if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                $amount40hc=$array_amounts['c40hc'];
                $markup40hc=$array_markups['m40hc'];
                $total40hc=($amount40hc+$markup40hc)/$currency_rate;
                $sum40hc += number_format($total40hc, 2, '.', '');
              }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                $amount40hc=$array_amounts['c40hc'];
                $total40hc=$amount40hc/$currency_rate;
                $sum40hc += number_format($total40hc, 2, '.', '');
              }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                $markup40hc=$array_markups['m40hc'];
                $total40hc=$markup40hc/$currency_rate;
                $sum40hc += number_format($total40hc, 2, '.', '');
              }

              if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                $amount40nor=$array_amounts['c40nor'];
                $markup40nor=$array_markups['m40nor'];
                $total40nor=($amount40nor+$markup40nor)/$currency_rate;
                $sum40nor += number_format($total40nor, 2, '.', '');
              }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                $amount40nor=$array_amounts['c40nor'];
                $total40nor=$amount40nor/$currency_rate;
                $sum40nor += number_format($total40nor, 2, '.', '');
              }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                $markup40nor=$array_markups['m40nor'];
                $total40nor=$markup40nor/$currency_rate;
                $sum40nor += number_format($total40nor, 2, '.', '');
              }

              if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                $amount45=$array_amounts['c45'];
                $markup45=$array_markups['m45'];
                $total45=($amount45+$markup45)/$currency_rate;
                $sum45 += number_format($total45, 2, '.', '');
              }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                $amount45=$array_amounts['c45'];
                $total45=$amount45/$currency_rate;
                $sum45 += number_format($total45, 2, '.', '');
              }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                $markup45=$array_markups['m45'];
                $total45=$markup45/$currency_rate;
                $sum45 += number_format($total45, 2, '.', '');
              }

              $value->total_20=number_format($sum20, 2, '.', '');
              $value->total_40=number_format($sum40, 2, '.', '');
              $value->total_40hc=number_format($sum40hc, 2, '.', '');
              $value->total_40nor=number_format($sum40nor, 2, '.', '');
              $value->total_45=number_format($sum45, 2, '.', '');
            }
          }
          if(!$rate->inland->isEmpty()){
            foreach($rate->inland as $value){
              if($quote->pdf_option->grouped_destination_charges==1){
                $typeCurrency =  $quote->pdf_option->destination_charges_currency;
              }else{
                $typeCurrency =  $currency_cfg->alphacode;
              }
              $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
              $array_amounts = json_decode($value->rate,true);
              $array_markups = json_decode($value->markup,true);
              if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                $amount20=$array_amounts['c20'];
                $markup20=$array_markups['m20'];
                $total20=($amount20+$markup20)/$currency_rate;
                $inland20 += number_format($total20, 2, '.', '');
              }
              if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                $amount40=$array_amounts['c40'];
                $markup40=$array_markups['m40'];
                $total40=($amount40+$markup40)/$currency_rate;
                $inland40 += number_format($total40, 2, '.', '');
              }
              if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                $amount40hc=$array_amounts['c40hc'];
                $markup40hc=$array_markups['m40hc'];
                $total40hc=($amount40hc+$markup40hc)/$currency_rate;
                $inland40hc += number_format($total40hc, 2, '.', '');
              }
              if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                $amount40nor=$array_amounts['c40nor'];
                $markup40nor=$array_markups['m40nor'];
                $total40nor=($amount40nor+$markup40nor)/$currency_rate;
                $inland40nor += number_format($total40nor, 2, '.', '');
              }
              if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                $amount45=$array_amounts['c45'];
                $markup45=$array_markups['m45'];
                $total45=($amount45+$markup45)/$currency_rate;
                $inland45 += number_format($total45, 2, '.', '');
              }
              $value->total_20=number_format($inland20, 2, '.', '');
              $value->total_40=number_format($inland40, 2, '.', '');
              $value->total_40hc=number_format($inland40hc, 2, '.', '');
              $value->total_40nor=number_format($inland40nor, 2, '.', '');
              $value->total_45=number_format($inland45, 2, '.', '');
            }
          } 
        }
      }
    }

    $origin_charges_detailed = collect($origin_charges);

    $origin_charges_detailed = $origin_charges_detailed->groupBy([

      function ($item) {
        return $item['carrier']['name'];
      },   
      function ($item) {
        return $item['origin_port']['name'].', '.$item['origin_port']['code'];
      },
      function ($item) {
        return $item['destination_port']['name'];
      },

    ], $preserveKeys = true);

    foreach($origin_charges_detailed as $origin=>$item){
      foreach($item as $destination=>$items){
        foreach($items as $carrier=>$itemsDetail){
          foreach ($itemsDetail as $value) {     
            foreach ($value->charge as $amounts) {
              $sum20=0;
              $sum40=0;
              $sum40hc=0;
              $sum40nor=0;
              $sum45=0;
              $total40=0;
              $total20=0;
              $total40hc=0;
              $total40nor=0;
              $total45=0;
              $inland20= 0;
              $inland40= 0;
              $inland40hc= 0;
              $inland40nor= 0;
              $inland45= 0;
              if($amounts->type_id==1){
                if($quote->pdf_option->grouped_origin_charges==1){
                  $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                }else{
                  $typeCurrency =  $currency_cfg->alphacode;
                }
                $currency_rate=$this->ratesCurrency($amounts->currency_id,$typeCurrency);
                $array_amounts = json_decode($amounts->amount,true);
                $array_markups = json_decode($amounts->markups,true);
                if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                  $sum20=$array_amounts['c20']+$array_markups['m20'];
                  $total20=$sum20/$currency_rate;
                }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                  $sum20=$array_amounts['c20'];
                  $total20=$sum20/$currency_rate;
                }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                  $sum20=$array_markups['m20'];
                  $total20=$sum20/$currency_rate;
                }

                if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                  $sum40=$array_amounts['c40']+$array_markups['m40'];
                  $total40=$sum40/$currency_rate;
                }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                  $sum40=$array_amounts['c40'];
                  $total40=$sum40/$currency_rate;
                }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                  $sum40=$array_markups['m40'];
                  $total40=$sum40/$currency_rate;
                }

                if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                  $sum40hc=$array_amounts['c40hc']+$array_markups['m40hc'];
                  $total40hc=$sum40hc/$currency_rate;
                }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                  $sum40hc=$array_amounts['c40hc'];
                  $total40hc=$sum40hc/$currency_rate;
                }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                  $sum40hc=$array_markups['m40hc'];
                  $total40hc=$sum40hc/$currency_rate;
                }

                if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                  $sum40nor=$array_amounts['c40nor']+$array_markups['m40nor'];
                  $total40nor=$sum40nor/$currency_rate;
                }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                  $sum40nor=$array_amounts['c40nor'];
                  $total40nor=$sum40nor/$currency_rate;
                }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                  $sum40nor=$array_markups['m40nor'];
                  $total40nor=$sum40nor/$currency_rate;
                }

                if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $sum45=$array_amounts['c45']+$array_markups['m45'];
                  $total45=$sum45/$currency_rate;
                }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                  $sum45=$array_amounts['c45'];
                  $total45=$sum45/$currency_rate;
                }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $sum45=$array_markups['m45'];
                  $total45=$sum45/$currency_rate;
                }

                $amounts->total_20=number_format($total20, 2, '.', '');
                $amounts->total_40=number_format($total40, 2, '.', '');
                $amounts->total_40hc=number_format($total40hc, 2, '.', '');
                $amounts->total_40nor=number_format($total40nor, 2, '.', '');
                $amounts->total_45=number_format($total45, 2, '.', '');
              }
            }
            if(!$value->inland->isEmpty()){
              foreach($value->inland as $value){
                if($quote->pdf_option->grouped_origin_charges==1){
                  $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                }else{
                  $typeCurrency =  $currency_cfg->alphacode;
                }
                $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
                $array_amounts = json_decode($value->rate,true);
                $array_markups = json_decode($value->markup,true);
                if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                  $amount20=$array_amounts['c20'];
                  $markup20=$array_markups['m20'];
                  $total20=($amount20+$markup20)/$currency_rate;
                  $inland20 = number_format($total20, 2, '.', '');
                }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                  $amount20=$array_amounts['c20'];
                  $total20=$amount20/$currency_rate;
                  $inland20 = number_format($total20, 2, '.', '');
                }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                  $markup20=$array_markups['m20'];
                  $total20=$markup20/$currency_rate;
                  $inland20 = number_format($total20, 2, '.', '');
                }

                if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                  $amount40=$array_amounts['c40'];
                  $markup40=$array_markups['m40'];
                  $total40=($amount40+$markup40)/$currency_rate;
                  $inland40 = number_format($total40, 2, '.', '');
                }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                  $amount40=$array_amounts['c40'];
                  $total40=$amount40/$currency_rate;
                  $inland40 = number_format($total40, 2, '.', '');
                }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                  $markup40=$array_markups['m40'];
                  $total40=$markup40/$currency_rate;
                  $inland40 = number_format($total40, 2, '.', '');
                }

                if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                  $amount40hc=$array_amounts['c40hc'];
                  $markup40hc=$array_markups['m40hc'];
                  $total40hc=($amount40hc+$markup40hc)/$currency_rate;
                  $inland40hc = number_format($total40hc, 2, '.', '');
                }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                  $amount40hc=$array_amounts['c40hc'];
                  $total40hc=$amount40hc/$currency_rate;
                  $inland40hc = number_format($total40hc, 2, '.', '');
                }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                  $markup40hc=$array_markups['m40hc'];
                  $total40hc=$markup40hc/$currency_rate;
                  $inland40hc = number_format($total40hc, 2, '.', '');
                }

                if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                  $amount40nor=$array_amounts['c40nor'];
                  $markup40nor=$array_markups['m40nor'];
                  $total40nor=($amount40nor+$markup40nor)/$currency_rate;
                  $inland40nor = number_format($total40nor, 2, '.', '');
                }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                  $amount40nor=$array_amounts['c40nor'];
                  $total40nor=$amount40nor/$currency_rate;
                  $inland40nor = number_format($total40nor, 2, '.', '');
                }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                  $markup40nor=$array_markups['m40nor'];
                  $total40nor=$markup40nor/$currency_rate;
                  $inland40nor = number_format($total40nor, 2, '.', '');
                }

                if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $amount45=$array_amounts['c45'];
                  $markup45=$array_markups['m45'];
                  $total45=($amount45+$markup45)/$currency_rate;
                  $inland45 = number_format($total45, 2, '.', '');
                }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                  $amount45=$array_amounts['c45'];
                  $total45=$amount45/$currency_rate;
                  $inland45 = number_format($total45, 2, '.', '');
                }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $markup45=$array_markups['m45'];
                  $total45=$markup45/$currency_rate;
                  $inland45 = number_format($total45, 2, '.', '');
                }

                $value->total_20=number_format($inland20, 2, '.', '');
                $value->total_40=number_format($inland40, 2, '.', '');
                $value->total_40hc=number_format($inland40hc, 2, '.', '');
                $value->total_40nor=number_format($inland40nor, 2, '.', '');
                $value->total_45=number_format($inland45, 2, '.', '');
              }
            }            
          }
        } 
      }
    }

    /*** DESTINATION CHARGES ***/

    $destination_charges_grouped = collect($destination_charges);

    $destination_charges_grouped = $destination_charges_grouped->groupBy([

      function ($item) {
        return $item['destination_port']['name'].', '.$item['destination_port']['code'];
      },
      function ($item) {
        return $item['carrier']['name'];
      },

    ], $preserveKeys = true);
    foreach($destination_charges_grouped as $origin=>$detail){
      foreach($detail as $item){
        foreach($item as $rate){

          $sum20= 0;
          $sum40= 0;
          $sum40hc= 0;
          $sum40nor= 0;
          $sum45= 0;
          $inland20= 0;
          $inland40= 0;
          $inland40hc= 0;
          $inland40nor= 0;
          $inland45= 0;

          foreach($rate->charge as $value){

            if($value->type_id==2){
              if($quote->pdf_option->grouped_destination_charges==1){
                $typeCurrency =  $quote->pdf_option->destination_charges_currency;
              }else{
                $typeCurrency =  $currency_cfg->alphacode;
              }
              $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
              $array_amounts = json_decode($value->amount,true);
              $array_markups = json_decode($value->markups,true);
              if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                $amount20=$array_amounts['c20'];
                $markup20=$array_markups['m20'];
                $total20=($amount20+$markup20)/$currency_rate;
                $sum20 += number_format($total20, 2, '.', '');
              }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                $amount20=$array_amounts['c20'];
                $total20=$amount20/$currency_rate;
                $sum20 += number_format($total20, 2, '.', '');
              }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                $markup20=$array_markups['m20'];
                $total20=$markup20/$currency_rate;
                $sum20 += number_format($total20, 2, '.', '');
              }

              if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                $amount40=$array_amounts['c40'];
                $markup40=$array_markups['m40'];
                $total40=($amount40+$markup40)/$currency_rate;
                $sum40 += number_format($total40, 2, '.', '');
              }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                $amount40=$array_amounts['c40'];
                $total40=$amount40/$currency_rate;
                $sum40 += number_format($total40, 2, '.', '');
              }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                $markup40=$array_markups['m40'];
                $total40=$markup40/$currency_rate;
                $sum40 += number_format($total40, 2, '.', '');
              }

              if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                $amount40hc=$array_amounts['c40hc'];
                $markup40hc=$array_markups['m40hc'];
                $total40hc=($amount40hc+$markup40hc)/$currency_rate;
                $sum40hc += number_format($total40hc, 2, '.', '');
              }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                $amount40hc=$array_amounts['c40hc'];
                $total40hc=$amount40hc/$currency_rate;
                $sum40hc += number_format($total40hc, 2, '.', '');
              }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                $markup40hc=$array_markups['m40hc'];
                $total40hc= $markup40hc/$currency_rate;
                $sum40hc += number_format($total40hc, 2, '.', '');
              }

              if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                $amount40nor=$array_amounts['c40nor'];
                $markup40nor=$array_markups['m40nor'];
                $total40nor=($amount40nor+$markup40nor)/$currency_rate;
                $sum40nor += number_format($total40nor, 2, '.', '');
              }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                $amount40nor=$array_amounts['c40nor'];
                $total40nor=$amount40nor/$currency_rate;
                $sum40nor += number_format($total40nor, 2, '.', '');
              }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                $markup40nor=$array_markups['m40nor'];
                $total40nor=$markup40nor/$currency_rate;
                $sum40nor += number_format($total40nor, 2, '.', '');
              }

              if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                $amount45=$array_amounts['c45'];
                $markup45=$array_markups['m45'];
                $total45=($amount45+$markup45)/$currency_rate;
                $sum45 += number_format($total45, 2, '.', '');
              }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                $markup45=$array_markups['m45'];
                $total45=$amount45/$currency_rate;
                $sum45 += number_format($total45, 2, '.', '');
              }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                $markup45=$array_markups['m45'];
                $total45=$markup45/$currency_rate;
                $sum45 += number_format($total45, 2, '.', '');
              }

              $value->total_20=number_format($sum20, 2, '.', '');
              $value->total_40=number_format($sum40, 2, '.', '');
              $value->total_40hc=number_format($sum40hc, 2, '.', '');
              $value->total_40nor=number_format($sum40nor, 2, '.', '');
              $value->total_45=number_format($sum45, 2, '.', '');
            }
          }
          if(!$rate->inland->isEmpty()){
            foreach($rate->inland as $value){
              if($quote->pdf_option->grouped_destination_charges==1){
                $typeCurrency =  $quote->pdf_option->destination_charges_currency;
              }else{
                $typeCurrency =  $currency_cfg->alphacode;
              }
              $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
              $array_amounts = json_decode($value->rate,true);
              $array_markups = json_decode($value->markup,true);
              if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                $amount20=$array_amounts['c20'];
                $markup20=$array_markups['m20'];
                $total20=($amount20+$markup20)/$currency_rate;
                $inland20 += number_format($total20, 2, '.', '');
              }
              if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                $amount40=$array_amounts['c40'];
                $markup40=$array_markups['m40'];
                $total40=($amount40+$markup40)/$currency_rate;
                $inland40 += number_format($total40, 2, '.', '');
              }
              if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                $amount40hc=$array_amounts['c40hc'];
                $markup40hc=$array_markups['m40hc'];
                $total40hc=($amount40hc+$markup40hc)/$currency_rate;
                $inland40hc += number_format($total40hc, 2, '.', '');
              }
              if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                $amount40nor=$array_amounts['c40nor'];
                $markup40nor=$array_markups['m40nor'];
                $total40nor=($amount40nor+$markup40nor)/$currency_rate;
                $inland40nor += number_format($total40nor, 2, '.', '');
              }
              if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                $amount45=$array_amounts['c45'];
                $markup45=$array_markups['m45'];
                $total45=($amount45+$markup45)/$currency_rate;
                $inland45 += number_format($total45, 2, '.', '');
              }
              $value->total_20=number_format($inland20, 2, '.', '');
              $value->total_40=number_format($inland40, 2, '.', '');
              $value->total_40hc=number_format($inland40hc, 2, '.', '');
              $value->total_40nor=number_format($inland40nor, 2, '.', '');
              $value->total_45=number_format($inland45, 2, '.', '');
            }
          } 
        }
      }
    }    

    $destination_charges = $destination_charges->groupBy([

      function ($item) {
        return $item['carrier']['name'];
      },   
      function ($item) {
        return $item['destination_port']['name'].', '.$item['destination_port']['code'];
      },
      function ($item) {
        return $item['origin_port']['name'];
      },

    ], $preserveKeys = true);

    foreach($destination_charges as $carrier=>$item){
      foreach($item as $destination=>$items){
        foreach($items as $origin=>$itemsDetail){
          foreach ($itemsDetail as $value) {     
            foreach ($value->charge as $amounts) {
              $sum20=0;
              $sum40=0;
              $sum40hc=0;
              $sum40nor=0;
              $sum45=0;
              $total40=0;
              $total20=0;
              $total40hc=0;
              $total40nor=0;
              $total45=0;
              $inland20= 0;
              $inland40= 0;
              $inland40hc= 0;
              $inland40nor= 0;
              $inland45= 0;          
              if($amounts->type_id==2){
                //dd($quote->pdf_option->destination_charges_currency);
                if($quote->pdf_option->grouped_destination_charges==1){
                  $typeCurrency =  $quote->pdf_option->destination_charges_currency;
                }else{
                  $typeCurrency =  $currency_cfg->alphacode;
                }
                $currency_rate=$this->ratesCurrency($amounts->currency_id,$typeCurrency);
                $array_amounts = json_decode($amounts->amount,true);
                $array_markups = json_decode($amounts->markups,true);
                if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                  $sum20=$array_amounts['c20']+$array_markups['m20'];
                  $total20=$sum20/$currency_rate;
                }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                  $sum20=$array_amounts['c20'];
                  $total20=$sum20/$currency_rate;
                }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                  $sum20=$array_markups['m20'];
                  $total20=$sum20/$currency_rate;
                }

                if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                  $sum40=$array_amounts['c40']+$array_markups['m40'];
                  $total40=$sum40/$currency_rate;
                }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                  $sum40=$array_amounts['c40'];
                  $total40=$sum40/$currency_rate;
                }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                  $sum40=$array_markups['m40'];
                  $total40=$sum40/$currency_rate;
                }

                if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                  $sum40hc=$array_amounts['c40hc']+$array_markups['m40hc'];
                  $total40hc=$sum40hc/$currency_rate;
                }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                  $sum40hc=$array_amounts['c40hc'];
                  $total40hc=$sum40hc/$currency_rate;
                }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                  $sum40hc=$array_markups['m40hc'];
                  $total40hc=$sum40hc/$currency_rate;
                }

                if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                  $sum40nor=$array_amounts['c40nor']+$array_markups['m40nor'];
                  $total40nor=$sum40nor/$currency_rate;
                }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                  $sum40nor=$array_amounts['c40nor'];
                  $total40nor=$sum40nor/$currency_rate;
                }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                  $sum40nor=$array_markups['m40nor'];
                  $total40nor=$sum40nor/$currency_rate;
                }

                if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $sum45=$array_amounts['c45']+$array_markups['m45'];
                  $total45=$sum45/$currency_rate;
                }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                  $sum45=$array_amounts['c45'];
                  $total45=$sum45/$currency_rate;
                }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $sum45=$array_markups['m45'];
                  $total45=$sum45/$currency_rate;
                }        

                $amounts->total_20=number_format($total20, 2, '.', '');
                $amounts->total_40=number_format($total40, 2, '.', '');
                $amounts->total_40hc=number_format($total40hc, 2, '.', '');
                $amounts->total_40nor=number_format($total40nor, 2, '.', '');
                $amounts->total_45=number_format($total45, 2, '.', '');
              }
            }
            if(!$value->inland->isEmpty()){
              foreach($value->inland as $value){
                if($quote->pdf_option->grouped_destination_charges==1){
                  $typeCurrency =  $quote->pdf_option->destination_charges_currency;
                }else{
                  $typeCurrency =  $currency_cfg->alphacode;
                }
                $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
                $array_amounts = json_decode($value->rate,true);
                $array_markups = json_decode($value->markup,true);
                if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                  $amount20=$array_amounts['c20'];
                  $markup20=$array_markups['m20'];
                  $total20=($amount20+$markup20)/$currency_rate;
                  $inland20 = number_format($total20, 2, '.', '');
                }
                if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                  $amount40=$array_amounts['c40'];
                  $markup40=$array_markups['m40'];
                  $total40=($amount40+$markup40)/$currency_rate;
                  $inland40 = number_format($total40, 2, '.', '');
                }
                if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                  $amount40hc=$array_amounts['c40hc'];
                  $markup40hc=$array_markups['m40hc'];
                  $total40hc=($amount40hc+$markup40hc)/$currency_rate;
                  $inland40hc = number_format($total40hc, 2, '.', '');
                }
                if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                  $amount40nor=$array_amounts['c40nor'];
                  $markup40nor=$array_markups['m40nor'];
                  $total40nor=($amount40nor+$markup40nor)/$currency_rate;
                  $inland40nor = number_format($total40nor, 2, '.', '');
                }
                if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $amount45=$array_amounts['c45'];
                  $markup45=$array_markups['m45'];
                  $total45=($amount45+$markup45)/$currency_rate;
                  $inland45 = number_format($total45, 2, '.', '');
                }
                $value->total_20=number_format($inland20, 2, '.', '');
                $value->total_40=number_format($inland40, 2, '.', '');
                $value->total_40hc=number_format($inland40hc, 2, '.', '');
                $value->total_40nor=number_format($inland40nor, 2, '.', '');
                $value->total_45=number_format($inland45, 2, '.', '');
              }
            } 
          }
        } 
      }
    }
    //dd(json_encode($destination_charges));

    /**** FREIGHT CHARGES ****/

    /*** Detailed ***/

    $freight_charges_detailed = collect($freight_charges);

    $freight_charges_detailed = $freight_charges_detailed->groupBy([   
      function ($item) {
        return $item['origin_port']['name'].', '.$item['origin_port']['code'];
      },
      function ($item) {
        return $item['destination_port']['name'].', '.$item['destination_port']['code'];
      },
      function ($item) {
        return $item['carrier']['name'];
      },      
    ], $preserveKeys = true);

    foreach($freight_charges_detailed as $origin=>$item){
      foreach($item as $destination=>$items){
        foreach($items as $carrier=>$itemsDetail){
          foreach ($itemsDetail as $value) {     
            foreach ($value->charge as $amounts) {
              if($amounts->type_id==3){
                $sum_freight_20=0;
                $sum_freight_40=0;
                $sum_freight_40hc=0;
                $sum_freight_40nor=0;
                $sum_freight_45=0;
                $total_freight_40=0;
                $total_freight_20=0;
                $total_freight_40hc=0;
                $total_freight_40nor=0;
                $total_freight_45=0;
                //dd($quote->pdf_option->destination_charges_currency);
                if($quote->pdf_option->grouped_freight_charges==1){
                  $typeCurrency =  $quote->pdf_option->freight_charges_currency;
                }else{
                  $typeCurrency =  $currency_cfg->alphacode;
                }
                $currency_rate=$this->ratesCurrency($amounts->currency_id,$typeCurrency);
                $array_amounts = json_decode($amounts->amount,true);
                $array_markups = json_decode($amounts->markups,true);
                if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                  $sum_freight_20=$array_amounts['c20']+$array_markups['m20'];
                  $total_freight_20=$sum_freight_20/$currency_rate;
                }
                if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                  $sum_freight_40=$array_amounts['c40']+$array_markups['m40'];
                  $total_freight_40=$sum_freight_40/$currency_rate;
                }
                if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                  $sum_freight_40hc=$array_amounts['c40hc']+$array_markups['m40hc'];
                  $total_freight_40hc=$sum_freight_40hc/$currency_rate;
                }
                if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                  $sum_freight_40nor=$array_amounts['c40nor']+$array_markups['m40nor'];
                  $total_freight_40nor=$sum_freight_40nor/$currency_rate;
                }
                if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $sum_freight_45=$array_amounts['c45']+$array_markups['m45'];
                  $total_freight_45=$sum_freight_45/$currency_rate;
                }            

                $amounts->total_20 = number_format($total_freight_20, 2, '.', '');
                $amounts->total_40 = number_format($total_freight_40, 2, '.', '');
                $amounts->total_40hc = number_format($total_freight_40hc, 2, '.', '');
                $amounts->total_40nor = number_format($total_freight_40nor, 2, '.', '');
                $amounts->total_45 = number_format($total_freight_45, 2, '.', '');
              }
            }
          }
        } 
      }
    }

    //dd($freight_charges_detailed);

    /*** Grouped in ***/

    $freight_charges_grouped = collect($freight_charges);

    $freight_charges_grouped = $freight_charges_grouped->groupBy([

      function ($item) {
        return $item['origin_port']['name'].', '.$item['origin_port']['code'];
      },
      function ($item) {
        return $item['destination_port']['name'].', '.$item['destination_port']['code'];
      },
      function ($item) {
        return $item['carrier']['name'];
      },

    ], $preserveKeys = true);

    foreach($freight_charges_grouped as $freight){
      foreach($freight as $detail){
        foreach($detail as $item){
          $total_rate20=0;
          $total_rate40=0;
          $total_rate40hc=0;
          $total_rate40nor=0;
          $total_rate45=0;

          $total_rate_markup20=0;
          $total_rate_markup40=0;
          $total_rate_markup40hc=0;
          $total_rate_markup40nor=0;
          $total_rate_markup45=0;

          foreach($item as $rate){
            $sum20=0;
            $sum40=0;
            $sum40hc=0;
            $sum40nor=0;
            $sum45=0;
            $total40=0;
            $total20=0;
            $total40hc=0;
            $total40nor=0;
            $total45=0;

            if($quote->pdf_option->grouped_freight_charges==1){
              $typeCurrency =  $quote->pdf_option->freight_charges_currency;
            }else{
              $typeCurrency =  $currency_cfg->alphacode;
            }

            $currency_rate=$this->ratesCurrency($rate->currency_id,$typeCurrency);

            $array_rate_amounts = json_decode($rate->rates,true);
            //$array_rate_amounts = json_decode($array_rate_amounts,true);
            $array_rate_markups = json_decode($rate->markups,true);

            $currency = Currency::find($rate->currency_id);
            $item->currency_usd = $currency->rates;
            $item->currency_eur = $currency->rates_eur;      

            if(isset($array_rate_amounts['c20'])){
              $amount_rate20=$array_rate_amounts['c20'];
              $total_rate20=$amount_rate20/$currency_rate;
            }

            if(isset($array_rate_markups['m20'])){
              $markup_rate20=$array_rate_markups['m20'];
              $total_rate_markup20=$markup_rate20/$currency_rate;
            }

            if(isset($array_rate_amounts['c40'])){
              $amount_rate40=$array_rate_amounts['c40'];
              $total_rate40=$amount_rate40/$currency_rate;
            }

            if(isset($array_rate_markups['m40'])){
              $markup_rate40=$array_rate_markups['m40'];
              $total_rate_markup40=$markup_rate40/$currency_rate;
            }

            if(isset($array_rate_amounts['c40hc'])){
              $amount_rate40hc=$array_rate_amounts['c40hc'];
              $total_rate40hc=$amount_rate40hc/$currency_rate;
            }
            if(isset($array_rate_markups['m40hc'])){
              $markup_rate40hc=$array_rate_markups['m40hc'];
              $total_rate_markup40hc=$markup_rate40hc/$currency_rate;
            }

            if(isset($array_rate_amounts['c40nor'])){
              $amount_rate40nor=$array_rate_amounts['c40nor'];
              $total_rate40nor=$amount_rate40nor/$currency_rate;
            }
            if(isset($array_rate_markups['m40nor'])){
              $markup_rate40nor=$array_rate_markups['m40nor'];
              $total_rate_markup40nor=$markup_rate40nor/$currency_rate;
            }

            if(isset($array_rate_amounts['c45'])){
              $amount_rate45=$array_rate_amounts['c45'];
              $total_rate45=$amount_rate45/$currency_rate;
            }
            if(isset($array_rate_markups['m45'])){
              $markup_rate45=$array_rate_markups['m45'];
              $total_rate_markup45=$markup_rate45/$currency_rate;
            } 

            $rate->total_rate20=number_format($total_rate20+$total_rate_markup20, 2, '.', '');
            $rate->total_rate40=number_format($total_rate40+$total_rate_markup40, 2, '.', '');
            $rate->total_rate40hc=number_format($total_rate40hc+$total_rate_markup40hc, 2, '.', '');
            $rate->total_rate40nor=number_format($total_rate40nor+$total_rate_markup40nor, 2, '.', '');
            $rate->total_rate45=number_format($total_rate45+$total_rate_markup45, 2, '.', '');

            $rate->total_rate_a20=number_format($total_rate20, 2, '.', '');
            $rate->total_rate_a40=number_format($total_rate40, 2, '.', '');
            $rate->total_rate_a40hc=number_format($total_rate40hc, 2, '.', '');
            $rate->total_rate_a40nor=number_format($total_rate40nor, 2, '.', '');
            $rate->total_rate_a45=number_format($total_rate45, 2, '.', '');

            $rate->total_rate_m20=number_format($total_rate_markup20, 2, '.', '');
            $rate->total_rate_m40=number_format($total_rate_markup40, 2, '.', '');
            $rate->total_rate_m40hc=number_format($total_rate_markup40hc, 2, '.', '');
            $rate->total_rate_m40nor=number_format($total_rate_markup40nor, 2, '.', '');
            $rate->total_rate_m45=number_format($total_rate_markup45, 2, '.', '');             

            foreach ($rate->charge as $amounts) {
              if($amounts->type_id==3){
                if($quote->pdf_option->grouped_freight_charges==1){
                  $typeCurrency =  $quote->pdf_option->freight_charges_currency;
                }else{
                  $typeCurrency =  $currency_cfg->alphacode;
                }
                $currency_rate=$this->ratesCurrency($amounts->currency_id,$typeCurrency);
                $array_amounts = json_decode($amounts->amount,true);
                $array_markups = json_decode($amounts->markups,true);
                if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                  $sum20=$array_amounts['c20']+$array_markups['m20'];
                  $total20=$sum20/$currency_rate;
                }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                  $sum20=$array_amounts['c20'];
                  $total20=$sum20/$currency_rate;
                }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                  $sum20=$array_markups['m20'];
                  $total20=$sum20/$currency_rate;
                }

                if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                  $sum40=$array_amounts['c40']+$array_markups['m40'];
                  $total40=$sum40/$currency_rate;
                }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                  $sum40=$array_amounts['c40'];
                  $total40=$sum40/$currency_rate;
                }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                  $sum40=$array_markups['m40'];
                  $total40=$sum40/$currency_rate;
                }

                if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                  $sum40hc=$array_amounts['c40hc']+$array_markups['m40hc'];
                  $total40hc=$sum40hc/$currency_rate;
                }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                  $sum40hc=$array_amounts['c40hc'];
                  $total40hc=$sum40hc/$currency_rate;
                }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                  $sum40hc=$array_markups['m40hc'];
                  $total40hc=$sum40hc/$currency_rate;
                }

                if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                  $sum40nor=$array_amounts['c40nor']+$array_markups['m40nor'];
                  $total40nor=$sum40nor/$currency_rate;
                }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                  $sum40nor=$array_amounts['c40nor'];
                  $total40nor=$sum40nor/$currency_rate;
                }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                  $sum40nor=$array_markups['m40nor'];
                  $total40nor=$sum40nor/$currency_rate;
                }

                if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $sum45=$array_amounts['c45']+$array_markups['m45'];
                  $total45=$sum45/$currency_rate;
                }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                  $sum45=$array_amounts['c45'];
                  $total45=$sum45/$currency_rate;
                }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $sum45=$array_markups['m45'];
                  $total45=$sum45/$currency_rate;
                }

                $amounts->total_20=number_format($total20, 2, '.', '');
                $amounts->total_40=number_format($total40, 2, '.', '');
                $amounts->total_40hc=number_format($total40hc, 2, '.', '');
                $amounts->total_40nor=number_format($total40nor, 2, '.', '');
                $amounts->total_45=number_format($total45, 2, '.', '');
              }
            }
          }
        }
      }
    }

    //$origin_charges=$origin_charges->toArray();
    //dd(json_encode($freight_charges_grouped));
    $view = \View::make('quotesv2.pdf.index', ['quote'=>$quote,'rates'=>$rates,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'user'=>$user,'currency_cfg'=>$currency_cfg,'charges_type'=>$type,'equipmentHides'=>$equipmentHides,'freight_charges_grouped'=>$freight_charges_grouped,'destination_charges'=>$destination_charges,'origin_charges_grouped'=>$origin_charges_grouped,'origin_charges_detailed'=>$origin_charges_detailed,'destination_charges_grouped'=>$destination_charges_grouped,'freight_charges_detailed'=>$freight_charges_detailed]);

    $pdf = \App::make('dompdf.wrapper');
    $pdf->loadHTML($view)->save('pdf/temp_'.$quote->id.'.pdf');

    return $pdf->stream('quote');
  }

  //Delete rates

  public function delete($id){
    AutomaticRate::where('id',$id)->delete();
    return response()->json(['message' => 'Ok']);
  }

  //Delete charge

  public function deleteCharge($id){
    Charge::where('id',$id)->delete();
    return response()->json(['message' => 'Ok']);
  }

  //Delete charge lcl air

  public function deleteChargeLclAir($id){
    ChargeLclAir::where('id',$id)->delete();
    return response()->json(['message' => 'Ok']);
  }

  // Store

  public function storeCharge(Request $request){

    $array_amount_20 = array();
    $array_markup_20 = array();
    $array_amount_40 = array();
    $array_markup_40 = array();
    $array_amount_40hc = array();
    $array_markup_40hc = array();
    $array_amount_40nor = array();
    $array_markup_40nor = array();
    $array_amount_45 = array();
    $array_markup_45 = array();
    $merge_amounts = array();
    $merge_markups = array();
    if($request->amount_c20){
      $array_amount_20 = array('c20' => $request->amount_c20);
    }
    if($request->markup_m20){
      $array_markup_20 = array('m20' => $request->markup_m20);
    }
    if($request->amount_c40){
      $array_amount_40 = array('c40' => $request->amount_c40);
    }
    if($request->markup_m40){
      $array_markup_40 = array('m40' => $request->markup_m40);
    }
    if($request->amount_c40hc){
      $array_amount_40hc = array('c40hc' => $request->amount_c40hc);
    }
    if($request->markup_m40hc){
      $array_markup_40hc = array('m40hc' => $request->markup_m40hc);
    }
    if($request->amount_c40nor){
      $array_amount_40nor = array('c40nor' => $request->amount_c40nor);
    }
    if($request->markup_m40nor){
      $array_markup_40nor = array('m40nor' => $request->markup_m40nor);
    }
    if($request->amount_c45){
      $array_amount_45 = array('c45' => $request->amount_c45);
    }
    if($request->markup_m45){
      $array_markup_45 = array('m45' => $request->markup_m45);
    }
    $merge_amounts = array_merge($array_amount_20,$array_amount_40,$array_amount_40hc,$array_amount_40nor,$array_amount_45);
    $merge_markups = array_merge($array_markup_20,$array_markup_40,$array_markup_40hc,$array_markup_40nor,$array_markup_45);

    $charge = new Charge();
    $charge->automatic_rate_id=$request->automatic_rate_id;
    $charge->type_id=$request->type_id;
    $charge->surcharge_id=$request->surcharge_id;
    $charge->calculation_type_id=$request->calculation_type_id;
    $charge->amount=json_encode($merge_amounts);
    $charge->markups=json_encode($merge_markups);
    $charge->currency_id=$request->currency_id;
    $charge->save();

    return response()->json(['message' => 'Ok']);

  }

  public function storeChargeLclAir(Request $request){

    $charge = new ChargeLclAir();
    $charge->automatic_rate_id=$request->automatic_rate_id;
    $charge->type_id=$request->type_id;
    $charge->surcharge_id=$request->surcharge_id;
    $charge->calculation_type_id=$request->calculation_type_id;
    $charge->units=$request->units;
    $charge->price_per_unit=$request->price_per_unit;
    $charge->total=$request->total;
    $charge->markup=$request->markup;
    $charge->currency_id=$request->currency_id;
    $charge->save();

    return response()->json(['message' => 'Ok']);

  }
  public function getCompanyPayments($id)
  {
    $payments = Company::find($id);
    return $payments->payment_conditions;
  }

  public function store(Request $request){


    if(!empty($request->input('form'))){
      $form =  json_decode($request->input('form'));
      $info = $request->input('info');
      $equipment =  stripslashes(json_encode($form->equipment ));
      $dateQ = explode('/',$form->date);
      $since = $dateQ[0];
      $until = $dateQ[1];
      $priceId = null;
      if(isset($form->price_id )){
        $priceId = $form->price_id;
        if($priceId=="0"){
          $priceId = null;
        }
      }

      $payments = $this->getCompanyPayments($form->company_id_quote);

      $request->request->add(['company_user_id' => \Auth::user()->company_user_id ,'quote_id'=>$this->idPersonalizado(),'type'=>'FCL','delivery_type'=>$form->delivery_type,'company_id'=>$form->company_id_quote,'contact_id' => $form->contact_id ,'validity_start'=>$since,'validity_end'=>$until,'user_id'=>\Auth::id(), 'equipment'=>$equipment  , 'status'=>'Draft' ,'incoterm_id' =>$form->incoterm_id  ,'date_issued'=>$since ,'price_id' => $priceId ,'payment_conditions' => $payments]);
      $quote= QuoteV2::create($request->all());



      $company = User::where('id',\Auth::id())->with('companyUser.currency')->first();
      $currency_id = $company->companyUser->currency_id;
      $currency = Currency::find($currency_id);

      $pdf_option = new PdfOption();
      $pdf_option->quote_id=$quote->id;
      $pdf_option->show_type='total in';
      $pdf_option->grouped_total_currency=0;
      $pdf_option->total_in_currency=$currency->alphacode;
      $pdf_option->freight_charges_currency=$currency->alphacode;
      $pdf_option->origin_charges_currency=$currency->alphacode;
      $pdf_option->destination_charges_currency=$currency->alphacode;
      $pdf_option->language='English';
      $pdf_option->save();

    }else{// COTIZACION MANUAL

      $dateQ = explode('/',$request->input('date'));
      $since = $dateQ[0];
      $until = $dateQ[1];
      $company = User::where('id',\Auth::id())->with('companyUser.currency')->first();

      $idCurrency = $company->companyUser->currency_id;
      $currency = Currency::find($idCurrency);

      $arregloNull = array();
      $arregloNull = json_encode($arregloNull);


      if($request->input('type') == '1'){
        $typeText = "FCL";
        $equipment =  stripslashes(json_encode($request->input('equipment')));
        $delivery_type = $request->input('delivery_type') ;


      }
      if($request->input('type') == '2'){
        $typeText = "LCL";
        $equipment =  $arregloNull;
        $delivery_type = $request->input('delivery_type') ;
      }
      if($request->input('type') == '3'){
        $typeText = "AIR";
        $equipment =  $arregloNull;
        $delivery_type = $request->input('delivery_type_air') ;

      }

      $payments = $this->getCompanyPayments($request->input('company_id_quote'));

      $request->request->add(['company_user_id' => \Auth::user()->company_user_id ,'quote_id'=>$this->idPersonalizado(),'type'=> $typeText,'delivery_type'=>$delivery_type,'company_id'=>$request->input('company_id_quote'),'contact_id' =>$request->input('contact_id') ,'validity_start'=>$since,'validity_end'=>$until,'user_id'=>\Auth::id(), 'equipment'=>$equipment  , 'status'=>'Draft' , 'date_issued'=>$since ,'payment_conditions' => $payments ]);
      $quote= QuoteV2::create($request->all());

      // FCL
      if($typeText == 'FCL' || $typeText == 'LCL'){
        foreach($request->input('originport') as $origP){
          $infoOrig = explode("-", $origP);
          $origin_port[] = $infoOrig[0];
        }
        foreach($request->input('destinyport') as $destP){
          $infoDest = explode("-", $destP);
          $destiny_port[] = $infoDest[0];
        }
        foreach($origin_port as $orig){
          foreach($destiny_port as $dest){
            $request->request->add(['contract' => '' ,'origin_port_id'=> $orig,'destination_port_id'=>$dest,'carrier_id'=>$request->input('carrieManual')  ,'currency_id'=>  $idCurrency ,'quote_id'=>$quote->id]);
            $rate = AutomaticRate::create($request->all());

            $oceanFreight = new Charge();
            $oceanFreight->automatic_rate_id= $rate->id;
            $oceanFreight->type_id = '3' ;
            $oceanFreight->surcharge_id = null ;
            $oceanFreight->calculation_type_id = '5' ;
            $oceanFreight->amount = $arregloNull;
            $oceanFreight->markups = $arregloNull;
            $oceanFreight->currency_id = $idCurrency;
            $oceanFreight->total =  $arregloNull;
            $oceanFreight->save();

          }
        }
      }
      if($typeText == 'AIR' ){

        $request->request->add(['contract' => '' ,'origin_airport_id'=> $request->input('origin_airport_id'),'destination_airport_id'=> $request->input('destination_airport_id'),'airline_id'=>$request->input('airline_id') ,'currency_id'=>  $idCurrency ,'quote_id'=>$quote->id]);
        $rate = AutomaticRate::create($request->all());


        $oceanFreight = new Charge();
        $oceanFreight->automatic_rate_id= $rate->id;
        $oceanFreight->type_id = '3' ;
        $oceanFreight->surcharge_id = null ;
        $oceanFreight->calculation_type_id = '5' ;
        $oceanFreight->amount = $arregloNull;
        $oceanFreight->markups = $arregloNull;
        $oceanFreight->currency_id = $idCurrency;
        $oceanFreight->total =  $arregloNull;
        $oceanFreight->save();



      }
      //LCL        $input = Input::all();

      if($typeText == 'LCL' || $typeText == 'AIR' ){
        $input = Input::all();
        $quantity = array_values( array_filter($input['quantity']) );
        //dd($input);
        $type_cargo = array_values( array_filter($input['type_load_cargo']) );
        $height = array_values( array_filter($input['height']) );
        $width = array_values( array_filter($input['width']) );
        $large = array_values( array_filter($input['large']) );
        $weight = array_values( array_filter($input['weight']) );
        $volume = array_values( array_filter($input['volume']) );
        if(count($quantity)>0){
          foreach($type_cargo as $key=>$item){
            $package_load = new PackageLoadV2();
            $package_load->quote_id = $quote->id;
            $package_load->type_cargo = $type_cargo[$key];
            $package_load->quantity = $quantity[$key];
            $package_load->height = $height[$key];
            $package_load->width = $width[$key];
            $package_load->large = $large[$key];
            $package_load->weight = $weight[$key];
            $package_load->total_weight = $weight[$key]*$quantity[$key];
            if(!empty($volume)){
              $package_load->volume = $volume[$key];
            }else{
              $package_load->volume = 0.01;
            }

            $package_load->save();
          }
        }
      }


      $pdf_option = new PdfOption();
      $pdf_option->quote_id=$quote->id;
      $pdf_option->show_type='total in';
      $pdf_option->grouped_total_currency=0;
      $pdf_option->total_in_currency=$currency->alphacode;
      $pdf_option->freight_charges_currency=$currency->alphacode;
      $pdf_option->origin_charges_currency=$currency->alphacode;
      $pdf_option->destination_charges_currency=$currency->alphacode;
      $pdf_option->language='English';
      $pdf_option->save();
      // MANUAL RATE
    }

    //AUTOMATIC QUOTE
    if(!empty($info)){
      foreach($info as $infoA){
        $info_D = json_decode($infoA);

        // Rates
        foreach($info_D->rates as $rateO){

          $rates =   json_encode($rateO->rate);
          $markups =   json_encode($rateO->markups);
          $arregloNull = array();

          $request->request->add(['contract' => $info_D->contract->id ,'origin_port_id'=> $info_D->port_origin->id,'destination_port_id'=>$info_D->port_destiny->id ,'carrier_id'=>$info_D->carrier->id ,'currency_id'=>  $info_D->currency->id ,'quote_id'=>$quote->id]);

          $rate = AutomaticRate::create($request->all());


          $oceanFreight = new Charge();
          $oceanFreight->automatic_rate_id= $rate->id;
          $oceanFreight->type_id = '3' ;
          $oceanFreight->surcharge_id = null ;
          $oceanFreight->calculation_type_id = '5' ;
          $oceanFreight->amount = $rates;
          $oceanFreight->markups = $markups;
          $oceanFreight->currency_id = $info_D->currency->id;
          $oceanFreight->total =  $rates;
          $oceanFreight->save();

          $inlandD =  $request->input('inlandD'.$rateO->rate_id);
          $inlandO =  $request->input('inlandO'.$rateO->rate_id);
          //INLAND DESTINO
          if(!empty($inlandD)){

            foreach( $inlandD as $inlandDestiny){

              $inlandDestiny = json_decode($inlandDestiny);

              $arregloMontoInDest = array();
              $arregloMarkupsInDest = array();
              $montoInDest = array();
              $markupInDest = array();
              foreach($inlandDestiny->inlandDetails as $key => $inlandDetails){

                if($inlandDetails->amount != 0){
                  $arregloMontoInDest = array($key => $inlandDetails->amount);
                  $montoInDest = array_merge($arregloMontoInDest,$montoInDest);  
                }
                if($inlandDetails->markup != 0){
                  $arregloMarkupsInDest = array($key => $inlandDetails->markup);
                  $markupInDest = array_merge($arregloMarkupsInDest,$markupInDest);
                }

              }

              $arregloMontoInDest =  json_encode($montoInDest);
              $arregloMarkupsInDest =  json_encode($markupInDest);
              $inlandDest = new AutomaticInland();
              $inlandDest->quote_id= $quote->id;
              $inlandDest->automatic_rate_id = $rate->id;
              $inlandDest->provider =  $inlandDestiny->providerName;
              $inlandDest->distance =  $inlandDestiny->km;
              $inlandDest->contract = $info_D->contract->id;
              $inlandDest->port_id = $inlandDestiny->port_id;
              $inlandDest->type = $inlandDestiny->type;
              $inlandDest->rate = $arregloMontoInDest;
              $inlandDest->markup = $arregloMarkupsInDest;
              $inlandDest->validity_start =$inlandDestiny->validity_start ;
              $inlandDest->validity_end=$inlandDestiny->validity_end ;
              $inlandDest->currency_id =  $info_D->currency->id;
              $inlandDest->save();

            }  
          }
          //INLAND ORIGEN 

          if(!empty($inlandO)){

            foreach( $inlandO as $inlandOrigin){

              $inlandOrigin = json_decode($inlandOrigin);

              $arregloMontoInOrig = array();
              $arregloMarkupsInOrig = array();
              $montoInOrig = array();
              $markupInOrig = array();
              foreach($inlandOrigin->inlandDetails as $key => $inlandDetails){

                if($inlandDetails->amount != 0){
                  $arregloMontoInOrig = array($key => $inlandDetails->amount);
                  $montoInOrig = array_merge($arregloMontoInOrig,$montoInOrig);  
                }
                if($inlandDetails->markup != 0){
                  $arregloMarkupsInOrig = array($key => $inlandDetails->markup);
                  $markupInOrig = array_merge($arregloMarkupsInOrig,$markupInOrig);
                }

              }

              $arregloMontoInOrig =  json_encode($montoInOrig);
              $arregloMarkupsInOrig =  json_encode($markupInOrig);
              $inlandOrig = new AutomaticInland();
              $inlandOrig->quote_id= $quote->id;
              $inlandOrig->automatic_rate_id = $rate->id;
              $inlandOrig->provider =  $inlandOrigin->providerName;
              $inlandOrig->distance =  $inlandOrigin->km;
              $inlandOrig->contract = $info_D->contract->id;
              $inlandOrig->port_id = $inlandOrigin->port_id;
              $inlandOrig->type = $inlandOrigin->type;
              $inlandOrig->rate = $arregloMontoInOrig;
              $inlandOrig->markup = $arregloMarkupsInOrig;
              $inlandOrig->validity_start =$inlandOrigin->validity_start ;
              $inlandOrig->validity_end=$inlandOrigin->validity_end ;
              $inlandOrig->currency_id =  $info_D->currency->id;
              $inlandOrig->save();

            }  
          }



        }
        //CHARGES ORIGIN
        foreach($info_D->localorigin as $localorigin){
          $arregloMontoO = array();
          $arregloMarkupsO = array();
          $montoO = array();
          $markupO = array();
          foreach($localorigin as $localO){
            foreach($localO as $local){
              if($local->type != '99'){
                $arregloMontoO = array('c'.$local->type => $local->monto);
                $montoO = array_merge($arregloMontoO,$montoO);
                $arregloMarkupsO = array('m'.$local->type => $local->markup);
                $markupO = array_merge($arregloMarkupsO,$markupO);
              }
              if($local->type == '99'){
                $arregloO = array('type_id' => '1' , 'surcharge_id' => $local->surcharge_id , 'calculation_type_id' => $local->calculation_id, 'currency_id' => $local->currency_id);
              }
            }
          }

          $arregloMontoO =  json_encode($montoO);
          $arregloMarkupsO =  json_encode($markupO);

          $chargeOrigin = new Charge();
          $chargeOrigin->automatic_rate_id= $rate->id;
          $chargeOrigin->type_id = $arregloO['type_id'] ;
          $chargeOrigin->surcharge_id = $arregloO['surcharge_id']  ;
          $chargeOrigin->calculation_type_id = $arregloO['calculation_type_id']  ;
          $chargeOrigin->amount =  $arregloMontoO  ;
          $chargeOrigin->markups = $arregloMarkupsO  ;
          $chargeOrigin->currency_id = $arregloO['currency_id']  ;
          $chargeOrigin->total =  $arregloMarkupsO ;
          $chargeOrigin->save();
        }

        // CHARGES DESTINY 
        foreach($info_D->localdestiny as $localdestiny){
          $arregloMontoD = array();
          $arregloMarkupsD = array();
          $montoD = array();
          $markupD = array();
          foreach($localdestiny as $localD){
            foreach($localD as $local){
              if($local->type != '99'){

                $arregloMontoD = array('c'.$local->type => $local->monto);
                $montoD = array_merge($arregloMontoD,$montoD);
                $arregloMarkupsD = array('m'.$local->type => $local->markup);
                $markupD = array_merge($arregloMarkupsD,$markupD);
              }
              if($local->type == '99'){
                $arregloD = array('type_id' => '2' , 'surcharge_id' => $local->surcharge_id , 'calculation_type_id' => $local->calculation_id, 'currency_id' => $local->currency_id );
              }
            }
          }

          $arregloMontoD =  json_encode($montoD);
          $arregloMarkupsD =  json_encode($markupD);

          $chargeDestiny = new Charge();
          $chargeDestiny->automatic_rate_id= $rate->id;
          $chargeDestiny->type_id = $arregloD['type_id'] ;
          $chargeDestiny->surcharge_id = $arregloD['surcharge_id']  ;
          $chargeDestiny->calculation_type_id = $arregloD['calculation_type_id']  ;
          $chargeDestiny->amount =  $arregloMontoD;
          $chargeDestiny->markups = $arregloMarkupsD;
          $chargeDestiny->currency_id = $arregloD['currency_id']  ;
          $chargeDestiny->total =  $arregloMarkupsD;
          $chargeDestiny->save();
        }

        // CHARGES FREIGHT 
        foreach($info_D->localfreight as $localfreight){
          $arregloMontoF = array();
          $arregloMarkupsF = array();
          $montoF = array();
          $markupF = array();
          foreach($localfreight as $localF){
            foreach($localF as $local){
              if($local->type != '99'){
                $arregloMontoF = array('c'.$local->type => $local->monto);
                $montoF = array_merge($arregloMontoF,$montoF);
                $arregloMarkupsF = array('m'.$local->type => $local->markup);
                $markupF = array_merge($arregloMarkupsF,$markupF);
              }
              if($local->type == '99'){
                $arregloF = array('type_id' => '3' , 'surcharge_id' => $local->surcharge_id , 'calculation_type_id' => $local->calculation_id , 'currency_id' => $local->currency_id );
              }
            }
          }
          $arregloMontoF =  json_encode($montoF);
          $arregloMarkupsF =  json_encode($markupF);

          $chargeFreight = new Charge();
          $chargeFreight->automatic_rate_id= $rate->id;
          $chargeFreight->type_id = $arregloF['type_id'] ;
          $chargeFreight->surcharge_id = $arregloF['surcharge_id']  ;
          $chargeFreight->calculation_type_id = $arregloF['calculation_type_id']  ;
          $chargeFreight->amount =  $arregloMontoF;
          $chargeFreight->markups = $arregloMarkupsF;
          $chargeFreight->currency_id = $arregloF['currency_id']  ;
          $chargeFreight->total =  $arregloMarkupsF;
          $chargeFreight->save();
        }


        /*
        $terms = new TermsAndCondition();
        $terms->quote_id= $quote->id;
        $terms->content =$info_D->terms;

        $terms->save();*/

      }  
    }

    //$request->session()->flash('message.nivel', 'success');
    //$request->session()->flash('message.title', 'Well done!');
    //$request->session()->flash('message.content', 'Register completed successfully!');
    //return redirect()->route('quotes.index');
    return redirect()->action('QuoteV2Controller@show', setearRouteKey($quote->id));
  }

  public function storeRates(Request $request){

    $arregloNull = array();
    $arregloNull = json_encode($arregloNull);
    $quote = QuoteV2::find($request->input('quote_id'));
    $company = User::where('id',\Auth::id())->with('companyUser.currency')->first();
    $idCurrency = $company->companyUser->currency_id;
    $dateQ = explode('/',$request->input('date'));
    $since = $dateQ[0];
    $until = $dateQ[1];

    // FCL & LCL
    if($quote->type == 'FCL' || $quote->type == 'LCL'){
      foreach($request->input('originport') as $origP){
        $infoOrig = explode("-", $origP);
        $origin_port[] = $infoOrig[0];
      }
      foreach($request->input('destinyport') as $destP){
        $infoDest = explode("-", $destP);
        $destiny_port[] = $infoDest[0];
      }
      foreach($origin_port as $orig){
        foreach($destiny_port as $dest){
          $request->request->add(['contract' => '' ,'origin_port_id'=> $orig,'destination_port_id'=>$dest,'carrier_id'=>$request->input('carrieManual')  ,'rates'=> $arregloNull ,'validity_start'=>$since,'validity_end'=>$until,'markups'=> $arregloNull ,'currency_id'=>  $idCurrency ,'total' => $arregloNull,'quote_id'=>$quote->id]);
          $rate = AutomaticRate::create($request->all());
        }
      }
    }else if($quote->type == 'AIR' ){
      $request->request->add(['contract' => '' ,'origin_airport_id'=> $request->input('origin_airport_id'),'destination_airport_id'=> $request->input('destination_airport_id'),'airline_id'=>$request->input('airline_id')  ,'rates'=> $arregloNull ,'markups'=> $arregloNull ,'validity_start'=>$since,'validity_end'=>$until,'currency_id'=>  $idCurrency ,'total' => $arregloNull,'quote_id'=>$quote->id]);
      $rate = AutomaticRate::create($request->all());
    }

    return redirect()->action('QuoteV2Controller@show', setearRouteKey($quote->id));
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
  public function search()
  {
    $company_user_id=\Auth::user()->company_user_id;
    $incoterm = Incoterm::pluck('name','id');
    $incoterm->prepend('Select at option','');
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
    $carrierMan = Carrier::all()->pluck('name','id');
    $airlines = Airline::all()->pluck('name','id');

    $company_user = User::where('id',\Auth::id())->first();
    if(count($company_user->companyUser)>0) {
      $currency_name = Currency::where('id', $company_user->companyUser->currency_id)->first();
    }else{
      $currency_name = '';
    }
    $currencies = Currency::all()->pluck('alphacode','id');
    $hideO = 'hide';
    $hideD = 'hide';
    return view('quotesv2/search',  compact('companies','carrierMan','hideO','hideD','countries','harbors','prices','company_user','currencies','currency_name','incoterm','airlines'));


  }

  public function processSearch(Request $request){


    //Variables del usuario conectado
    $company_user_id=\Auth::user()->company_user_id;
    $user_id =  \Auth::id();

    //Variables para cargar el  Formulario
    $form  = $request->all();
    $incoterm = Incoterm::pluck('name','id');
    if(\Auth::user()->hasRole('subuser')){
      $companies = Company::where('company_user_id','=',$company_user_id)->whereHas('groupUserCompanies', function($q)  {
        $q->where('user_id',\Auth::user()->id);
      })->orwhere('owner',\Auth::user()->id)->pluck('business_name','id');
    }else{
      $companies = Company::where('company_user_id','=',$company_user_id)->pluck('business_name','id');
    }
    $airlines = Airline::all()->pluck('name','id');
    $harbors = Harbor::get()->pluck('display_name','id_complete');
    $countries = Country::all()->pluck('name','id');
    $prices = Price::all()->pluck('name','id');
    $company_user = User::where('id',\Auth::id())->first();
    $carrierMan = Carrier::all()->pluck('name','id');

    if(count($company_user->companyUser)>0) {
      $currency_name = Currency::where('id', $company_user->companyUser->currency_id)->first();
    }else{
      $currency_name = '';
    }
    $currencies = Currency::all()->pluck('alphacode','id');


    //Settings de la compañia 
    $company = User::where('id',\Auth::id())->with('companyUser.currency')->first();
    $typeCurrency =  $company->companyUser->currency->alphacode ;
    $idCurrency = $company->companyUser->currency_id;

    // Request Formulario
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
    $equipment = $request->input('equipment');
    $delivery_type = $request->input('delivery_type');
    $price_id = $request->input('price_id');
    $modality_inland = $request->modality;
    $company_id = $request->input('company_id_quote');
    $mode = $request->mode;
    // Fecha Contrato
    $dateRange =  $request->input('date');
    $dateRange = explode("/",$dateRange);
    $dateSince = $dateRange[0];
    $dateUntil = $dateRange[1];

    //Collection Equipment Dinamico
    $equipmentHides = $this->hideContainer($equipment,'');
    //Colecciones 
    $inlandDestiny = new collection();
    $inlandOrigin = new collection();

    //Markups Freight
    $freighPercentage = 0;
    $freighAmmount = 0;
    $freighMarkup= 0;
    // Markups Local
    $localPercentage = 0;
    $localAmmount = 0;
    $localMarkup = 0;
    $markupLocalCurre = 0;
    // Markups Local
    $inlandPercentage = 0;
    $inlandAmmount = 0;
    $inlandMarkup = 0;
    $markupInlandCurre = 0;
    // Markups
    $fclMarkup = Price::whereHas('company_price', function($q) use($price_id) {
      $q->where('price_id', '=',$price_id);
    })->with('freight_markup','local_markup','inland_markup')->get();

    foreach($fclMarkup as $freight){
      // Freight
      $fclFreight = $freight->freight_markup->where('price_type_id','=',1);
      // Valor de porcentaje
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
    // Fin Markups

    // Calculo de los inlands
    $modality_inland = '1';// FALTA AGREGAR EXPORT
    $company_inland = $request->input('company_id_quote');
    $texto20 = 'Inland 20 x' .$request->input('twuenty'); 
    $texto40 = 'Inland 40 x' .$request->input('forty');
    $texto40hc = 'Inland 40HC x'. $request->input('fortyhc');
    // Destination Address
    $hideO = 'hide';
    $hideD = 'hide';
    if($delivery_type == "2" || $delivery_type == "4" ){ 

      $hideD = '';
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
        $inlandDetails = array();

        foreach($inlandsValue->inlandports as $ports){
          $monto = 0;

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
                  if($details->type == 'twuenty' &&  in_array( '20',$equipment) ){
                    if( $distancia >= $details->lower && $distancia  <= $details->upper){
                      $sub_20 = number_format( $details->ammount / $rateI, 2, '.', ''); 
                      $monto += number_format($sub_20, 2, '.', ''); 
                      $amount_inland = number_format($details->ammount, 2, '.', ''); 
                      $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');
                      $km20 = false;
                      // CALCULO MARKUPS 
                      $markupI20=$this->inlandMarkup($inlandPercentage,$inlandAmmount,$inlandMarkup,$sub_20,$typeCurrency,$markupInlandCurre);
                      // FIN CALCULO MARKUPS 
                      $arrayInland20 = array("cant_cont" =>  '1' , "sub_in" => $sub_20 ,'amount' => $amount_inland ,'currency' => $details->currency->alphacode , 'price_unit' => $price_per_unit , 'typeContent' => 'i20') ; 
                      $arrayInland20 = array_merge($markupI20,$arrayInland20);
                      $inlandDetails[] = $arrayInland20;
                    }
                  }
                  if($details->type == 'forty' &&  in_array( '40',$equipment) ){

                    if( $distancia >= $details->lower && $distancia  <= $details->upper){
                      $sub_40 = number_format( $details->ammount / $rateI, 2, '.', ''); 
                      $monto +=  number_format($sub_40, 2, '.', ''); 
                      $amount_inland = $details->ammount;
                      $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');
                      $km40 = false;
                      // CALCULO MARKUPS 
                      $markupI40=$this->inlandMarkup($inlandPercentage,$inlandAmmount,$inlandMarkup,$sub_40,$typeCurrency,$markupInlandCurre);
                      // FIN CALCULO MARKUPS 
                      $arrayInland40 = array("cant_cont" =>  '1' , "sub_in" => $sub_40 ,'amount' => $amount_inland ,'currency' => $details->currency->alphacode , 'price_unit' => $price_per_unit , 'typeContent' => 'i40') ; 
                      $arrayInland40 = array_merge($markupI40,$arrayInland40);
                      $inlandDetails[] = $arrayInland40;
                    }
                  }
                  if($details->type == 'fortyhc' &&   in_array( '40HC',$equipment) ){
                    if( $distancia >= $details->lower && $distancia  <= $details->upper){
                      $sub_40hc =  number_format( $details->ammount / $rateI, 2, '.', ''); 
                      $monto +=  number_format($sub_40hc, 2, '.', ''); 
                      $price_per_unit = number_format($details->ammount / $distancia, 2, '.', '');
                      $amount_inland =  number_format($details->ammount , 2, '.', ''); 
                      $km40hc = false;

                      // CALCULO MARKUPS 
                      $markupI40hc=$this->inlandMarkup($inlandPercentage,$inlandAmmount,$inlandMarkup,$sub_40hc,$typeCurrency,$markupInlandCurre);
                      // FIN CALCULO MARKUPS 
                      $arrayInland40hc = array("cant_cont" => $request->input('fortyhc') , "sub_in" => $sub_40hc, "des_in" => $texto40hc,'amount' => $amount_inland,'currency' => $details->currency->alphacode , 'price_unit' => $price_per_unit , 'typeContent' => 'i40HC' ) ;
                      $arrayInland40hc = array_merge($markupI40hc,$arrayInland40hc);
                      $inlandDetails[] = $arrayInland40hc;
                    }
                  }

                }
                // KILOMETROS ADICIONALES 

                if(isset($inlandsValue->inlandadditionalkms)){


                  $rateGeneral = $this->ratesCurrency($inlandsValue->inlandadditionalkms->currency_id,$typeCurrency);
                  if($km20 &&  in_array( '20',$equipment) ){
                    $montoKm = ($distancia * $inlandsValue->inlandadditionalkms->km_20) / $rateGeneral;
                    $sub_20 =  number_format($montoKm, 2, '.', '');
                    $monto += $sub_20;
                    $amount_inland = $distancia * $inlandsValue->inlandadditionalkms->km_20;
                    $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');
                    $amount_inland = number_format($amount_inland, 2, '.', '');
                    // CALCULO MARKUPS 
                    $markupI20=$this->inlandMarkup($inlandPercentage,$inlandAmmount,$inlandMarkup,$sub_20,$typeCurrency,$markupInlandCurre);
                    // FIN CALCULO MARKUPS 
                    $sub_20 = number_format($sub_20, 2, '.', '');
                    $arrayInland20 = array("cant_cont" =>'1' , "sub_in" => $sub_20, "des_in" => $texto20 ,'amount' => $amount_inland ,'currency' =>$inlandsValue->inlandadditionalkms->currency->alphacode, 'price_unit' => $price_per_unit , 'typeContent' => 'i20' ) ;
                    $arrayInland20 = array("cant_cont" =>'1' , "sub_in" => $sub_20, "des_in" => $texto20 ,'amount' => $amount_inland ,'currency' =>$inlandsValue->inlandadditionalkms->currency->alphacode, 'price_unit' => $price_per_unit , 'typeContent' => 'i20' ) ;
                    $arrayInland20 = array_merge($markupI20,$arrayInland20);
                    $inlandDetails[] = $arrayInland20;
                  }
                  if($km40 &&  in_array( '40',$equipment) ){
                    $montoKm = ($distancia * $inlandsValue->inlandadditionalkms->km_40) / $rateGeneral;

                    $sub_40 = number_format($montoKm, 2, '.', '');
                    $monto += $sub_40;
                    $amount_inland = $distancia * $inlandsValue->inlandadditionalkms->km_40 ;
                    $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');
                    $amount_inland = number_format($amount_inland, 2, '.', '');
                    // CALCULO MARKUPS 
                    $markupI40=$this->inlandMarkup($inlandPercentage,$inlandAmmount,$inlandMarkup,$sub_40,$typeCurrency,$markupInlandCurre);
                    // FIN CALCULO MARKUPS
                    $sub_40 = number_format($sub_40, 2, '.', '');
                    $arrayInland40 = array("cant_cont" => '1', "sub_in" => $sub_40, "des_in" =>  $texto40,'amount' => $amount_inland ,'currency' => $inlandsValue->inlandadditionalkms->currency->alphacode , 'price_unit' => $price_per_unit, 'typeContent' => 'i40' ) ;
                    $arrayInland40 = array_merge($markupI40,$arrayInland40);
                    $inlandDetails[] = $arrayInland40;

                  }
                  if($km40hc &&  in_array( '40HC',$equipment)){
                    $montoKm = ($distancia * $inlandsValue->inlandadditionalkms->km_40hc) / $rateGeneral;
                    $sub_40hc = number_format($montoKm, 2, '.', '');
                    $monto += $sub_40hc;

                    $amount_inland = $distancia * $inlandsValue->inlandadditionalkms->km_40hc;
                    $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');
                    $amount_inland = number_format($amount_inland, 2, '.', '');
                    // CALCULO MARKUPS 
                    $markupI40hc=$this->inlandMarkup($inlandPercentage,$inlandAmmount,$inlandMarkup,$sub_40hc,$typeCurrency,$markupInlandCurre);
                    // FIN CALCULO MARKUPS
                    $sub_40hc = number_format($sub_40hc, 2, '.', '');
                    $arrayInland40hc = array("cant_cont" =>'1' , "sub_in" => $sub_40hc, "des_in" => $texto40hc,'amount' => $amount_inland ,'currency' => $typeCurrency , 'price_unit' => $price_per_unit , 'typeContent' => 'i40HC') ;
                    $arrayInland40hc = array_merge($markupI40hc,$arrayInland40hc);
                    $inlandDetails[] = $arrayInland40hc;
                  }

                }

                $monto = number_format($monto, 2, '.', '');
                if($monto > 0){
                  $inlandDetails = Collection::make($inlandDetails);
                  $arregloInland =  array("prov_id" => $inlandsValue->id ,"provider" => "Inland Haulage","providerName" => $inlandsValue->provider ,"port_id" => $ports->ports->id,"port_name" =>  $ports->ports->name,'port_id'=> $ports->ports->id ,'validity_start'=>$inlandsValue->validity,'validity_end'=>$inlandsValue->expire ,"km" => $distancia, "monto" => $monto ,'type' => 'Destination','type_currency' => $inlandsValue->inlandadditionalkms->currency->alphacode ,'idCurrency' => $inlandsValue->currency_id );
                  $arregloInland['inlandDetails'] = $inlandDetails->groupBy('typeContent')->map(function($item){
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
        $inlandDestiny = Collection::make($data);
        //dd($collection); //  completo
        /* $inlandDestiny = $collection->groupBy('port_id')->map(function($item){
          $test = $item->where('monto', $item->min('monto'))->first();
          return $test;
        });*/
        // filtraor por el minimo
      }

    }
    // Origin Addrees
    if($delivery_type == "3" || $delivery_type == "4" ){
      $hideO = '';
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
                  if($details->type == 'twuenty' &&  in_array( '20',$equipment) ){
                    if( $distancia >= $details->lower && $distancia  <= $details->upper){
                      $sub_20 =  number_format( $details->ammount / $rateI, 2, '.', ''); 
                      $monto += $sub_20;
                      $amount_inland = $details->ammount;
                      $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');
                      $km20 = false;
                      // CALCULO MARKUPS 
                      $markupI20=$this->inlandMarkup($inlandPercentage,$inlandAmmount,$inlandMarkup,$sub_20,$typeCurrency,$markupInlandCurre);
                      // FIN CALCULO MARKUPS 
                      $arrayInland20 = array("cant_cont" =>  '1' , "sub_in" => $sub_20 ,'amount' => $amount_inland ,'currency' => $details->currency->alphacode , 'price_unit' => $price_per_unit , 'typeContent' => 'i20') ; 
                      $arrayInland20 = array_merge($markupI20,$arrayInland20);
                      $inlandDetailsOrig[] = $arrayInland20;
                    }
                  }
                  if($details->type == 'forty' &&  in_array( '40',$equipment) ){

                    if( $distancia >= $details->lower && $distancia  <= $details->upper){
                      $sub_40 =  number_format( $details->ammount / $rateI, 2, '.', ''); 
                      $monto += $sub_40;
                      $amount_inland = $details->ammount;
                      $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');
                      $km40 = false;
                      // CALCULO MARKUPS 
                      $markupI40=$this->inlandMarkup($inlandPercentage,$inlandAmmount,$inlandMarkup,$sub_40,$typeCurrency,$markupInlandCurre);
                      // FIN CALCULO MARKUPS 
                      $arrayInland40 = array("cant_cont" =>  '1' , "sub_in" => $sub_40 ,'amount' => $amount_inland ,'currency' => $details->currency->alphacode , 'price_unit' => $price_per_unit , 'typeContent' => 'i40') ; 
                      $arrayInland40 = array_merge($markupI40,$arrayInland40);
                      $inlandDetailsOrig[] = $arrayInland40;
                    }
                  }
                  if($details->type == 'fortyhc' &&   in_array( '40HC',$equipment) ){
                    if( $distancia >= $details->lower && $distancia  <= $details->upper){
                      $sub_40hc =   number_format( $details->ammount / $rateI, 2, '.', ''); 
                      $monto += $sub_40hc;
                      $price_per_unit = number_format($details->ammount / $distancia, 2, '.', '');
                      $amount_inland =  $details->ammount;
                      $km40hc = false;
                      // CALCULO MARKUPS 
                      $markupI40hc=$this->inlandMarkup($inlandPercentage,$inlandAmmount,$inlandMarkup,$sub_40hc,$typeCurrency,$type);
                      // FIN CALCULO MARKUPS 
                      $arrayInland40hc = array("cant_cont" => $request->input('fortyhc') , "sub_in" => $sub_40hc, "des_in" => $texto40hc,'amount' => $amount_inland,'currency' => $details->currency->alphacode , 'price_unit' => $price_per_unit , 'typeContent' => 'i40HC' ) ;
                      $arrayInland40hc = array_merge($markupI40hc,$arrayInland40hc);
                      $inlandDetailsOrig[] = $arrayInland40hc;
                    }
                  }

                }
                // KILOMETROS ADICIONALES 

                if(isset($inlandsValue->inlandadditionalkms)){

                  $rateGeneral = $this->ratesCurrency($inlandsValue->inlandadditionalkms->currency_id,$typeCurrency);
                  if($km20 &&  in_array( '20',$equipment) ){
                    $montoKm = ($distancia * $inlandsValue->inlandadditionalkms->km_20) / $rateGeneral;
                    $sub_20 = $montoKm;
                    $monto += $sub_20;
                    $amount_inland = $distancia * $inlandsValue->inlandadditionalkms->km_20;
                    $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');
                    $amount_inland = number_format($amount_inland, 2, '.', '');
                    // CALCULO MARKUPS 
                    $markupI20=$this->inlandMarkup($inlandPercentage,$inlandAmmount,$inlandMarkup,$sub_20,$typeCurrency,$markupInlandCurre);
                    // FIN CALCULO MARKUPS 
                    $sub_20 = number_format($sub_20, 2, '.', '');
                    $arrayInland20 = array("cant_cont" =>'1' , "sub_in" => $sub_20, "des_in" => $texto20 ,'amount' => $amount_inland ,'currency' =>$inlandsValue->inlandadditionalkms->currency->alphacode, 'price_unit' => $price_per_unit , 'typeContent' => 'i20' ) ;
                    $arrayInland20 = array("cant_cont" =>'1' , "sub_in" => $sub_20, "des_in" => $texto20 ,'amount' => $amount_inland ,'currency' =>$inlandsValue->inlandadditionalkms->currency->alphacode, 'price_unit' => $price_per_unit , 'typeContent' => 'i20' ) ;
                    $arrayInland20 = array_merge($markupI20,$arrayInland20);
                    $inlandDetailsOrig[] = $arrayInland20;
                  }
                  if($km40 &&  in_array( '40',$equipment) ){
                    $montoKm = ($distancia * $inlandsValue->inlandadditionalkms->km_40) / $rateGeneral;
                    $sub_40 = $montoKm;
                    $monto += $sub_40;
                    $amount_inland = $distancia * $inlandsValue->inlandadditionalkms->km_40 ;
                    $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');
                    $amount_inland = number_format($amount_inland, 2, '.', '');
                    // CALCULO MARKUPS 
                    $markupI40=$this->inlandMarkup($inlandPercentage,$inlandAmmount,$inlandMarkup,$sub_40,$typeCurrency,$markupInlandCurre);
                    // FIN CALCULO MARKUPS
                    $sub_40 = number_format($sub_40, 2, '.', '');
                    $arrayInland40 = array("cant_cont" => '1', "sub_in" => $sub_40, "des_in" =>  $texto40,'amount' => $amount_inland ,'currency' => $inlandsValue->inlandadditionalkms->currency->alphacode , 'price_unit' => $price_per_unit, 'typeContent' => 'i40' ) ;
                    $arrayInland40 = array_merge($markupI40,$arrayInland40);
                    $inlandDetailsOrig[] = $arrayInland40;
                  }
                  if($km40hc &&  in_array( '40HC',$equipment)){
                    $montoKm = ($distancia * $inlandsValue->inlandadditionalkms->km_40hc) / $rateGeneral;
                    $sub_40hc = $montoKm;
                    $monto += $sub_40hc;

                    $amount_inland = $distancia * $inlandsValue->inlandadditionalkms->km_40hc;
                    $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');
                    $amount_inland = number_format($amount_inland, 2, '.', '');
                    // CALCULO MARKUPS 
                    $markupI40hc=$this->inlandMarkup($inlandPercentage,$inlandAmmount,$inlandMarkup,$sub_40hc,$typeCurrency,$markupInlandCurre);
                    // FIN CALCULO MARKUPS
                    $sub_40hc = number_format($sub_40hc, 2, '.', '');
                    $arrayInland40hc = array("cant_cont" =>'1' , "sub_in" => $sub_40hc, "des_in" => $texto40hc,'amount' => $amount_inland ,'currency' => $typeCurrency , 'price_unit' => $price_per_unit , 'typeContent' => 'i40HC') ;
                    $arrayInland40hc = array_merge($markupI40hc,$arrayInland40hc);
                    $inlandDetailsOrig[] = $arrayInland40hc;
                  }

                }

                $monto = number_format($monto, 2, '.', '');
                if($monto > 0){
                  $inlandDetailsOrig = Collection::make($inlandDetailsOrig);

                  $arregloInlandOrig = array("prov_id" => $inlandsValue->id ,"provider" => "Inland Haulage","providerName" => $inlandsValue->provider ,"port_id" => $ports->ports->id,"port_name" =>  $ports->ports->name ,'validity_start'=>$inlandsValue->validity,'validity_end'=>$inlandsValue->expire ,"km" => $distancia , "monto" => $monto ,'type' => 'Origin','type_currency' => $typeCurrency ,'idCurrency' => $inlandsValue->currency_id  );

                  $arregloInlandOrig['inlandDetails'] = $inlandDetailsOrig->groupBy('typeContent')->map(function($item){

                    $minimoDetails = $item->where('sub_in', $item->min('sub_in'))->first();

                    return $minimoDetails;
                  });
                  $dataOrig[] = $arregloInlandOrig;
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




    // Consulta base de datos rates
    $arreglo = Rate::whereIn('origin_port',$origin_port)->whereIn('destiny_port',$destiny_port)->with('port_origin','port_destiny','contract','carrier')->whereHas('contract', function($q) use($dateSince,$dateUntil,$user_id,$company_user_id,$company_id)
    {
      $q->whereHas('contract_user_restriction', function($a) use($user_id){
        $a->where('user_id', '=',$user_id);
      })->orDoesntHave('contract_user_restriction');
    })->whereHas('contract', function($q) use($dateSince,$dateUntil,$user_id,$company_user_id,$company_id)
                 {
                   $q->whereHas('contract_company_restriction', function($b) use($company_id){
                     $b->where('company_id', '=',$company_id);
                   })->orDoesntHave('contract_company_restriction');
                 })->whereHas('contract', function($q) use($dateSince,$dateUntil,$company_user_id){
      $q->where('validity', '<=',$dateSince)->where('expire', '>=', $dateUntil)->where('company_user_id','=',$company_user_id);
    });
    $arreglo = $arreglo->get();

    $formulario = $request;
    $array20 = array('2','4','5','6','9','10','11'); // id  calculation type 2 = per 20 , 4= per teu , 5 per container
    $array40 =  array('1','4','5','6','9','10','11'); // id  calculation type 2 = per 40 
    $array40Hc= array('3','4','5','6','9','10','11'); // id  calculation type 3 = per 40HC 
    $array40Nor = array('7','4','5','6','9','10','11');  // id  calculation type 7 = per 40NOR
    $array45 = array('8','4','5','6','9','10','11');  // id  calculation type 8 = per 45

    $arrayContainers =  array('1','2','3','4','7','8'); 

    foreach($arreglo as $data){
      $collectionRate = new Collection();
      $totalFreight = 0;
      $totalRates = 0;
      $totalT20 = 0;
      $totalT40 = 0;
      $totalT40hc = 0;
      $totalT40nor = 0;
      $totalT45 = 0;
      $totalT  = 0 ;
      //Variables Totalizadoras 
      $totales = array();

      $tot_20_F = 0;
      $tot_40_F = 0;
      $tot_40hc_F = 0;
      $tot_40nor_F = 0;
      $tot_45_F = 0;

      $tot_20_O = 0;
      $tot_40_O = 0;
      $tot_40hc_O = 0;
      $tot_40nor_O = 0;
      $tot_45_O = 0;

      $tot_20_D = 0;
      $tot_40_D = 0;
      $tot_40hc_D = 0;
      $tot_40nor_D = 0;
      $tot_45_D = 0;

      $carrier[] = $data->carrier_id;
      $orig_port = array($data->origin_port);
      $dest_port = array($data->destiny_port);
      $rateDetail = new collection();
      $collectionOrigin = new collection();
      $collectionDestiny = new collection();
      $collectionFreight = new collection();


      $arregloRate =  array();
      //Arreglos para guardar el rate

      $arregloRateSave['rate'] = array();
      $arregloRateSave['markups'] = array();

      //Arreglo para guardar charges
      $arregloCharges['origin'] =  array();

      $arregloOrigin =  array();
      $arregloFreight =  array();
      $arregloDestiny =  array();
      // globales
      $arregloOriginG =  array();
      $arregloFreightG =  array();
      $arregloDestinyG =  array();

      $rateC = $this->ratesCurrency($data->currency->id,$typeCurrency);

      // Rates 
      foreach($equipment as $containers){
        //Calculo para los diferentes tipos de contenedores
        if($containers == '20'){
          $markup20 = $this->freightMarkups($freighPercentage,$freighAmmount,$freighMarkup,$data->twuenty,$typeCurrency,$containers);

          $array20Detail = array('price20' => $data->twuenty, 'currency20' => $data->currency->alphacode ,'idCurrency20' => $data->currency_id);
          $tot_20_F += $markup20['monto20'] / $rateC;
          // Arreglos para guardar los rates
          $array_20_save = array('c20' => $data->twuenty);
          $arregloRateSave['rate']  = array_merge($array_20_save,$arregloRateSave['rate']);
          // Markups
          $array_20_markup =  array('m20' => $markup20['markup20']);
          $arregloRateSave['markups']  = array_merge($array_20_markup,$arregloRateSave['markups']);

          $array20T = array_merge($array20Detail,$markup20);
          $arregloRate = array_merge($array20T,$arregloRate);
          //Total 
          $totales['20F'] =  $tot_20_F;

        }
        if($containers == '40'){
          $markup40 = $this->freightMarkups($freighPercentage,$freighAmmount,$freighMarkup,$data->forty,$typeCurrency,$containers);
          $array40Detail = array('price40' => $data->forty, 'currency40' => $data->currency->alphacode ,'idCurrency40' => $data->currency_id);
          $tot_40_F += $markup40['monto40']  / $rateC;
          // Arreglos para guardar los rates
          $array_40_save = array('c40' => $data->forty);
          $arregloRateSave['rate']  = array_merge($array_40_save,$arregloRateSave['rate']);
          // Markups
          $array_40_markup =  array('m40' => $markup40['markup40']);
          $arregloRateSave['markups']  = array_merge($array_40_markup,$arregloRateSave['markups']);

          $array40T = array_merge($array40Detail,$markup40);
          $arregloRate = array_merge($array40T,$arregloRate); 
          $totales['40F'] = $tot_40_F;

        }
        if($containers == '40HC'){
          $markup40hc = $this->freightMarkups($freighPercentage,$freighAmmount,$freighMarkup,$data->fortyhc,$typeCurrency,$containers);
          $array40hcDetail = array('price40hc' => $data->fortyhc, 'currency40hc' => $data->currency->alphacode ,'idCurrency40hc' => $data->currency_id);
          $tot_40hc_F += $markup40hc['monto40HC'] / $rateC;
          // Arreglos para guardar los rates
          $array_40hc_save = array('c40HC' => $data->fortyhc);
          $arregloRateSave['rate']  = array_merge($array_40hc_save,$arregloRateSave['rate']);
          // Markups
          $array_40hc_markup =  array('m40HC' => $markup40hc['markup40HC']);
          $arregloRateSave['markups']  = array_merge($array_40hc_markup,$arregloRateSave['markups']);

          $array40hcT = array_merge($array40hcDetail,$markup40hc);
          $arregloRate = array_merge($array40hcT,$arregloRate); 
          $totales['40hcF'] = $tot_40hc_F;

        }
        if($containers == '40NOR'){
          $markup40nor = $this->freightMarkups($freighPercentage,$freighAmmount,$freighMarkup,$data->fortynor,$typeCurrency,$containers);
          $array40norDetail = array('price40nor' => $data->fortynor, 'currency40nor' => $data->currency->alphacode ,'idCurrency40nor' => $data->currency_id);
          $tot_40nor_F += $markup40nor['monto40NOR'] / $rateC;
          // Arreglos para guardar los rates
          $array_40nor_save = array('c40NOR' => $data->fortynor);
          $arregloRateSave['rate']  = array_merge($array_40nor_save,$arregloRateSave['rate']);
          // Markups
          $array_40nor_markup =  array('m40NOR' => $markup40nor['markup40NOR']);
          $arregloRateSave['markups']  =array_merge($array_40nor_markup,$arregloRateSave['markups']);

          $array40norT = array_merge($array40norDetail,$markup40nor);
          $arregloRate = array_merge($array40norT,$arregloRate); 
          $totales['40norF'] = $tot_40nor_F;

        }
        if($containers == '45'){
          $markup45 = $this->freightMarkups($freighPercentage,$freighAmmount,$freighMarkup,$data->fortyfive,$typeCurrency,$containers);
          $array45Detail = array('price45' => $data->fortyfive, 'currency45' => $data->currency->alphacode ,'idCurrency45' => $data->currency_id);
          $tot_45_F += $markup45['monto45'] / $rateC;
          // Arreglos para guardar los rates
          $array_45_save = array('c45' => $data->fortyfive);
          $arregloRateSave['rate'] = array_merge($array_45_save,$arregloRateSave['rate']);
          // Markups
          $array_45_markup =  array('m45' => $markup45['markup45']);
          $arregloRateSave['markups']  = array_merge($array_45_markup,$arregloRateSave['markups']);

          $array45T = array_merge($array45Detail,$markup45);
          $arregloRate = array_merge($array45T,$arregloRate); 
          $totales['45F'] = $tot_45_F;

        }
      }

      // id de los port  ALL
      array_push($orig_port,1485);
      array_push($dest_port,1485);
      // id de los carrier ALL 
      $carrier_all = 26;
      array_push($carrier,$carrier_all);
      // Id de los paises 
      array_push($origin_country,250);
      array_push($destiny_country,250);

      // ################### Calculos local  Charges #############################

      $localChar = LocalCharge::where('contract_id','=',$data->contract_id)->whereHas('localcharcarriers', function($q) use($carrier) {
        $q->whereIn('carrier_id', $carrier);
      })->where(function ($query) use($orig_port,$dest_port,$origin_country,$destiny_country){
        $query->whereHas('localcharports', function($q) use($orig_port,$dest_port) {
          $q->whereIn('port_orig', $orig_port)->whereIn('port_dest',$dest_port);
        })->orwhereHas('localcharcountries', function($q) use($origin_country,$destiny_country) {
          $q->whereIn('country_orig', $origin_country)->whereIn('country_dest', $destiny_country);
        });
      })->with('localcharports.portOrig','localcharcarriers.carrier','currency','surcharge.saleterm')->orderBy('typedestiny_id','calculationtype_id','surchage_id')->get();

      foreach($localChar as $local){

        $rateMount = $this->ratesCurrency($local->currency->id,$typeCurrency);

        // Condicion para enviar los terminos de venta o compra
        if(isset($local->surcharge->saleterm->name)){
          $terminos = $local->surcharge->saleterm->name;
        }else{
          $terminos = $local->surcharge->name;
        }

        foreach($local->localcharcarriers as $localCarrier){
          if($localCarrier->carrier_id == $data->carrier_id || $localCarrier->carrier_id ==  $carrier_all ){
            //Origin
            if($local->typedestiny_id == '1'){

              if(in_array($local->calculationtype_id, $array20) && in_array( '20',$equipment) ){

                $montoOrig = $local->ammount;
                $monto =   $local->ammount  / $rateMount ;
                $monto = number_format($monto, 2, '.', '');
                $markup20 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'20' ,'rate_id' => $data->id ,'calculation_id'=> $local->calculationtype->id ,'montoOrig' => $montoOrig,'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency );

                $arregloOrigin = array_merge($arregloOrigin,$markup20);
                $collectionOrigin->push($arregloOrigin);      
                $tot_20_O  +=  $markup20['montoMarkup'];

              }
              if(in_array($local->calculationtype_id, $array40) && in_array( '40',$equipment) ){

                $montoOrig = $local->ammount;
                $monto =   $local->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$local->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40','rate_id' => $data->id ,'montoOrig' => $montoOrig ,'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency  );
                $arregloOrigin = array_merge($arregloOrigin,$markup40);
                $collectionOrigin->push($arregloOrigin);
                $tot_40_O  +=  $markup40['montoMarkup'];

              }
              if(in_array($local->calculationtype_id, $array40Hc)&& in_array( '40HC',$equipment)){

                $montoOrig = $local->ammount;
                $monto =   $local->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$local->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40hc = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40hc','rate_id' => $data->id  ,'montoOrig' => $montoOrig ,'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency  );
                $arregloOrigin = array_merge($arregloOrigin,$markup40hc);
                $collectionOrigin->push($arregloOrigin);
                $tot_40hc_O  +=   $markup40hc['montoMarkup'];

              }
              if(in_array($local->calculationtype_id, $array40Nor)&& in_array( '40NOR',$equipment)){

                $montoOrig = $local->ammount;
                $monto =   $local->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$local->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40nor = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40nor','rate_id' => $data->id  ,'montoOrig' => $montoOrig ,'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency  );
                $arregloOrigin = array_merge($arregloOrigin,$markup40nor);
                $collectionOrigin->push($arregloOrigin);
                $tot_40nor_O  +=  $markup40nor['montoMarkup'];

              }
              if(in_array($local->calculationtype_id, $array45) && in_array( '45',$equipment)){

                $montoOrig = $local->ammount;
                $monto =   $local->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$local->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup45 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'45','rate_id' => $data->id  ,'montoOrig' => $montoOrig ,'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id   ,'currency_orig_id' => $idCurrency );
                $arregloOrigin = array_merge($arregloOrigin,$markup45);
                $collectionOrigin->push($arregloOrigin);
                $tot_45_O  +=  $markup45['montoMarkup'];

              }

              if(in_array($local->calculationtype_id,$arrayContainers)){
                $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => 'Per Container','contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'99','rate_id' => $data->id  ,'calculation_id'=> '5', 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id   ,'currency_orig_id' => $idCurrency );

              }else{
                $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $local->calculationtype->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $local->currency->id   ,'currency_orig_id' => $idCurrency );

              }
              $collectionOrigin->push($arregloOrigin);

            }

            //Destiny
            if($local->typedestiny_id == '2'){

              if(in_array($local->calculationtype_id, $array20) && in_array( '20',$equipment)  ){

                $montoOrig = $local->ammount;
                $monto =   $local->ammount  / $rateMount ;
                $monto = number_format($monto, 2, '.', '');
                $markup20 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloDestiny = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'20' ,'rate_id' => $data->id ,'montoOrig' => $montoOrig ,'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency  );
                $arregloDestiny = array_merge($arregloDestiny,$markup20);
                $collectionDestiny->push($arregloDestiny);
                $tot_20_D +=  $markup20['montoMarkup'];

              }
              if(in_array($local->calculationtype_id, $array40)&& in_array( '40',$equipment)){

                $montoOrig = $local->ammount;
                $monto =   $local->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$local->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloDestiny = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40' ,'rate_id' => $data->id ,'montoOrig' => $montoOrig ,'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency  );
                $arregloDestiny = array_merge($arregloDestiny,$markup40);
                $collectionDestiny->push($arregloDestiny);
                $tot_40_D  +=  $markup40['montoMarkup'];
              }
              if(in_array($local->calculationtype_id, $array40Hc)&& in_array( '40HC',$equipment)){

                $montoOrig = $local->ammount;
                $monto =   $local->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$local->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40hc = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloDestiny = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40hc' ,'rate_id' => $data->id ,'montoOrig' => $montoOrig ,'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency );
                $arregloDestiny = array_merge($arregloDestiny,$markup40hc);
                $collectionDestiny->push($arregloDestiny);
                $tot_40hc_D  +=   $markup40hc['montoMarkup'];

              }
              if(in_array($local->calculationtype_id, $array40Nor)&& in_array( '40NOR',$equipment)){

                $montoOrig = $local->ammount;
                $monto =   $local->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$local->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40nor = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloDestiny = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40nor' ,'rate_id' => $data->id ,'montoOrig' => $montoOrig ,'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency );
                $arregloDestiny = array_merge($arregloDestiny,$markup40nor);
                $collectionDestiny->push($arregloDestiny);
                $tot_40nor_D  +=  $markup40nor['montoMarkup'];

              }
              if(in_array($local->calculationtype_id, $array45) && in_array( '45',$equipment)){

                $montoOrig = $local->ammount;
                $monto =   $local->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$local->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup45 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloDestiny = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'45' ,'rate_id' => $data->id ,'montoOrig' => $montoOrig ,'typecurrency' => $typeCurrency,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency );
                $arregloDestiny = array_merge($arregloDestiny,$markup45);
                $collectionDestiny->push($arregloDestiny);
                $tot_45_D  +=  $markup45['montoMarkup'];
                $montoOrig = $local->ammount;
              }

              if(in_array($local->calculationtype_id,$arrayContainers)){
                $arregloDestiny = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => 'Per Container','contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> '5', 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency  );
              }else{
                $arregloDestiny = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'99','rate_id' => $data->id ,'calculation_id'=> $local->calculationtype->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id   ,'currency_orig_id' => $idCurrency );
              }
              $collectionDestiny->push($arregloDestiny);
            }
            //Freight
            if($local->typedestiny_id == '3'){

              if(in_array($local->calculationtype_id, $array20) && in_array( '20',$equipment) ){

                $montoOrig = $local->ammount;
                $monto =   $local->ammount  / $rateMount ;
                $monto = number_format($monto, 2, '.', '');
                $markup20 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'20','rate_id' => $data->id  ,'montoOrig' => $montoOrig ,'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency );
                $arregloFreight = array_merge($arregloFreight,$markup20);
                $collectionFreight->push($arregloFreight);
                $totales['20F'] += $markup20['montoMarkup'];

              }
              if(in_array($local->calculationtype_id, $array40) && in_array( '40',$equipment) ){

                $montoOrig = $local->ammount;
                $monto =   $local->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$local->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40','rate_id' => $data->id  ,'montoOrig' => $montoOrig ,'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency );
                $arregloFreight = array_merge($arregloFreight,$markup40);
                $collectionFreight->push($arregloFreight);
                $totales['40F'] +=  $markup40['montoMarkup'];

              }
              if(in_array($local->calculationtype_id, $array40Hc) && in_array( '40HC',$equipment) ){

                $montoOrig = $local->ammount;
                $monto =   $local->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$local->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40hc = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40hc' ,'rate_id' => $data->id ,'montoOrig' => $montoOrig ,'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency );
                $arregloFreight = array_merge($arregloFreight,$markup40hc);
                $collectionFreight->push($arregloFreight);
                $totales['40hcF'] +=   $markup40hc['montoMarkup'];

              }
              if(in_array($local->calculationtype_id, $array40Nor)  && in_array( '40NOR',$equipment) ){

                $montoOrig = $local->ammount;
                $monto = $local->ammount / $rateMount;
                $monto = $this->perTeu($monto,$local->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40nor = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40nor','rate_id' => $data->id ,'montoOrig' => $montoOrig ,'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency );
                $arregloFreight = array_merge($arregloFreight,$markup40nor);
                $collectionFreight->push($arregloFreight);
                $totales['40norF'] += $markup40nor['montoMarkup'];

              }
              if(in_array($local->calculationtype_id, $array45) && in_array( '45',$equipment) ){

                $montoOrig = $local->ammount;
                $monto =   $local->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$local->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup45 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'45','rate_id' => $data->id ,'montoOrig' => $montoOrig,'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency );
                $arregloFreight = array_merge($arregloFreight,$markup45);
                $collectionFreight->push($arregloFreight);
                $totales['45F'] +=  $markup45['montoMarkup'];

              }

              if(in_array($local->calculationtype_id,$arrayContainers)){
                $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => 'Per Container','contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> '5' , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency  );
              }else{

                $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'99'  ,'rate_id' => $data->id ,'calculation_id'=> $local->calculationtype->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency ) ;
              }

              $collectionFreight->push($arregloFreight);

            }

          }
        }

      }
      // ################## Fin local Charges        #############################

      //################## Calculos Global Charges #################################

      $globalChar = GlobalCharge::where('validity', '<=',$dateSince)->where('expire', '>=', $dateUntil)->whereHas('globalcharcarrier', function($q) use($carrier) {
        $q->whereIn('carrier_id', $carrier);
      })->where(function ($query) use($orig_port,$dest_port,$origin_country,$destiny_country){
        $query->whereHas('globalcharport', function($q) use($orig_port,$dest_port) {
          $q->whereIn('port_orig', $orig_port)->whereIn('port_dest', $dest_port);
        })->orwhereHas('globalcharcountry', function($q) use($origin_country,$destiny_country) {
          $q->whereIn('country_orig', $origin_country)->whereIn('country_dest', $destiny_country);
        });
      })->where('company_user_id','=',$company_user_id)->with('globalcharport.portOrig','globalcharport.portDest','globalcharcarrier.carrier','currency','surcharge.saleterm')->get();


      foreach($globalChar as $global){

        $rateMount = $this->ratesCurrency($global->currency->id,$typeCurrency);

        // Condicion para enviar los terminos de venta o compra
        if(isset($global->surcharge->saleterm->name)){
          $terminos = $global->surcharge->saleterm->name;
        }else{
          $terminos = $global->surcharge->name;
        }

        foreach($global->globalcharcarrier as $globalCarrier){
          if($globalCarrier->carrier_id == $data->carrier_id || $globalCarrier->carrier_id ==  $carrier_all ){
            //Origin
            if($global->typedestiny_id == '1'){

              if(in_array($global->calculationtype_id, $array20) && in_array('20',$equipment) ){

                $montoOrig = $global->ammount ;
                $monto =   $global->ammount  / $rateMount ;

                $monto = number_format($monto, 2, '.', '');
                $markup20 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloOriginG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'20' ,'rate_id' => $data->id , 'montoOrig' => $montoOrig , 'typecurrency' => $typeCurrency ,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency  );
                $arregloOriginG = array_merge($arregloOriginG,$markup20);
                $collectionOrigin->push($arregloOriginG);
                $tot_20_O  +=  $markup20['montoMarkup'];

              }
              if(in_array($global->calculationtype_id, $array40)&& in_array( '40',$equipment)){

                $montoOrig = $global->ammount ;
                $monto =   $global->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$global->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloOriginG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'40' ,'rate_id' => $data->id ,'montoOrig' => $montoOrig ,  'typecurrency' => $typeCurrency ,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
                $arregloOriginG = array_merge($arregloOriginG,$markup40);
                $collectionOrigin->push($arregloOriginG);
                $tot_40_O  +=   $markup40['montoMarkup'];
              }
              if(in_array($global->calculationtype_id, $array40Hc)&& in_array( '40HC',$equipment)){

                $montoOrig = $global->ammount ;
                $monto =   $global->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$global->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40hc = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloOriginG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'40hc','rate_id' => $data->id  , 'montoOrig' => $montoOrig , 'typecurrency' => $typeCurrency ,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
                $arregloOriginG = array_merge($arregloOriginG,$markup40hc);
                $collectionOrigin->push($arregloOriginG);
                $tot_40hc_O  +=   $markup40hc['montoMarkup'];
              }
              if(in_array($global->calculationtype_id, $array40Nor)&& in_array( '40NOR',$equipment)){

                $montoOrig = $global->ammount ;
                $monto =   $global->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$global->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40nor = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloOriginG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'40nor','rate_id' => $data->id  ,'montoOrig' => $montoOrig ,  'typecurrency' => $typeCurrency ,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
                $arregloOriginG = array_merge($arregloOriginG,$markup40nor);
                $collectionOrigin->push($arregloOriginG);
                $tot_40nor_O  +=  $markup40nor['montoMarkup'];
              }
              if(in_array($global->calculationtype_id, $array45)&& in_array( '45',$equipment)){
                $montoOrig = $global->ammount ;

                $monto =   $global->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$global->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup45 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloOriginG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'45','rate_id' => $data->id   ,'montoOrig' => $montoOrig ,  'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
                $arregloOriginG = array_merge($arregloOriginG,$markup45);
                $collectionOrigin->push($arregloOriginG);
                $tot_45_O  +=  $markup45['montoMarkup'];
              }

              if(in_array($global->calculationtype_id,$arrayContainers)){
                $arregloOriginG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => 'Per Container','contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> '5' ,'montoOrig' => 0.00,  'typecurrency' => $typeCurrency ,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );

              }else{
                $arregloOriginG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $global->calculationtype->id ,'montoOrig' => 0.00,  'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );


              }
              $collectionOrigin->push($arregloOriginG);
            }
            //Destiny
            if($global->typedestiny_id == '2'){

              if(in_array($global->calculationtype_id, $array20) &&  in_array('20',$equipment)){
                $montoOrig = $global->ammount ;
                $monto =   $global->ammount  / $rateMount ;

                $monto = number_format($monto, 2, '.', '');
                $markup20 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloDestinyG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'20' ,'rate_id' => $data->id  , 'montoOrig' => $montoOrig , 'typecurrency' => $typeCurrency ,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
                $arregloDestinyG = array_merge($arregloDestinyG,$markup20);
                $collectionDestiny->push($arregloDestinyG);
                $tot_20_D +=  $markup20['montoMarkup'];
              }
              if(in_array($global->calculationtype_id, $array40)&& in_array( '40',$equipment) ){
                $montoOrig = $global->ammount ;

                $monto =   $global->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$global->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloDestinyG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'40' ,'rate_id' => $data->id  , 'montoOrig' => $montoOrig , 'typecurrency' => $typeCurrency ,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
                $arregloDestinyG = array_merge($arregloDestinyG,$markup40);
                $collectionDestiny->push($arregloDestinyG);
                $tot_40_D  +=  $markup40['montoMarkup'];
              }
              if(in_array($global->calculationtype_id, $array40Hc)&& in_array( '40HC',$equipment) ){
                $montoOrig = $global->ammount ;
                $monto =   $global->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$global->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40hc = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloDestinyG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'40hc' ,'rate_id' => $data->id  , 'montoOrig' => $montoOrig , 'typecurrency' => $typeCurrency ,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
                $arregloDestinyG = array_merge($arregloDestinyG,$markup40hc);
                $collectionDestiny->push($arregloDestinyG);
                $tot_40hc_D  +=   $markup40hc['montoMarkup'];
              }
              if(in_array($global->calculationtype_id, $array40Nor)&& in_array( '40NOR',$equipment) ){
                $montoOrig = $global->ammount ;
                $monto =   $global->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$global->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40nor = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloDestinyG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'40nor' ,'rate_id' => $data->id  , 'montoOrig' => $montoOrig , 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
                $arregloDestinyG = array_merge($arregloDestinyG,$markup40nor);
                $collectionDestiny->push($arregloDestinyG);
                $tot_40nor_D  +=  $markup40nor['montoMarkup'];
              }
              if(in_array($global->calculationtype_id, $array45)&& in_array( '45',$equipment) ){
                $montoOrig = $global->ammount ;
                $monto =   $global->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$global->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup45 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloDestinyG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'45' ,'rate_id' => $data->id  , 'montoOrig' => $montoOrig , 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
                $arregloDestinyG = array_merge($arregloDestinyG,$markup45);
                $collectionDestiny->push($arregloDestinyG);
                $tot_45_D  +=  $markup45['montoMarkup'];
              }


              if(in_array($global->calculationtype_id,$arrayContainers)){
                $arregloDestinyG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => 'Per Container','contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'99','rate_id' => $data->id ,'calculation_id'=> '5', 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
              }else{
                $arregloDestinyG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $global->calculationtype->id, 'montoOrig' => 0.00,  'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
              }

              $collectionDestiny->push($arregloDestinyG);
            }
            //Freight
            if($global->typedestiny_id == '3'){

              if(in_array($global->calculationtype_id, $array20) && in_array('20',$equipment)){
                $montoOrig = $global->ammount ;

                $monto =   $global->ammount  / $rateMount ;

                $monto = number_format($monto, 2, '.', '');
                $markup20 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloFreightG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'20' ,'rate_id' => $data->id  , 'montoOrig' => $montoOrig , 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
                $arregloFreightG = array_merge($arregloFreightG,$markup20);
                $collectionFreight->push($arregloFreightG);
                $totales['20F'] += $markup20['montoMarkup'];

              }
              if(in_array($global->calculationtype_id, $array40) && in_array( '40',$equipment) ){
                $montoOrig = $global->ammount ;
                $monto =   $global->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$global->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloFreightG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'40','rate_id' => $data->id   , 'montoOrig' => $montoOrig , 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
                $arregloFreightG = array_merge($arregloFreightG,$markup40);
                $collectionFreight->push($arregloFreightG);
                $totales['40F'] +=  $markup40['montoMarkup'];
              }
              if(in_array($global->calculationtype_id, $array40Hc) && in_array( '40HC',$equipment) ){
                $montoOrig = $global->ammount ;
                $monto =   $global->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$global->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40hc = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloFreightG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'40hc','rate_id' => $data->id   , 'montoOrig' => $montoOrig , 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
                $arregloFreightG = array_merge($arregloFreightG,$markup40hc);
                $collectionFreight->push($arregloFreightG);
                $totales['40hcF'] +=   $markup40hc['montoMarkup'];
              }
              if(in_array($global->calculationtype_id, $array40Nor) && in_array( '40NOR',$equipment) ){
                $montoOrig = $global->ammount ;
                $monto =   $global->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$global->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40nor = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloFreightG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'40nor','rate_id' => $data->id  , 'montoOrig' => $montoOrig , 'typecurrency' => $typeCurrency  ,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
                $arregloFreightG = array_merge($arregloFreightG,$markup40nor);
                $collectionFreight->push($arregloFreightG);
                $totales['40norF'] += $markup40nor['montoMarkup'];
              }
              if(in_array($global->calculationtype_id, $array45) && in_array( '45',$equipment) ){
                $montoOrig = $global->ammount ;
                $monto =   $global->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$global->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup45 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloFreightG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'45' ,'rate_id' => $data->id  , 'montoOrig' => $montoOrig , 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
                $arregloFreightG = array_merge($arregloFreightG,$markup45);
                $collectionFreight->push($arregloFreightG);
                $totales['45F'] +=  $markup45['montoMarkup'];
              }

              if(in_array($global->calculationtype_id,$arrayContainers)){

                $arregloFreightG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => 'Per Container','contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'99','rate_id' => $data->id ,'calculation_id'=> '5' , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency ,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
              }else{

                $arregloFreightG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' =>  $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $global->calculationtype->id ,'montoOrig' => 0.00,  'typecurrency' => $typeCurrency ,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );

              }

              $collectionFreight->push($arregloFreightG);

            }

          }
        }
      }

      // ############################ Fin global charges ######################

      // Ordenar las colecciones
      if(!empty($collectionFreight))
        $collectionFreight = $this->OrdenarCollection($collectionFreight);
      if(!empty($collectionDestiny))
        $collectionDestiny = $this->OrdenarCollection($collectionDestiny);
      if(!empty($collectionOrigin))
        $collectionOrigin = $this->OrdenarCollection($collectionOrigin);

      // Totales Freight 
      if(!isset($totales['20F']))
        $totales['20F'] = 0;
      if(!isset($totales['40F']))
        $totales['40F'] = 0;
      if(!isset($totales['40hcF']))
        $totales['40hcF'] = 0;
      if(!isset($totales['40norF']))
        $totales['40norF'] = 0;
      if(!isset($totales['45F']))
        $totales['45F'] = 0;



      $totalT20 = $tot_20_D +  $totales['20F'] + $tot_20_O ;
      $totalT40  = $tot_40_D + $totales['40F'] + $tot_40_O ;
      $totalT40hc  = $tot_40hc_D + $totales['40hcF'] + $tot_40hc_O ;
      $totalT40nor  = $tot_40nor_D +  $totales['40norF'] + $tot_40nor_O ;
      $totalT45  = $tot_45_D + $totales['45F'] + $tot_45_O ;


      $totalRates += $totalT;
      $array = array('type'=>'Ocean Freight','detail'=>'Per Container','subtotal'=>$totalRates, 'total' =>$totalRates." ". $typeCurrency , 'idCurrency' => $data->currency_id,'currency_rate' => $data->currency->alphacode,'rate_id' => $data->id );
      $array = array_merge($array,$arregloRate);
      $array =  array_merge($array,$arregloRateSave);
      $collectionRate->push($array);


      // TERMS AND CONDITIONS 
      $port_all = harbor::where('name','ALL')->first();
      $term_port_orig = array($data->origin_port);
      $term_port_dest = array($data->destiny_port);
      $term_carrier_id[] = $data->carrier_id;
      array_push($term_carrier_id,$carrier_all);

      $terms_all = TermsPort::where('port_id',$port_all->id)->with('term')->whereHas('term', function($q) use($term_carrier_id)  {
        $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id)->whereHas('TermConditioncarriers', function($b) use($term_carrier_id)  {
          $b->wherein('carrier_id',$term_carrier_id);
        });
      })->get();
      $terms_origin = TermsPort::wherein('port_id',$term_port_orig)->with('term')->whereHas('term', function($q) use($term_carrier_id)  {
        $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id)->whereHas('TermConditioncarriers', function($b) use($term_carrier_id)  {
          $b->wherein('carrier_id',$term_carrier_id);
        });
      })->get();

      $terms_destination = TermsPort::wherein('port_id',$term_port_dest)->with('term')->whereHas('term', function($q)  use($term_carrier_id) {
        $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id)->whereHas('TermConditioncarriers', function($b) use($term_carrier_id)  {
          $b->wherein('carrier_id',$term_carrier_id);
        });
      })->get();

      $termsO='';
      $termsD='';
      $terms ='';
      if($mode=='1'){
        $termsO= 'Export:';
        foreach($terms_origin as $termOrig){
          $termsO .=  "<br>".$termOrig->term->export;
        }
        foreach($terms_destination as $termDest){
          $termsD .=  "<br>".$termDest->term->export;
        }

      }else if($mode=='2' ){
        $termsO= 'Import:';
        foreach($terms_origin as $termOrig){
          $termsO .=  "<br>".$termOrig->term->import;
        }
        foreach($terms_destination as $termDest){
          $termsD .=  "<br>".$termDest->term->import;
        }
      }
      $terms = $termsO." ".$termsD ; 



      //TERMS 

      $data->setAttribute('terms',$terms);
      // Valores
      $data->setAttribute('rates',$collectionRate);
      $data->setAttribute('localfreight',$collectionFreight);
      $data->setAttribute('localdestiny',$collectionDestiny);
      $data->setAttribute('localorigin',$collectionOrigin);
      // Valores totales por contenedor
      $data->setAttribute('total20', number_format($totalT20, 2, '.', ''));
      $data->setAttribute('total40', number_format($totalT40, 2, '.', ''));
      $data->setAttribute('total40hc', number_format($totalT40hc, 2, '.', ''));
      $data->setAttribute('total40nor', number_format($totalT40nor, 2, '.', ''));
      $data->setAttribute('total45', number_format($totalT45, 2, '.', ''));

      // Freight
      $data->setAttribute('tot20F', number_format($totales['20F'], 2, '.', ''));
      $data->setAttribute('tot40F', number_format($totales['40F'], 2, '.', ''));
      $data->setAttribute('tot40hcF', number_format($totales['40hcF'], 2, '.', ''));
      $data->setAttribute('tot40norF', number_format($totales['40norF'], 2, '.', ''));
      $data->setAttribute('tot45F', number_format($totales['45F'], 2, '.', ''));

      // Origin
      $data->setAttribute('tot20O', number_format($tot_20_O, 2, '.', ''));
      $data->setAttribute('tot40O', number_format($tot_40_O, 2, '.', ''));
      $data->setAttribute('tot40hcO', number_format($tot_40hc_O, 2, '.', ''));
      $data->setAttribute('tot40norO', number_format($tot_40nor_O, 2, '.', ''));
      $data->setAttribute('tot45O', number_format($tot_45_O, 2, '.', ''));
      //Destiny
      $data->setAttribute('tot20D', number_format($tot_20_D, 2, '.', ''));
      $data->setAttribute('tot40D', number_format($tot_40_D, 2, '.', ''));
      $data->setAttribute('tot40hcD', number_format($tot_40hc_D, 2, '.', ''));
      $data->setAttribute('tot40norD', number_format($tot_40nor_D, 2, '.', ''));
      $data->setAttribute('tot45D', number_format($tot_45_D, 2, '.', ''));
      // INLANDS
      $data->setAttribute('inlandDestiny',$inlandDestiny);
      $data->setAttribute('inlandOrigin',$inlandOrigin);
      $data->setAttribute('typeCurrency',$typeCurrency);




    }
    $arreglo  =  $arreglo->sortBy('total20');
    //dd($arreglo);
    return view('quotesv2/search',  compact('arreglo','form','companies','quotes','countries','harbors','prices','company_user','currencies','currency_name','incoterm','equipmentHides','carrierMan','hideD','hideO','airlines'));

  }


  public function perTeu($monto,$calculation_type){
    if($calculation_type == 4){
      $monto = $monto * 2;
      return $monto;
    }else{
      return $monto;
    }
  }

  public function inlandMarkup($inlandPercentage,$inlandAmmount,$inlandMarkup,$monto,$typeCurrency,$markupInlandCurre){

    if($inlandPercentage != 0){
      $markup = ( $monto *  $inlandPercentage ) / 100 ;
      $markup = number_format($markup, 2, '.', '');
      $monto += $markup ;
      $monto = number_format($monto, 2, '.', '');
      $arraymarkupI = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($inlandPercentage%)",'montoInlandT' => $monto ) ;
    }else{

      $markup =$inlandAmmount;
      $markup = number_format($markup, 2, '.', '');
      $monto += number_format($inlandMarkup, 2, '.', '');
      $arraymarkupI = array("markup" => $markup , "markupConvert" => $inlandMarkup, "typemarkup" => $markupInlandCurre,'montoInlandT' => $monto ) ;

    }
    return $arraymarkupI;

  }

  public function freightMarkups($freighPercentage,$freighAmmount,$freighMarkup,$monto,$typeCurrency,$type){

    if($freighPercentage != 0){
      $freighPercentage = intval($freighPercentage);
      $markup = ( $monto *  $freighPercentage ) / 100 ;
      $markup = number_format($markup, 2, '.', '');
      $monto += $markup ;
      number_format($monto, 2, '.', '');
      $arraymarkup = array("markup".$type => $markup , "markupConvert".$type => $markup, "typemarkup".$type => "$typeCurrency ($freighPercentage%)", "monto".$type => $monto) ;
    }else{

      $markup =trim($freighAmmount);
      $monto += $freighMarkup;
      $monto = number_format($monto, 2, '.', '');
      $arraymarkup = array("markup".$type => $markup , "markupConvert".$type => $freighMarkup, "typemarkup".$type => $typeCurrency,"monto".$type => $monto) ;
    }

    return $arraymarkup;

  }

  public function localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre){

    if($localPercentage != 0){
      $markup = ( $monto *  $localPercentage ) / 100 ;
      $markup = number_format($markup, 2, '.', '');
      $monto += $markup;
      $arraymarkup = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)",'montoMarkup' => $monto) ;

    }else{
      $markup =$localAmmount;
      $markup = number_format($markup, 2, '.', '');
      $monto += $localMarkup;
      $monto = number_format($monto, 2, '.', '');
      $arraymarkup = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre,'montoMarkup' => $monto) ;

    }


    return $arraymarkup;

  }

  public function OrdenarCollection($collection){

    $collection = $collection->groupBy([
      'surcharge_name',
      function ($item)  {
        return $item['type'];
      },
    ], $preserveKeys = true);

    // Se Ordena y unen la collection
    $collect = new collection();
    $monto = 0;
    $montoMarkup = 0;
    $totalMarkup = 0;

    foreach($collection as $item){


      foreach($item as $items){
        $total = count($items);

        if($total > 1 ){
          foreach($items as $itemsDetail){

            $monto += $itemsDetail['monto']; 
            $montoMarkup += $itemsDetail['montoMarkup']; 
            $totalMarkup += $itemsDetail['markup']; 
          }
          $itemsDetail['monto'] = number_format($monto, 2, '.', '');
          $itemsDetail['montoMarkup'] = number_format($montoMarkup, 2, '.', ''); 
          $itemsDetail['markup'] =  number_format($totalMarkup, 2, '.', '');
          $itemsDetail['currency'] = $itemsDetail['typecurrency'];
          $itemsDetail['currency_id'] = $itemsDetail['currency_orig_id'];
          $collect->push($itemsDetail);
          $monto = 0;
          $montoMarkup = 0;
          $totalMarkup = 0;

        }else{
          foreach($items as $itemsDetail){
            $itemsDetail['monto'] = number_format($itemsDetail['montoOrig'], 2, '.', ''); 
            $collect->push($itemsDetail); 
            $monto = 0;
            $montoMarkup = 0;
            $totalMarkup = 0;
          }
        }
      }
    }

    $collect = $collect->groupBy([
      'surcharge_name',
      function ($item) use($collect) {
        $collect->put('x','surcharge_name');
        return $item['type'];
      },
    ], $preserveKeys = true);

    return $collect;
  }


}
