<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
use EventIntercom;
use App\Repositories\Schedules;

class QuoteAutoController extends Controller
{
  /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

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
      if($val == '40hc'){
        $hidden40hc = '';
      }
      if($val == '40nor'){
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

    //Variables de Formulario
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

    //Collection Equipment Dinamico
    $equipmentHides = $this->hideContainer($request->input('equipment'));

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
    $delivery_type = $request->input('delivery_type');
    $price_id = $request->input('price_id');
    $modality_inland = $request->modality;
    $company_id = $request->input('company_id_quote');
    // Fecha Contrato
    $dateRange =  $request->input('date');
    $dateRange = explode("/",$dateRange);
    $dateSince = $dateRange[0];
    $dateUntil = $dateRange[1];

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

    //   dd($arreglo);

    return view('quotesv2/search',  compact('arreglo','form','companies','quotes','countries','harbors','prices','company_user','currencies','currency_name','incoterm','equipmentHides'));




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
