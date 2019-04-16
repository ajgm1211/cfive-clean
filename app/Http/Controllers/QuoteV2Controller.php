<?php

namespace App\Http\Controllers;

use App\AutomaticRate;
use App\AutomaticInland;
use App\CalculationType;
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
use App\Quote;
use App\QuoteV2;
use App\Surcharge;
use App\User;
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
        $custom_id  = $quote->quote_id;
        $company  = $quote->company->business_name;
      }

      if(!$quote->origin_address){
        $origin = $quote->origin_port->display_name;
      } else {
        $origin = $quote->origin_address;
      }

      if(!$quote->destination_address){
        $destination = $quote->destination_port->display_name;
      } else {
        $destination = $quote->destination_address;
      }

      $data = [
        'id'            => $quote->id,
        'custom_id'     => $custom_id,
        'idSet'         => setearRouteKey($quote->id),
        'client'        => $company,
        'created'       => date_format($quote->created_at, 'M d, Y H:i'),
        'user'          => $quote->user->name.' '.$quote->user->lastname,
        'origin'        => $origin,
        'destination'   => $destination,
        'type'          => $quote->type,
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
            <a href="/v2/quotes/'.$colletion['idSet'].'/edit" class="dropdown-item" >
            <span>
            <i class="la la-edit"></i>
            &nbsp;
            Edit
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
      ->editColumn('id', 'ID: {{$id}}')->make(true);
  }

  public function show($id)
  {
    //Setting id
    $id = obtenerRouteKey($id);
    $origin_charges = new Collection();
    $freight_charges = new Collection();
    $destination_charges = new Collection();

    //Retrieving all data
    $company_user_id = \Auth::user()->company_user_id;
    $quote = QuoteV2::findOrFail($id);
    $inlands = AutomaticInland::where('quote_id',$quote->id)->get();
    $rates = AutomaticRate::where('quote_id',$quote->id)->get();
    foreach ($rates as $rate) {
      foreach ($rate->charge as $item) {
        if($item->type_id==1){
          $origin_charges->push($item);
        }else if($item->type_id==2){
          $destination_charges->push($item);
        }else{
          $freight_charges->push($item);
        }
      }
    }

    $companies = Company::where('company_user_id',$company_user_id)->pluck('business_name','id');
    $contacts = Contact::where('company_id',$quote->company_id)->pluck('first_name','id');
    $incoterms = Incoterm::pluck('name','id');
    $users = User::where('company_user_id',$company_user_id)->pluck('name','id');
    $prices = Price::where('company_user_id',$company_user_id)->pluck('name','id');
    $currencies = Currency::pluck('alphacode','id');
    $company_user=CompanyUser::find(\Auth::user()->company_user_id);
    $currency_cfg = Currency::find($company_user->currency_id);
    $equipmentHides = $this->hideContainer($quote->equipment);
    $calculation_types = CalculationType::where('name','Per Container')->pluck('name','id');
    $surcharges = Surcharge::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');
    $email_templates = EmailTemplate::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');

    //Adding country codes to rates collection
    foreach ($rates as $item) {
      $rates->map(function ($item) {
        $item['origin_country_code'] = strtolower(substr($item->origin_port->code, 0, 2));
        $item['destination_country_code'] = strtolower(substr($item->destination_port->code, 0, 2));
        return $item;
      });
    }

    $emaildimanicdata = json_encode([
      'quote_bool'   => 'true',
      'company_id'   => '',
      'contact_id'   => '',
      'quote_id'     => $quote->id
    ]);

    return view('quotesv2/show', compact('quote','companies','incoterms','users','prices','contacts','currencies','currency_cfg','equipmentHides','freight_charges','origin_charges','destination_charges','calculation_types','rates','surcharges','email_templates','inlands','emaildimanicdata'));
  }

  public function updateQuoteCharges(Request $request)
  {
    //$charge=Charge::find($request->pk)->update(['amount->20' => $request->value]);
    DB::table('charges')
      ->where('id', $request->pk)
      ->update([$request->name => $request->value]);

    return response()->json(['success'=>'done']);
  }

  public function update(Request $request,$id)
  {

    $validation = explode('/',$request->validity);
    $validity_start = $validation[0];
    $validity_end = $validation[1];

    $quote=QuoteV2::find($id);
    $quote->quote_id=$request->quote_id;
    $quote->type=$request->type;
    $quote->company_id=$request->company_id;
    $quote->contact_id=$request->contact_id;
    $quote->delivery_type=$request->delivery_type;
    $quote->date_issued=$request->date_issued;
    $quote->incoterm_id=$request->incoterm_id;
    $quote->equipment=$request->equipment;
    $quote->validity_start=$validity_start;
    $quote->validity_end=$validity_end;
    $quote->user_id=$request->user_id;
    $quote->status=$request->status;
    $quote->update();

    $contact_name=$quote->contact->first_name.' '.$quote->contact->last_name;

    return response()->json(['message'=>'Ok','quote'=>$quote,'contact_name'=>$contact_name]);
  }

  public function updatePaymentConditions(Request $request,$id)
  {
    $quote=QuoteV2::find($id);

    $quote->payment_conditions=$request->payments;
    $quote->update();

    return response()->json(['message'=>'Ok','quote'=>$quote]);
  }

  public function updateTerms(Request $request,$id)
  {
    $quote=QuoteV2::find($id);

    $quote->terms_and_conditions=$request->terms;
    $quote->update();

    return response()->json(['message'=>'Ok','quote'=>$quote]);
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

    $quote_duplicate->save();

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

      $numeroFinal = $numeroFinal[1] +1;

      $iniciales = $iniciales."-".$numeroFinal;
    }
    return $iniciales;
  }

  public function hideContainer($equipmentForm){
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

    $view = \View::make('quotesv2.pdf.index', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'user'=>$user,'currency_cfg'=>$currency_cfg,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);

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

  // Store

  public function store(Request $request){


    $form =  json_decode($request->input('form'));
    $info = $request->input('info');
    $equipment = json_encode($form->equipment);
    $dateQ = explode('/',$form->date);
    $since = $dateQ[0];
    $until = $dateQ[1];


    $request->request->add(['company_user_id' => \Auth::user()->company_user_id ,'custom_quote_id'=>\Auth::user()->company_user_id,'type'=>'FCL','delivery_type'=>1,'company_id'=>$form->company_id_quote,'contact_id'=>$form->company_id_quote,'contact_id' => $form->contact_id ,'validity_start'=>$since,'validity_end'=>$until,'user_id'=>\Auth::id(), 'equipment'=>$equipment , 'incoterm_id'=>'1' , 'status'=>'Draft' , 'date_issued'=>$since  ]);

    $quote= QuoteV2::create($request->all());

    foreach($info as $info){
      $info_D = json_decode($info);
      // Rates
      foreach($info_D->rates as $rate){
        $rates =   json_encode($rate->rate);
        $markups =   json_encode($rate->markups);

        $request->request->add(['contract' => $info_D->contract->id ,'origin_port_id'=> $info_D->port_origin->id,'destination_port_id'=>$info_D->port_destiny->id ,'carrier_id'=>$info_D->carrier->id ,'rates'=> $rates,'markups'=> $markups ,'currency_id'=>  $info_D->currency->id ,'total' => $rates,'quote_id'=>$quote->id]);
        $rate = AutomaticRate::create($request->all());
      }
      //CHARGES
      foreach($info_D->localorigin as $localorigin){
        foreach($localorigin as $localO){

          foreach($localO as $local){
            if($local->type != '99'){
              $arregloMontoO[] = array('c'.$local->type => $local->monto );
              $arregloMarkupsO[] = array('c'.$local->type => $local->markup );
            }
            if($local->type == '99'){
              $arregloO = array('type_id' => '1' , 'surcharge_id' => $local->surcharge_id , 'calculation_type_id' => '5' , 'currency_id' => $info_D->currency->id );
            }
          }

        }
        $arregloMontoO =  json_encode($arregloMontoO);
        $arregloMarkupsO =  json_encode($arregloMarkupsO);
        $chargeOrigin = new Charge();
        $chargeOrigin->type_id = $arregloO['type_id'] ;
        $chargeOrigin->surcharge_id = $arregloO['surcharge_id']  ;
        $chargeOrigin->calculation_type_id = $arregloO['calculation_type_id']  ;
        $chargeOrigin->amount =  $arregloMontoO  ;
        $chargeOrigin->markups = $arregloMarkupsO  ;
        $chargeOrigin->currency_id = $arregloO['currency_id']  ;
        $chargeOrigin->total =  $arregloMarkupsO ;
        $global->save();
      }
    }
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
    return view('quotesv2/search',  compact('companies','countries','harbors','prices','company_user','currencies','currency_name','incoterm'));


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
    // Fecha Contrato
    $dateRange =  $request->input('date');
    $dateRange = explode("/",$dateRange);
    $dateSince = $dateRange[0];
    $dateUntil = $dateRange[1];

    //Collection Equipment Dinamico
    $equipmentHides = $this->hideContainer($equipment);
    //Colecciones 

    //Markups Freight
    $freighPercentage = 0;
    $freighAmmount = 0;
    $freighMarkup= 0;
    // Markups Local
    $localPercentage = 0;
    $localAmmount = 0;
    $localMarkup = 0;
    $markupLocalCurre = 0;

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

    }

    // Fin Markups

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
    $array20 = array('2','4','5','6','9','10'); // id  calculation type 2 = per 20 , 4= per teu , 5 per container
    $array40 =  array('1','4','5','6','9','10'); // id  calculation type 2 = per 40 
    $array40Hc= array('3','4','5','6','9','10'); // id  calculation type 3 = per 40HC 
    $array40Nor = array('7','4','5','6','9','10');  // id  calculation type 7 = per 40NOR
    $array45 = array('8','4','5','6','9','10');  // id  calculation type 8 = per 45

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

                $monto =   $local->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$local->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup20 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'20' );
                $arregloOrigin = array_merge($arregloOrigin,$markup20);
                $collectionOrigin->push($arregloOrigin);      
                $tot_20_O  +=  $markup20['montoMarkup'];
              }
              if(in_array($local->calculationtype_id, $array40) && in_array( '40',$equipment) ){


                $monto =   $local->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$local->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40' );
                $arregloOrigin = array_merge($arregloOrigin,$markup40);
                $collectionOrigin->push($arregloOrigin);
                $tot_40_O  +=  $markup40['montoMarkup'];
              }
              if(in_array($local->calculationtype_id, $array40Hc)&& in_array( '40HC',$equipment)){

                $monto =   $local->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$local->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40hc = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40hc' );
                $arregloOrigin = array_merge($arregloOrigin,$markup40hc);
                $collectionOrigin->push($arregloOrigin);
                $tot_40hc_O  +=   $markup40hc['montoMarkup'];
              }
              if(in_array($local->calculationtype_id, $array40Nor)&& in_array( '40NOR',$equipment)){

                $monto =   $local->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$local->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40nor = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40nor' );
                $arregloOrigin = array_merge($arregloOrigin,$markup40nor);
                $collectionOrigin->push($arregloOrigin);
                $tot_40nor_O  +=  $markup40nor['montoMarkup'];
              }
              if(in_array($local->calculationtype_id, $array45) && in_array( '45',$equipment)){

                $monto =   $local->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$local->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup45 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'45' );
                $arregloOrigin = array_merge($arregloOrigin,$markup45);
                $collectionOrigin->push($arregloOrigin);
                $tot_45_O  +=  $markup45['montoMarkup'];
              }

              if(in_array($local->calculationtype_id,$arrayContainers)){
                $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => 'Per Container','contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'99' );

              }else{
                $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'99' );

              }
              $collectionOrigin->push($arregloOrigin);

            }
            //Destiny
            if($local->typedestiny_id == '2'){

              if(in_array($local->calculationtype_id, $array20) && in_array( '20',$equipment)  ){

                $monto =   $local->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$local->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup20 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloDestiny = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'20' );
                $arregloDestiny = array_merge($arregloDestiny,$markup20);
                $collectionDestiny->push($arregloDestiny);
                $tot_20_D +=  $markup20['montoMarkup'];

              }
              if(in_array($local->calculationtype_id, $array40)&& in_array( '40',$equipment)){

                $monto =   $local->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$local->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloDestiny = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40' );
                $arregloDestiny = array_merge($arregloDestiny,$markup40);
                $collectionDestiny->push($arregloDestiny);
                $tot_40_D  +=  $markup40['montoMarkup'];
              }
              if(in_array($local->calculationtype_id, $array40Hc)&& in_array( '40HC',$equipment)){
                $monto =   $local->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$local->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40hc = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloDestiny = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40hc' );
                $arregloDestiny = array_merge($arregloDestiny,$markup40hc);
                $collectionDestiny->push($arregloDestiny);
                $tot_40hc_D  +=   $markup40hc['montoMarkup'];
              }
              if(in_array($local->calculationtype_id, $array40Nor)&& in_array( '40NOR',$equipment)){
                $monto =   $local->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$local->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40nor = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloDestiny = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40nor' );
                $arregloDestiny = array_merge($arregloDestiny,$markup40nor);
                $collectionDestiny->push($arregloDestiny);
                $tot_40nor_D  +=  $markup40nor['montoMarkup'];
              }
              if(in_array($local->calculationtype_id, $array45) && in_array( '45',$equipment)){
                $monto =   $local->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$local->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup45 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloDestiny = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'45' );
                $arregloDestiny = array_merge($arregloDestiny,$markup45);
                $collectionDestiny->push($arregloDestiny);
                $tot_45_D  +=  $markup45['montoMarkup'];
              }

              if(in_array($local->calculationtype_id,$arrayContainers)){
                $arregloDestiny = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => 'Per Container','contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'99' );
              }else{
                $arregloDestiny = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'99' );
              }
              $collectionDestiny->push($arregloDestiny);
            }
            //Freight
            if($local->typedestiny_id == '3'){

              if(in_array($local->calculationtype_id, $array20) && in_array( '20',$equipment) ){

                $monto =   $local->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$local->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup20 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'20' );
                $arregloFreight = array_merge($arregloFreight,$markup20);
                $collectionFreight->push($arregloFreight);
                $totales['20F'] += $markup20['montoMarkup'];
              }
              if(in_array($local->calculationtype_id, $array40) && in_array( '40',$equipment) ){
                $monto =   $local->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$local->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40' );
                $arregloFreight = array_merge($arregloFreight,$markup40);
                $collectionFreight->push($arregloFreight);
                $totales['40F'] +=  $markup40['montoMarkup'];
              }
              if(in_array($local->calculationtype_id, $array40Hc) && in_array( '40HC',$equipment) ){
                $monto =   $local->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$local->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40hc = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40hc' );
                $arregloFreight = array_merge($arregloFreight,$markup40hc);
                $collectionFreight->push($arregloFreight);
                $totales['40hcF'] +=   $markup40hc['montoMarkup'];
              }
              if(in_array($local->calculationtype_id, $array40Nor)  && in_array( '40NOR',$equipment) ){
                $monto = $local->ammount / $rateMount;
                $monto = $this->perTeu($monto,$local->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40nor = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40nor' );
                $arregloFreight = array_merge($arregloFreight,$markup40nor);
                $collectionFreight->push($arregloFreight);
                $totales['40norF'] += $markup40nor['montoMarkup'];
              }
              if(in_array($local->calculationtype_id, $array45) && in_array( '45',$equipment) ){
                $monto =   $local->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$local->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup45 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'45' );
                $arregloFreight = array_merge($arregloFreight,$markup45);
                $collectionFreight->push($arregloFreight);
                $totales['45F'] +=  $markup45['montoMarkup'];
              }

              if(in_array($local->calculationtype_id,$arrayContainers)){
                $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => 'Per Container','contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'99' );
              }else{

                $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'99' );
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

                $monto =   $global->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$global->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup20 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloOriginG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'20' );
                $arregloOriginG = array_merge($arregloOriginG,$markup20);
                $collectionOrigin->push($arregloOriginG);
                $tot_20_O  +=  $markup20['montoMarkup'];

              }
              if(in_array($global->calculationtype_id, $array40)&& in_array( '40',$equipment)){

                $monto =   $global->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$global->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloOriginG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40' );
                $arregloOriginG = array_merge($arregloOriginG,$markup40);
                $collectionOrigin->push($arregloOriginG);
                $tot_40_O  +=   $markup40['montoMarkup'];
              }
              if(in_array($global->calculationtype_id, $array40Hc)&& in_array( '40HC',$equipment)){

                $monto =   $global->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$global->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40hc = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloOriginG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40hc' );
                $arregloOriginG = array_merge($arregloOriginG,$markup40hc);
                $collectionOrigin->push($arregloOriginG);
                $tot_40hc_O  +=   $markup40hc['montoMarkup'];
              }
              if(in_array($global->calculationtype_id, $array40Nor)&& in_array( '40NOR',$equipment)){

                $monto =   $global->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$global->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40nor = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloOriginG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40nor' );
                $arregloOriginG = array_merge($arregloOriginG,$markup40nor);
                $collectionOrigin->push($arregloOriginG);
                $tot_40nor_O  +=  $markup40nor['montoMarkup'];
              }
              if(in_array($global->calculationtype_id, $array45)&& in_array( '45',$equipment)){

                $monto =   $global->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$global->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup45 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloOriginG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'45' );
                $arregloOriginG = array_merge($arregloOriginG,$markup45);
                $collectionOrigin->push($arregloOriginG);
                $tot_45_O  +=  $markup45['montoMarkup'];
              }

              if(in_array($global->calculationtype_id,$arrayContainers)){
                $arregloOriginG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => 'Per Container','contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'99' );

              }else{
                $arregloOriginG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'99' );


              }
              $collectionOrigin->push($arregloOriginG);
            }
            //Destiny
            if($global->typedestiny_id == '2'){

              if(in_array($global->calculationtype_id, $array20) &&  in_array('20',$equipment)){
                $monto =   $global->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$global->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup20 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloDestinyG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'20' );
                $arregloDestinyG = array_merge($arregloDestinyG,$markup20);
                $collectionDestiny->push($arregloDestinyG);
                $tot_20_D +=  $markup20['montoMarkup'];
              }
              if(in_array($global->calculationtype_id, $array40)&& in_array( '40',$equipment) ){

                $monto =   $global->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$global->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloDestinyG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40' );
                $arregloDestinyG = array_merge($arregloDestinyG,$markup40);
                $collectionDestiny->push($arregloDestinyG);
                $tot_40_D  +=  $markup40['montoMarkup'];
              }
              if(in_array($global->calculationtype_id, $array40Hc)&& in_array( '40HC',$equipment) ){
                $monto =   $global->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$global->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40hc = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloDestinyG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40hc' );
                $arregloDestinyG = array_merge($arregloDestinyG,$markup40hc);
                $collectionDestiny->push($arregloDestinyG);
                $tot_40hc_D  +=   $markup40hc['montoMarkup'];
              }
              if(in_array($global->calculationtype_id, $array40Nor)&& in_array( '40NOR',$equipment) ){
                $monto =   $global->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$global->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40nor = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloDestinyG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40nor' );
                $arregloDestinyG = array_merge($arregloDestinyG,$markup40nor);
                $collectionDestiny->push($arregloDestinyG);
                $tot_40nor_D  +=  $markup40nor['montoMarkup'];
              }
              if(in_array($global->calculationtype_id, $array45)&& in_array( '45',$equipment) ){
                $monto =   $global->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$global->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup45 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloDestinyG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'45' );
                $arregloDestinyG = array_merge($arregloDestinyG,$markup45);
                $collectionDestiny->push($arregloDestinyG);
                $tot_45_D  +=  $markup45['montoMarkup'];
              }


              if(in_array($global->calculationtype_id,$arrayContainers)){
                $arregloDestinyG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => 'Per Container','contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'99' );
              }else{
                $arregloDestinyG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'99' );
              }

              $collectionDestiny->push($arregloDestinyG);
            }
            //Freight
            if($global->typedestiny_id == '3'){

              if(in_array($global->calculationtype_id, $array20) && in_array('20',$equipment)){

                $monto =   $global->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$global->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup20 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloFreightG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'20' );
                $arregloFreightG = array_merge($arregloFreightG,$markup20);
                $collectionFreight->push($arregloFreightG);
                $totales['20F'] += $markup20['montoMarkup'];

              }
              if(in_array($global->calculationtype_id, $array40) && in_array( '40',$equipment) ){
                $monto =   $global->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$global->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloFreightG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40' );
                $arregloFreightG = array_merge($arregloFreightG,$markup40);
                $collectionFreight->push($arregloFreightG);
                $totales['40F'] +=  $markup40['montoMarkup'];
              }
              if(in_array($global->calculationtype_id, $array40Hc) && in_array( '40HC',$equipment) ){
                $monto =   $global->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$global->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40hc = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloFreightG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40hc' );
                $arregloFreightG = array_merge($arregloFreightG,$markup40hc);
                $collectionFreight->push($arregloFreightG);
                $totales['40hcF'] +=   $markup40hc['montoMarkup'];
              }
              if(in_array($global->calculationtype_id, $array40Nor) && in_array( '40NOR',$equipment) ){
                $monto =   $global->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$global->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup40nor = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloFreightG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40nor' );
                $arregloFreightG = array_merge($arregloFreightG,$markup40nor);
                $collectionFreight->push($arregloFreightG);
                $totales['40norF'] += $markup40nor['montoMarkup'];
              }
              if(in_array($global->calculationtype_id, $array45) && in_array( '45',$equipment) ){
                $monto =   $global->ammount  / $rateMount ;
                $monto = $this->perTeu($monto,$global->calculationtype_id);
                $monto = number_format($monto, 2, '.', '');
                $markup45 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre);
                $arregloFreightG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'45' );
                $arregloFreightG = array_merge($arregloFreightG,$markup45);
                $collectionFreight->push($arregloFreightG);
                $totales['45F'] +=  $markup45['montoMarkup'];
              }

              if(in_array($global->calculationtype_id,$arrayContainers)){

                $arregloFreightG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => 'Per Container','contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'99' );
              }else{

                $arregloFreightG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' =>  $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'99' );

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
      $array = array('type'=>'Ocean Freight','detail'=>'Per Container','subtotal'=>$totalRates, 'total' =>$totalRates." ". $typeCurrency , 'idCurrency' => $data->currency_id,'currency_rate' => $data->currency->alphacode );
      $array = array_merge($array,$arregloRate);
      $array =  array_merge($array,$arregloRateSave);
      $collectionRate->push($array);


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

    }

    return view('quotesv2/search',  compact('arreglo','form','companies','quotes','countries','harbors','prices','company_user','currencies','currency_name','incoterm','equipmentHides'));

  }


  public function perTeu($monto,$calculation_type){
    if($calculation_type == 4){
      $monto = $monto * 2;
      return $monto;
    }else{
      return $monto;
    }
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
      $total = count($item);
      foreach($item as $items){
        if($total > 1 ){
          foreach($items as $itemsDetail){

            $monto += $itemsDetail['monto']; 
            $montoMarkup += $itemsDetail['montoMarkup']; 
            $totalMarkup += $itemsDetail['markup']; 
          }
          $itemsDetail['monto'] = number_format($monto, 2, '.', '');
          $itemsDetail['montoMarkup'] = number_format($montoMarkup, 2, '.', ''); 
          $itemsDetail['markup'] = $totalMarkup;


          $collect->push($itemsDetail);

          $monto = 0;
          $montoMarkup = 0;
          $totalMarkup = 0;

        }else{
          foreach($items as $itemsDetail){
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
