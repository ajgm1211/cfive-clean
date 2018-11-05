<?php

namespace App\Http\Controllers;

use App\Company;
use App\ContractUserRestriction;
use App\ContractCompanyRestriction;
use Illuminate\Http\Request;
use App\Contract;
use App\Contact;
use App\Country;
use App\Carrier;
use App\Harbor;
use App\Rate;
use App\FailRate;
use App\Currency;
use App\CalculationType;
use App\LocalCharge;
use App\Surcharge;
use App\LocalCharCarrier;
use App\LocalCharPort;
use App\User;
use App\TypeDestiny;
use App\FailSurCharge;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Excel;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\UploadFileRateRequest;
use App\FileTmp;
use App\Jobs\ImportationRatesSurchargerJob;
use Illuminate\Support\Facades\Storage;
use Yajra\Datatables\Datatables;
use App\CompanyUser;
use App\ViewLocalCharges;
use App\ViewRates;
use App\ViewContractRates;
use App\LocalCharCountry;


class ContractsController extends Controller
{

  public function index()
  {
    $arreglo = Contract::where('company_user_id','=',Auth::user()->company_user_id)->with('rates')->get();
    $contractG = Contract::where('company_user_id','=',Auth::user()->company_user_id)->get();


    return view('contracts/index', compact('arreglo','contractG'));
  }

  public function add()
  {
    $objcountry = new Country();
    $objcarrier = new Carrier();
    $objharbor = new Harbor();
    $objcurrency = new Currency();
    $objcalculation = new CalculationType();
    $objsurcharge = new Surcharge();
    $objtypedestiny = new TypeDestiny();

    $harbor = $objharbor->all()->pluck('display_name','id');
    $country = $objcountry->all()->pluck('name','id');
    $carrier = $objcarrier->all()->pluck('name','id');
    $currency = $objcurrency->all()->pluck('alphacode','id');
    $calculationT = $objcalculation->all()->pluck('name','id');
    $typedestiny = $objtypedestiny->all()->pluck('description','id');
    $surcharge = $objsurcharge->where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
    $companies = Company::where('company_user_id', '=', \Auth::user()->company_user_id)->pluck('business_name','id');
    $contacts = Contact::whereHas('company', function ($query) {
      $query->where('company_user_id', '=', \Auth::user()->company_user_id);
    })->pluck('first_name','id');
    if(Auth::user()->type == 'company' ){
      $users =  User::whereHas('companyUser', function($q)
                               {
                                 $q->where('company_user_id', '=', Auth::user()->company_user_id);
                               })->pluck('Name','id');
    }
    if(Auth::user()->type == 'admin' || Auth::user()->type == 'subuser' ){
      $users =  User::whereHas('companyUser', function($q)
                               {
                                 $q->where('company_user_id', '=', Auth::user()->company_user_id);
                               })->pluck('Name','id');
    }

    return view('contracts.addT',compact('country','carrier','harbor','currency','calculationT','surcharge','typedestiny','companies','contacts','users'));

  }

  public function create()
  {
    //
  }

  public function store(Request $request)
  {
    //  dd($request->all());
    $contract = new Contract($request->all());
    $contract->company_user_id =Auth::user()->company_user_id;
    $validation = explode('/',$request->validation_expire);
    $contract->validity = $validation[0];
    $contract->expire = $validation[1];
    $contract->save();

    $details = $request->input('currency_id');
    $detailscharges = $request->input('localcurrency_id');
    $companies = $request->input('companies');
    $users = $request->input('users');
    // For Each de los rates
    $contador = 1;
    $contadorRate = 1;


    foreach($details as $key => $value)
    {

      $rateOrig = $request->input('origin_id'.$contadorRate);
      $rateDest = $request->input('destiny_id'.$contadorRate);

      foreach($rateOrig as $Rorig => $Origvalue)
      {
        foreach($rateDest as $Rdest => $Destvalue)
        {
          $rates = new Rate();
          $rates->origin_port = $request->input('origin_id'.$contadorRate.'.'.$Rorig); 
          $rates->destiny_port = $request->input('destiny_id'.$contadorRate.'.'.$Rdest); 
          $rates->carrier_id = $request->input('carrier_id.'.$key);
          $rates->twuenty = $request->input('twuenty.'.$key);
          $rates->forty = $request->input('forty.'.$key);
          $rates->fortyhc = $request->input('fortyhc.'.$key);
          $rates->fortynor = $request->input('fortynor.'.$key);
          $rates->fortyfive = $request->input('fortyfive.'.$key);
          $rates->currency_id = $request->input('currency_id.'.$key);
          $rates->contract()->associate($contract);
          $rates->save();
        }
      }
      $contadorRate++;
    }
    // For Each de los localcharge
    foreach($detailscharges as $key2 => $value)
    {

      if(!empty($request->input('ammount.'.$key2))) {
        $localcharge = new LocalCharge();
        $localcharge->surcharge_id = $request->input('type.'.$key2);
        $localcharge->typedestiny_id = $request->input('changetype.'.$key2);
        $localcharge->calculationtype_id = $request->input('calculationtype.'.$key2);
        $localcharge->ammount = $request->input('ammount.'.$key2);
        $localcharge->currency_id = $request->input('localcurrency_id.'.$key2);
        $localcharge->contract()->associate($contract);
        $localcharge->save();

        $detailcarrier = $request->input('localcarrier_id'.$contador);
        foreach($detailcarrier as $c => $value)
        {
          $detailcarrier = new LocalCharCarrier();
          $detailcarrier->carrier_id =$request->input('localcarrier_id'.$contador.'.'.$c);
          $detailcarrier->localcharge()->associate($localcharge);
          $detailcarrier->save();
        }

        $typeroute =  $request->input('typeroute'.$contador);
        if($typeroute == 'port'){
          $detailportOrig = $request->input('port_origlocal'.$contador);
          $detailportDest = $request->input('port_destlocal'.$contador);
          foreach($detailportOrig as $orig => $value)
          {
            foreach($detailportDest as $dest => $value)
            {
              $detailport = new LocalCharPort();
              $detailport->port_orig = $request->input('port_origlocal'.$contador.'.'.$orig);
              $detailport->port_dest = $request->input('port_destlocal'.$contador.'.'.$dest);
              $detailport->localcharge()->associate($localcharge);
              $detailport->save();
            }

          }
        }elseif($typeroute == 'country'){

          $detailcountryOrig = $request->input('country_orig'.$contador);
          $detailcountryDest = $request->input('country_dest'.$contador);
          foreach($detailcountryOrig as $origC => $value)
          {
            foreach($detailcountryDest as $destC => $value)
            {
              $detailcountry = new LocalCharCountry();
              $detailcountry->country_orig =$request->input('country_orig'.$contador.'.'.$origC);
              $detailcountry->country_dest = $request->input('country_dest'.$contador.'.'.$destC);
              $detailcountry->localcharge()->associate($localcharge);
              $detailcountry->save();
            }
          }

        }


        $contador++;
      }
    }

    if(!empty($companies)){
      foreach($companies as $key3 => $value)
      {
        $contract_company_restriction = new ContractCompanyRestriction();
        $contract_company_restriction->company_id=$value;
        $contract_company_restriction->contract_id=$contract->id;
        $contract_company_restriction->save();
      }
    }

    if(!empty($users)){
      foreach($users as $key4 => $value)
      {
        $contract_client_restriction = new ContractUserRestriction();
        $contract_client_restriction->user_id=$value;
        $contract_client_restriction->contract_id=$contract->id;
        $contract_client_restriction->save();
      }
    }

    $request->session()->flash('message.nivel', 'success');
    $request->session()->flash('message.title', 'Well done!');
    $request->session()->flash('message.content', 'You successfully add this contract.');

    return redirect()->action('ContractsController@index');

  }


  public function show($id)
  {
    //
  }



  // FUNCIONES PARA EL DATATABLE
  public function data($id){

    $localchar = new  ViewLocalCharges();
    $data = $localchar->select('id','surcharge','port_orig','port_dest','country_orig','country_dest','changetype','carrier','calculation_type','ammount','currency')->where('contract_id',$id);

    return \DataTables::of($data)
      ->addColumn('origin', function ($data) {
        if($data['country_orig'] != null){
          return $data['country_orig'];
        }else{
          return $data['port_orig'];
        }

      })
      ->addColumn('destiny', function ($data) {
        if($data['country_dest'] != null){
          return $data['country_dest'];
        }else{
          return $data['port_dest'];
        }
      })
      ->addColumn('options', function ($data) {
        return " <a   class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test'  title='Edit '  onclick='AbrirModal(\"editLocalCharge\",$data[id])'>
          <i class='la la-edit'></i>
          </a>
            <a  data-local-id='$data[id]'    class='m_sweetalert_demo_8  m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill'  title='delete' >
          <i id='rm_l' class='la la-times-circle'></i>
        ";
      }) ->setRowId('id')->rawColumns(['options'])->make(true);
  }// local charges en edit

  public function dataRates($id){


    $rate = new  ViewRates();
    $data = $rate->select('id','port_orig','port_dest','carrier','twuenty','forty','fortyhc','fortynor','fortyfive','currency')->where('contract_id',$id);

    return \DataTables::of($data)
      ->addColumn('options', function ($data) {
        return " <a   class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test' title='Edit'  onclick='AbrirModal(\"editRate\",$data[id])'>
          <i class='la la-edit'></i>
          </a>
             <a id='delete-rate' data-rate-id='$data[id]' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' title='Delete' >
                    <i  class='la la-times-circle'></i>
                    </a>

        ";
      }) ->setRowId('id')->rawColumns(['options'])->make(true);

  }

  public function contractRates(){
    $contractRate = new  ViewContractRates();
    $data = $contractRate->select('id','contract_id','name','number','validy','expire','status','port_orig','port_dest','carrier','twuenty','forty','fortyhc','fortynor','fortyfive','currency')->where('company_user_id', Auth::user()->company_user_id);


    return \DataTables::of($data)

      ->addColumn('validity', function ($data) {
        return $data['validy'] ." / ".$data['expire'];
      })
      ->addColumn('options', function ($data) {
        return "<a href='contracts/".setearRouteKey($data['contract_id'])."/edit' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill'  title='Edit '>
                      <i class='la la-edit'></i>
                    </a>

                    <a href='#' id='delete-rate' data-rate-id='$data[id]' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' title='Delete' >
                    <i  class='la la-times-circle'></i>
                    </a>

        ";
      }) ->setRowId('id')->rawColumns(['options'])->make(true);

  }

  public function contractTable(){

    $contractG = Contract::where('company_user_id','=',Auth::user()->company_user_id)->get();



    return \DataTables::collection($contractG)

      ->addColumn('options', function (Contract $contractG) {
        return "      <a href='contracts/".setearRouteKey($contractG->id)."/edit' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill'  title='Edit '>
                      <i class='la la-edit'></i>
                    </a>
                    <a  id='delete-contract' data-contract-id='$contractG->id' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill'  title='Delete'>
                      <i class='la la-eraser'></i>
                    </a>

        ";
      }) ->setRowId('id')->rawColumns(['options'])->make(true);

  }



  public function edit(Request $request,$id)
  {
    $id = obtenerRouteKey($id);
    $contracts = Contract::where('id',$id)->first();

    $objtypedestiny = new TypeDestiny();
    $objcountry = new Country();
    $objcarrier = new Carrier();
    $objharbor = new Harbor();
    $objcurrency = new Currency();
    $objcalculation = new CalculationType();
    $objsurcharge = new Surcharge();

    $harbor = $objharbor->all()->pluck('display_name','id');
    $country = $objcountry->all()->pluck('name','id');
    $carrier = $objcarrier->all()->pluck('name','id');
    $currency = $objcurrency->all()->pluck('alphacode','id');
    $calculationT = $objcalculation->all()->pluck('name','id');
    $typedestiny = $objtypedestiny->all()->pluck('description','id');
    $surcharge = $objsurcharge->where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
    $company_restriction = ContractCompanyRestriction::where('contract_id',$contracts->id)->first();
    $user_restriction = ContractUserRestriction::where('contract_id',$contracts->id)->first();
    if(!empty($company_restriction)){
      $company = Company::where('id',$company_restriction->company_id)->select('id')->first();
    }
    if(!empty($user_restriction)){
      $user = User::where('id',$user_restriction->user_id)->select('id')->first();
    }
    $companies = Company::where('company_user_id', '=', \Auth::user()->company_user_id)->pluck('business_name','id');
    if(Auth::user()->type == 'company' ){
      $users =  User::whereHas('companyUser', function($q)
                               {
                                 $q->where('company_user_id', '=', Auth::user()->company_user_id);
                               })->pluck('Name','id');
    }
    if(Auth::user()->type == 'admin' || Auth::user()->type == 'subuser' ){
      $users =  User::whereHas('companyUser', function($q)
                               {
                                 $q->where('company_user_id', '=', Auth::user()->company_user_id);
                               })->pluck('Name','id');
    }
    //dd($contracts);
    if (!$request->session()->exists('activeS')) {
      $request->session()->flash('activeR', 'active');
    }

    return view('contracts.editT', compact('contracts','harbor','country','carrier','currency','calculationT','surcharge','typedestiny','company','companies','users','user','id'));
  }

  public function update(Request $request, $id)
  {
    $requestForm = $request->all();
    $contract = Contract::find($id);
    $validation = explode('/',$request->validation_expire);
    $contract->validity = $validation[0];
    $contract->expire = $validation[1];
    $contract->update($requestForm);

    $details = $request->input('origin_id');
    $detailscharges =  $request->input('localcurrency_id');//  $request->input('ammount');
    $companies = $request->input('companies');
    $users = $request->input('users');
    $contador = 1;
    // for each rates 
    foreach($details as $key => $value)
    {
      if(is_numeric($request->input('twuenty.'.$key))) {

        $rates = new Rate();
        $rates->origin_port = $request->input('origin_id.'.$key);
        $rates->destiny_port = $request->input('destiny_id.'.$key);
        $rates->carrier_id = $request->input('carrier_id.'.$key);
        $rates->twuenty = $request->input('twuenty.'.$key);
        $rates->forty = $request->input('forty.'.$key);
        $rates->fortyhc = $request->input('fortyhc.'.$key);
        $rates->currency_id = $request->input('currency_id.'.$key);
        $rates->contract()->associate($contract);
        $rates->save();

      }
    }

    // For Each de los localcharge

    foreach($detailscharges as $key2 => $value)
    {
      if(!empty($request->input('ammount.'.$key2))) {
        $localcharge = new LocalCharge();
        $localcharge->surcharge_id = $request->input('type.'.$key2);
        $localcharge->typedestiny_id  = $request->input('changetype.'.$key2);
        $localcharge->calculationtype_id = $request->input('calculationtype.'.$key2);
        $localcharge->ammount = $request->input('ammount.'.$key2);
        $localcharge->currency_id = $request->input('localcurrency_id.'.$key2);
        $localcharge->contract()->associate($contract);
        $localcharge->save();
        $detailportOrig = $request->input('port_origlocal'.$contador);
        $detailportDest = $request->input('port_destlocal'.$contador);
        $detailcarrier = $request->input('localcarrier_id'.$contador);
        $companies = $request->input('companies');
        foreach($detailcarrier as $c => $value)
        {
          $detailcarrier = new LocalCharCarrier();
          $detailcarrier->carrier_id =$request->input('localcarrier_id'.$contador.'.'.$c);
          $detailcarrier->localcharge()->associate($localcharge);
          $detailcarrier->save();
        }
        foreach($detailportOrig as $orig => $value)
        {
          foreach($detailportDest as $dest => $value)
          {


            $detailport = new LocalCharPort();
            $detailport->port_orig = $request->input('port_origlocal'.$contador.'.'.$orig);
            $detailport->port_dest = $request->input('port_destlocal'.$contador.'.'.$dest);
            $detailport->localcharge()->associate($localcharge);
            $detailport->save();
          }

        }
        $contador++;
      }
    }

    if(!empty($companies)){
      ContractCompanyRestriction::where('contract_id',$contract->id)->delete();

      foreach($companies as $key3 => $value)
      {
        $contract_company_restriction = new ContractCompanyRestriction();
        $contract_company_restriction->company_id=$value;
        $contract_company_restriction->contract_id=$contract->id;
        $contract_company_restriction->save();
      }
    }

    if(!empty($users)){
      ContractUserRestriction::where('contract_id',$contract->id)->delete();

      foreach($users as $key4 => $value)
      {
        $contract_client_restriction = new ContractUserRestriction();
        $contract_client_restriction->user_id=$value;
        $contract_client_restriction->contract_id=$contract->id;
        $contract_client_restriction->save();
      }

    }

    $request->session()->flash('message.nivel', 'success');
    $request->session()->flash('message.title', 'Well done!');
    $request->session()->flash('message.content', 'You successfully update this contract.');
    return redirect()->action('ContractsController@index');

  }

  public function addRates($id){
    $objcarrier = new Carrier();
    $objharbor = new Harbor();
    $objcurrency = new Currency();
    $harbor = $objharbor->all()->pluck('display_name','id');
    $carrier = $objcarrier->all()->pluck('name','id');
    $currency = $objcurrency->all()->pluck('alphacode','id');

    return view('contracts.addRates', compact('harbor','carrier','currency','id'));
  }
  public function storeRates(Request $request,$id){




    $rateOrig = $request->input('origin_port');
    $rateDest = $request->input('destiny_port');

    foreach($rateOrig as $Rorig => $Origvalue)
    {
      foreach($rateDest as $Rdest => $Destvalue)
      {
        
        $rates = new Rate();
        $rates->origin_port =$Origvalue;
        $rates->destiny_port =$Destvalue;
        $rates->carrier_id = $request->input('carrier_id');
        $rates->twuenty = $request->input('twuenty');
        $rates->forty = $request->input('forty');
        $rates->fortyhc = $request->input('fortyhc');
        $rates->fortynor = $request->input('fortynor');
        $rates->fortyfive = $request->input('fortyfive');
        $rates->currency_id = $request->input('currency_id');
        $rates->contract_id = $id;
        $rates->save();
      }
    }
    return redirect()->back()->with('ratesSave','true');
  }
  public function editRates($id){
    $objcarrier = new Carrier();
    $objharbor = new Harbor();
    $objcurrency = new Currency();
    $harbor = $objharbor->all()->pluck('display_name','id');
    $carrier = $objcarrier->all()->pluck('name','id');
    $currency = $objcurrency->all()->pluck('alphacode','id');
    $rates = Rate::find($id);
    return view('contracts.editRates', compact('rates','harbor','carrier','currency'));
  }
  public function updateRates(Request $request, $id){
    $requestForm = $request->all();
    $rate = Rate::find($id);
    $rate->update($requestForm);
    return redirect()->back()->with('editRate','true');
  }

  public function addLocalChar($id){
    $objcarrier = new Carrier();
    $objharbor = new Harbor();
    $objcurrency = new Currency();
    $objcalculation = new CalculationType();
    $objsurcharge = new Surcharge();
    $objtypedestiny = new TypeDestiny();
    $countries = Country::pluck('name','id');
    $harbor = $objharbor->all()->pluck('display_name','id');
    $carrier = $objcarrier->all()->pluck('name','id');
    $currency = $objcurrency->all()->pluck('alphacode','id');
    $calculationT = $objcalculation->all()->pluck('name','id');
    $typedestiny = $objtypedestiny->all()->pluck('description','id');
    $surcharge = $objsurcharge->where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
    return view('contracts.addLocalCharge', compact('harbor','carrier','currency','calculationT','typedestiny','surcharge','id','countries'));

  }
  public function storeLocalChar(Request $request,$id){
    $localcharge = new LocalCharge();
    $request->request->add(['contract_id' => $id]);
    $localcharge =  $localcharge->create($request->all());

    $detailcarrier = $request->input('carrier_id');
    foreach($detailcarrier as $c => $value)
    {
      $detailcarrier = new LocalCharCarrier();
      $detailcarrier->carrier_id =$value;
      $detailcarrier->localcharge()->associate($localcharge);
      $detailcarrier->save();
    }
    $typeroute =  $request->input('typeroute');
    if($typeroute == 'port'){
      $detailportOrig = $request->input('port_origlocal');
      $detailportDest = $request->input('port_destlocal');
      foreach($detailportOrig as $orig => $valueOrig){
        foreach($detailportDest as $dest => $valueDest){
          $detailport = new LocalCharPort();
          $detailport->port_orig =$valueOrig;
          $detailport->port_dest =$valueDest;
          $detailport->localcharge()->associate($localcharge);
          $detailport->save();
        }
      }
    }elseif($typeroute == 'country'){

      $detailcountryOrig = $request->input('country_orig');
      $detailcountryDest = $request->input('country_dest');

      foreach($detailcountryOrig as $orig => $valueOrigC){
        foreach($detailcountryDest as $dest => $valueDestC){
          $detailcountry = new LocalCharCountry();
          $detailcountry->country_orig =$valueOrigC;
          $detailcountry->country_dest = $valueDestC;
          $detailcountry->localcharge()->associate($localcharge);
          $detailcountry->save();

        }
      }


    }
    return redirect()->back()->with('localcharSave','true')->with('activeS','active');
  }
  public function editLocalChar($id){
    $objcarrier = new Carrier();
    $objharbor = new Harbor();
    $objcurrency = new Currency();
    $objtypedestiny = new TypeDestiny();
    $objcalculation = new CalculationType();
    $objsurcharge = new Surcharge();
    $countries = Country::pluck('name','id');

    $calculationT = $objcalculation->all()->pluck('name','id');
    $typedestiny = $objtypedestiny->all()->pluck('description','id');
    $surcharge = $objsurcharge->where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
    $harbor = $objharbor->all()->pluck('display_name','id');
    $carrier = $objcarrier->all()->pluck('name','id');
    $currency = $objcurrency->all()->pluck('alphacode','id');
    $localcharges = LocalCharge::find($id);
    return view('contracts.editLocalCharge', compact('localcharges','harbor','carrier','currency','calculationT','typedestiny','surcharge','countries'));
  }
  public function updateLocalChar(Request $request, $id)
  {
    $localC = LocalCharge::find($id);

    $localC->surcharge_id = $request->input('surcharge_id');
    $localC->typedestiny_id  = $request->input('changetype');
    $localC->calculationtype_id = $request->input('calculationtype_id');
    $localC->ammount = $request->input('ammount');
    $localC->currency_id = $request->input('currency_id');
    $localC->update();


    $carrier = $request->input('carrier_id');
    $deleteCarrier = LocalCharCarrier::where("localcharge_id",$id);
    $deleteCarrier->delete();
    $deletePort = LocalCharPort::where("localcharge_id",$id);
    $deletePort->delete();
    $deleteCountry = LocalCharCountry::where("localcharge_id",$id);
    $deleteCountry->delete();
    $typerate =  $request->input('typeroute');
    if($typerate == 'port'){
      $detailportOrig = $request->input('port_origlocal');
      $detailportDest = $request->input('port_destlocal');
      foreach($detailportOrig as $orig => $valueOrig)
      {
        foreach($detailportDest as $dest => $valueDest)
        {
          $detailport = new LocalCharPort();
          $detailport->port_orig = $valueOrig;
          $detailport->port_dest = $valueDest;
          $detailport->localcharge_id = $id;
          $detailport->save();
        }
      }
    }elseif($typerate == 'country'){
      $detailCountrytOrig =$request->input('country_orig');
      $detailCountryDest = $request->input('country_dest');
      foreach($detailCountrytOrig as $orig => $valueOrigC)
      {
        foreach($detailCountryDest as $dest => $valueDestC)
        {
          $detailcountry = new LocalCharCountry();
          $detailcountry->country_orig = $valueOrigC;
          $detailcountry->country_dest =  $valueDestC;
          $detailcountry->localcharge_id = $id;
          $detailcountry->save();
        }
      }


    }

    foreach($carrier as $key)
    {
      $detailcarrier = new LocalCharCarrier();
      $detailcarrier->carrier_id = $key;
      $detailcarrier->localcharge_id = $id;
      $detailcarrier->save();
    }
    return redirect()->back()->with('localchar','true')->with('activeS','active');
  }


  public function destroy($id)
  {
    $rate = Rate::find($id);
    $rate->delete();
    return $rate;
  }

  public function deleteContract($id){

    $contract = Contract::find($id);
    if(isset($contract->rates)){
      if(isset($contract->localcharges)){
        return response()->json(['message' => count($contract->rates),'local' => count($contract->localcharges) ]);
      }else{
        return response()->json(['message' => count($contract->rates),'local' => 0]);
      }
    }
    return response()->json(['message' => 'SN','local' => 0]);
  }
  public function destroyContract($id){

    try { 

      $FileTmp = FileTmp::where('contract_id',$id)->first();
      if(count($FileTmp) > 0){
        Storage::Delete($FileTmp->name_file);
        $FileTmp->delete();
      }

      $contract = Contract::find($id);
      $contract->delete();

      return response()->json(['message' => 'Ok']);
    }
    catch (\Exception $e) {
      return response()->json(['message' => $e]);
    }


  }

  public function destroyLocalCharges($id)
  {
    $local = LocalCharge::find($id);
    $local->forceDelete();
  }

  public function destroyRates(Request $request,$id)
  {

    $rate = Rate::find($id);
    $rate->forceDelete();
    return $rate;

  }

  public function destroymsg($id)
  {
    return view('contracts/message' ,['rate_id' => $id]);
  }

  public function failRatesSurchrgesForNewContracts($id){

    $objharbor          = new Harbor();
    $objcurrency        = new Currency();
    $objcarrier         = new Carrier();
    $objsurcharge       = new Surcharge();
    $objtypedestiny     = new TypeDestiny();
    $objCalculationType = new CalculationType();
    $objsurcharge       = new Surcharge();

    $typedestiny           = $objtypedestiny->all()->pluck('description','id');
    $surchargeSelect       = $objsurcharge->all()->pluck('name','id');
    $carrierSelect         = $objcarrier->all()->pluck('name','id');
    $harbor                = $objharbor->all()->pluck('display_name','id');
    $currency              = $objcurrency->all()->pluck('alphacode','id');
    $calculationtypeselect = $objCalculationType->all()->pluck('name','id');
    $typedestiny           = $objtypedestiny->all()->pluck('description','id');
    $surchargeSelect       = $objsurcharge->where('company_user_id','=', \Auth::user()->company_user_id)->pluck('name','id');
    $calculationtypeselect = $objCalculationType->all()->pluck('name','id');

    //------------------------------- Rates ---------------------------------------------------------------

    $countrates = Rate::with('carrier','contract')->where('contract_id','=',$id)->count();
    $countfailrates = FailRate::where('contract_id','=',$id)->count();

    $rates = Rate::with('carrier','contract','port_origin','port_destiny')->where('contract_id','=',$id)->get();
    $failratesFor = FailRate::where('contract_id','=',$id)->get();

    $originV;
    $destinationV;
    $carrierV;
    $currencyV;
    $originA;
    $destinationA;
    $carrierA;
    $currencyA;
    $twuentyA;
    $fortyA;
    $fortyhcA;
    $failrates = collect([]);

    foreach( $failratesFor as $failrate){
      $carrAIn;
      $pruebacurre = "";
      $classdorigin='color:green';
      $classddestination='color:green';
      $classcarrier='color:green';
      $classcurrency='color:green';
      $classtwuenty ='color:green';
      $classforty ='color:green';
      $classfortyhc ='color:green';
      $originA =  explode("_",$failrate['origin_port']);
      //dd($originA);
      $destinationA = explode("_",$failrate['destiny_port']);
      $carrierA = explode("_",$failrate['carrier_id']);
      $currencyA = explode("_",$failrate['currency_id']);
      $twuentyA = explode("_",$failrate['twuenty']);
      $fortyA = explode("_",$failrate['forty']);
      $fortyhcA = explode("_",$failrate['fortyhc']);
      $originOb  = Harbor::where('varation->type','like','%'.strtolower($originA[0]).'%')
        ->first();
      $originAIn = $originOb['id'];
      $originC   = count($originA);
      if($originC <= 1){
        $originA = $originOb['name'];
      } else{
        $originA = $originA[0].' (error)';
        $classdorigin='color:red';
      }
      $destinationOb  = Harbor::where('varation->type','like','%'.strtolower($destinationA[0]).'%')
        ->first();
      $destinationAIn = $destinationOb['id'];
      $destinationC   = count($destinationA);
      if($destinationC <= 1){
        $destinationA = $destinationOb['name'];
      } else{
        $destinationA = $destinationA[0].' (error)';
        $classddestination='color:red';
      }
      $twuentyC   = count($twuentyA);
      if($twuentyC <= 1){
        $twuentyA = $twuentyA[0];
      } else{
        $twuentyA = $twuentyA[0].' (error)';
        $classtwuenty='color:red';
      }
      $fortyC   = count($fortyA);
      if($fortyC <= 1){
        $fortyA = $fortyA[0];
      } else{
        $fortyA = $fortyA[0].' (error)';
        $classforty='color:red';
      }
      $fortyhcC   = count($fortyhcA);
      if($fortyhcC <= 1){
        $fortyhcA = $fortyhcA[0];
      } else{
        $fortyhcA = $fortyhcA[0].' (error)';
        $classfortyhc='color:red';
      }
      $carrierOb =   Carrier::where('name','=',$carrierA[0])->first();
      $carrAIn = $carrierOb['id'];
      $carrierC = count($carrierA);
      if($carrierC <= 1){
        //dd($carrierAIn);
        $carrierA = $carrierA[0];
      }
      else{
        $carrierA = $carrierA[0].' (error)';
        $classcarrier='color:red';
      }
      $currencyC = count($currencyA);
      if($currencyC <= 1){
        $currenc = Currency::where('alphacode','=',$currencyA[0])->first();
        $pruebacurre = $currenc['id'];
        $currencyA = $currencyA[0];
      }
      else{
        $currencyA = $currencyA[0].' (error)';
        $classcurrency='color:red';
      }        
      $colec = ['rate_id'         =>  $failrate->id,
                'contract_id'     =>  $id,
                'origin_portLb'   =>  $originA,
                'origin_port'     =>  $originAIn,   
                'destiny_portLb'  =>  $destinationA,
                'destiny_port'    =>  $destinationAIn,     
                'carrierLb'       =>  $carrierA,
                'carrierAIn'      =>  $carrAIn,
                'twuenty'         =>  $twuentyA,      
                'forty'           =>  $fortyA,      
                'fortyhc'         =>  $fortyhcA,  
                'currency_id'     =>  $currencyA,
                'currencyAIn'     =>  $pruebacurre,
                'classorigin'     =>  $classdorigin,
                'classdestiny'    =>  $classddestination,
                'classcarrier'    =>  $classcarrier,
                'classtwuenty'    =>  $classtwuenty,
                'classforty'      =>  $classforty,
                'classfortyhc'    =>  $classfortyhc,
                'classcurrency'   =>  $classcurrency
               ];
      $pruebacurre = "";
      $carrAIn = "";
      $failrates->push($colec);
    }

    //------------------------------- Surcharge -----------------------------------------------------------

    $countfailsurcharge = FailSurCharge::where('contract_id','=',$id)->count();
    $countgoodsurcharge = LocalCharge::where('contract_id','=',$id)->count();

    $goodsurcharges     = LocalCharge::where('contract_id','=',$id)->with('currency','calculationtype','surcharge','typedestiny','localcharcarriers.carrier','localcharports.portOrig','localcharports.portDest')->get();
    $failsurchargeS = FailSurCharge::where('contract_id','=',$id)->get();

    $failsurchargecoll = collect([]);
    foreach($failsurchargeS as $failsurcharge){
      $classdorigin           =  'color:green';
      $classddestination      =  'color:green';
      $classtypedestiny       =  'color:green';
      $classcarrier           =  'color:green';
      $classsurcharger        =  'color:green';
      $classcalculationtype   =  'color:green';
      $classammount           =  'color:green';
      $classcurrency          =  'color:green';
      $surchargeA         =  explode("_",$failsurcharge['surcharge_id']);
      $originA            =  explode("_",$failsurcharge['port_orig']);
      $destinationA       =  explode("_",$failsurcharge['port_dest']);
      $calculationtypeA   =  explode("_",$failsurcharge['calculationtype_id']);
      $ammountA           =  explode("_",$failsurcharge['ammount']);
      $currencyA          =  explode("_",$failsurcharge['currency_id']);
      $carrierA           =  explode("_",$failsurcharge['carrier_id']);
      $originOb  = Harbor::where('varation->type','like','%'.strtolower($originA[0]).'%')
        ->first();
      $originAIn = $originOb['id'];
      $originC   = count($originA);
      if($originC <= 1){
        $originA = $originOb['name'];
      } else{
        $originA = $originA[0].' (error)';
        $classdorigin='color:red';
      }
      $destinationOb  = Harbor::where('varation->type','like','%'.strtolower($destinationA[0]).'%')
        ->first();
      $destinationAIn = $destinationOb['id'];
      $destinationC   = count($destinationA);
      if($destinationC <= 1){
        $destinationA = $destinationOb['name'];
      } else{
        $destinationA = $destinationA[0].' (error)';
        $classddestination='color:red';
      }
      $surchargeOb = Surcharge::where('name','=',$surchargeA[0])->where('company_user_id','=',\Auth::user()->company_user_id)->first();
      $surcharAin  = $surchargeOb['id'];
      $surchargeC = count($surchargeA);
      if($surchargeC <= 1){
        $surchargeA = $surchargeA[0];
      }
      else{
        $surchargeA         = $surchargeA[0].' (error)';
        $classsurcharger    = 'color:red';
      }
      $carrierOb =   Carrier::where('name','=',$carrierA[0])->first();
      $carrAIn = $carrierOb['id'];
      $carrierC = count($carrierA);
      if($carrierC <= 1){
        $carrierA = $carrierA[0];
      }
      else{
        $carrierA       = $carrierA[0].' (error)';
        $classcarrier   ='color:red';
      }
      $calculationtypeOb  = CalculationType::where('name','=',$calculationtypeA[0])->first();
      $calculationtypeAIn = $calculationtypeOb['id'];
      $calculationtypeC   = count($calculationtypeA);
      if($calculationtypeC <= 1){
        $calculationtypeA = $calculationtypeA[0];
      }
      else{
        $calculationtypeA       = $calculationtypeA[0].' (error)';
        $classcalculationtype   = 'color:red';
      }
      $ammountC = count($ammountA);
      if($ammountC <= 1){
        $ammountA = $failsurcharge->ammount;
      }
      else{
        $ammountA       = $ammountA[0].' (error)';
        $classammount   = 'color:red';
      }

      $currencyOb   = Currency::where('alphacode','=',$currencyA[0])->first();
      $currencyAIn  = $currencyOb['id'];
      $currencyC    = count($currencyA);
      if($currencyC <= 1){
        $currencyA = $currencyA[0];
      }
      else{
        $currencyA      = $currencyA[0].' (error)';
        $classcurrency  = 'color:red';
      }

      $typedestinyLB    = TypeDestiny::where('description','=',$failsurcharge['typedestiny_id'])->first();

      $destinyLB        = Harbor::where('id','=',$destinationA[0])->first();
      ////////////////////////////////////////////////////////////////////////////////////
      $arreglo = [
        'failSrucharge_id'      => $failsurcharge->id,
        'surchargelb'           => $surchargeA,
        'surcharge_id'          => $surcharAin,
        'origin_portLb'         => $originA,
        'origin_port'           => $originAIn,
        'destiny_portLb'        => $destinationA,
        'destiny_port'          => $destinationAIn, 
        'carrierlb'             => $carrierA,
        'carrier_id'            => $carrAIn,
        'typedestinylb'         => $typedestinyLB['description'],
        'typedestiny'           => 3,
        'ammount'               => $ammountA,
        'calculationtypelb'     => $calculationtypeA,
        'calculationtype'       => $calculationtypeAIn,
        'currencylb'            => $currencyA,
        'currency_id'           => $currencyAIn,
        'classsurcharge'        => $classsurcharger,
        'classorigin'           => $classdorigin,
        'classdestiny'          => $classddestination,
        'classtypedestiny'      => $classtypedestiny,
        'classcarrier'          => $classcarrier,
        'classcalculationtype'  => $classcalculationtype,
        'classammount'          => $classammount,
        'classcurrency'         => $classcurrency,
      ];
      //dd($arreglo);
      $failsurchargecoll->push($arreglo);
    }
    //dd($failsurchargecoll);
    //------------------------------------ Return ---------------------------------------------------------

    return  view('contracts.FailRatesSurchargerNewC',compact('rates',
                                                             'failrates',
                                                             'countfailrates',
                                                             'countrates',
                                                             'goodsurcharges',
                                                             'failsurchargecoll',
                                                             'countfailsurcharge',
                                                             'countgoodsurcharge',
                                                             'typedestiny',
                                                             'surchargeSelect',
                                                             'carrierSelect',
                                                             'harbor',
                                                             'currency',
                                                             'calculationtypeselect',
                                                             'id'
                                                            )); //*/
  }


}