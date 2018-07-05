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

class ContractsController extends Controller
{
  /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
  public function index()
  {
    $contracts = Contract::where('company_user_id','=',Auth::user()->company_user_id)->with('rates')->get();
    return view('contracts/index', ['arreglo' => $contracts]);
  }

  /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

  public function add()
  {
    $objcountry = new Country();
    $objcarrier = new Carrier();
    $objharbor = new Harbor();
    $objcurrency = new Currency();
    $objcalculation = new CalculationType();
    $objsurcharge = new Surcharge();
    $objtypedestiny = new TypeDestiny();

    $harbor = $objharbor->all()->pluck('name','id');
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

  /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
  public function store(Request $request)
  {
    $contract = new Contract($request->all());
    $contract->company_user_id =Auth::user()->company_user_id;
    $validation = explode('/',$request->validation_expire);
    $contract->validity = $validation[0];
    $contract->expire = $validation[1];
    $contract->save();

    $details = $request->input('origin_id');
    $detailscharges = $request->input('localcurrency_id');
    $companies = $request->input('companies');
    $users = $request->input('users');
    // For Each de los rates
    $contador = 1;
    foreach($details as $key => $value)
    {
      if(!empty($request->input('twuenty.'.$key))) {
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
        $localcharge->typedestiny_id = $request->input('changetype.'.$key2);
        $localcharge->calculationtype_id = $request->input('calculationtype.'.$key2);
        $localcharge->ammount = $request->input('ammount.'.$key2);
        $localcharge->currency_id = $request->input('localcurrency_id.'.$key2);
        $localcharge->contract()->associate($contract);
        $localcharge->save();

        $detailportOrig = $request->input('port_origlocal'.$contador);
        $detailportDest = $request->input('port_destlocal'.$contador);

        $detailcarrier = $request->input('localcarrier_id'.$contador);
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
    $contracts = Contract::where('id',$id)->with('rates','localcharges.localcharports','localcharges.localcharcarriers')->first();

    $objtypedestiny = new TypeDestiny();
    $objcountry = new Country();
    $objcarrier = new Carrier();
    $objharbor = new Harbor();
    $objcurrency = new Currency();
    $objcalculation = new CalculationType();
    $objsurcharge = new Surcharge();

    $harbor = $objharbor->all()->pluck('name','id');
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

    return view('contracts.editT', compact('contracts','harbor','country','carrier','currency','calculationT','surcharge','typedestiny','company','companies','users','user','id'));
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
    $requestForm = $request->all();
    $contract = Contract::find($id);
    $validation = explode('/',$request->validation_expire);
    $contract->validity = $validation[0];
    $contract->expire = $validation[1];
    $contract->update($requestForm);

    $details = $request->input('origin_id');
    $detailscharges = $request->input('ammount');
    $companies = $request->input('companies');
    $users = $request->input('users');
    $contador = 1;
    // for each rates 
    foreach($details as $key => $value)
    {
      if(!empty($request->input('twuenty.'.$key))) {

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

  public function updateRates(Request $request, $id){
    $requestForm = $request->all();

    $rate = Rate::find($id);
    $rate->update($requestForm);
  }

  public function UploadFileRateForContract(Request $request){

    try {
      $file = $request->file('file');
      $ext = strtolower($file->getClientOriginalExtension());

      $validator = \Validator::make(
        array('ext' => $ext),
        array('ext' => 'in:xls,xlsx,csv')
      );

      if ($validator->fails()) {
        $request->session()->flash('message.nivel', 'danger');
        $request->session()->flash('message.content', 'just archive with extension xlsx xls csv');
        return redirect()->route('contracts.edit',$request->contract_id);
      }

      //obtenemos el nombre del archivo
      $nombre = $file->getClientOriginalName();


      $dd = \Storage::disk('UpLoadFile')->put($nombre,\File::get($file));
      //dd(\Storage::disk('UpLoadFile')->url($nombre));

      $contract = $request->contract_id;
      $errors=0;
      Excel::Load(\Storage::disk('UpLoadFile')->url($nombre),function($reader) use($contract,$errors,$request) {
        foreach ($reader->get() as $book) {

          $carrier = Carrier::where('name','=',$book->carrier)->first();

          $duplicate =  Rate::where('origin_port','=',$book->origin)
            ->where('destiny_port','=',$book->destination)
            ->where('carrier_id','=',$carrier['id'])
            ->where('contract_id','=',$contract)
            ->count();

          if($duplicate <= 0){

            $twuenty = "20";
            $forty = "40";
            $fortyhc = "40hc";
            $origin = "origin";
            $destination = "destiny";
            $origB=false;
            $destiB=false;
            $carriB=false;
            $twuentyB=false;
            $fortyB=false;
            $fortyhcB=false;
            $curreB=false;

            $originV;
            $destinationV;
            $carrierV;
            $twuentyV;
            $fortyV;
            $fortyhcV;
            $currencyV;

            $currenc = Currency::where('alphacode','=',$book->currency)->first();
            $carrier = Carrier::where('name','=',$book->carrier)->first();

            if(empty($book->$origin) != true){
              $origB=true;
              $originV = $book->$origin;
            }else{
              $originV = $book->$origin.'_E';
            }

            if(empty($book->$destination) != true ){
              $destiB=true;
              $destinationV = $book->$destination;
            }else{
              $destinationV = $book->$destination.'_E';
            }

            if(empty($carrier->id) != true){
              $carriB=true;
              $carrierV = $carrier->id;
            }else{
              $carrierV = $book->carrier.'_E';
            }

            if(empty($book->$twuenty) != true ){
              $twuentyB=true;
              $twuentyV = (int)$book->$twuenty;
            }
            else{
              $twuentyV = $book->$twuenty.'_E';
            }

            if(empty($book->$forty) != true ){
              $fortyB=true;
              $fortyV = (int)$book->$forty;
            }
            else{
              $fortyV = $book->$forty.'_E';
            }

            if(empty($book->$fortyhc) != true ){
              $fortyhcB=true;
              $fortyhcV = (int)$book->$fortyhc;
            }
            else{
              $fortyhcV = $book->$fortyhc.'_E';
            }

            if(empty($currenc->id) != true){
              $curreB=true;
              $currencyV =  $currenc->id;
            }
            else{
              $currencyV = $book->currency.'_E';
            }

            if( $origB == true && $destiB == true
               && $carriB == true && $twuentyB == true
               && $fortyB == true && $fortyhcB == true
               && $curreB == true ) {

              Rate::create([
                'origin_port'   => $originV,
                'destiny_port'  => $destinationV,
                'carrier_id'    => $carrierV,
                'contract_id'   => $contract,
                'twuenty'       => $twuentyV,
                'forty'         => $fortyV,
                'fortyhc'       => $fortyhcV,
                'currency_id'   => $currencyV,
              ]);
            }
            else{
              if($curreB == true){
                $currencyV = $book->currency;
              }
              if($carriB == true){
                $carrierV = $book->carrier;
              }
              if($destiB == true){
                $destinationV = $book->$destination;
              }
              if($origB == true){
                $originV = $book->$origin;
              }

              $duplicateFail =  FailRate::where('origin_port','=',$originV)
                ->where('destiny_port','=',$destinationV)
                ->where('carrier_id','=',$carrierV)
                ->where('contract_id','=',$contract)
                ->count();
              if($duplicateFail <= 0){
                FailRate::create([
                  'origin_port'   => $originV,
                  'destiny_port'  => $destinationV,
                  'carrier_id'    => $carrierV,
                  'contract_id'   => $contract,
                  'twuenty'       => $twuentyV,
                  'forty'         => $fortyV,
                  'fortyhc'       => $fortyhcV,
                  'currency_id'   => $currencyV,
                ]);
                $errors++;
              }
            }
          }
        } //***
        if($errors > 0){
          $request->session()->flash('message.content', 'You successfully added the rate ');
          $request->session()->flash('message.nivel', 'danger');
          $request->session()->flash('message.title', 'Well done!');
          if($errors == 1){
            $request->session()->flash('message.content', $errors.' fee is not charged correctly');
          }else{
            $request->session()->flash('message.content', $errors.' Rates did not load correctly');
          }
        }
        else{
          $request->session()->flash('message.nivel', 'success');
          $request->session()->flash('message.title', 'Well done!');
        }
      });
      return redirect()->route('Failed.Rates.For.Contracts',$contract);

      //dd($res);*/

    } catch (\Illuminate\Database\QueryException $e) {

      $request->session()->flash('message.nivel', 'danger');
      $request->session()->flash('message.content', 'There was an error loading the file');
      return redirect()->route('contracts.edit',$request->contract_id);
    }
  }

  public function FailedRates($id){
    //$id se refiere al id del contracto
    $objharbor = new Harbor();
    $objcurrency = new Currency();
    $objcarrier = new Carrier();
    $carrierSelect = $objcarrier->all()->pluck('name','id');
    $harbor = $objharbor->all()->pluck('name','id');
    $currency = $objcurrency->all()->pluck('alphacode','id');
    //$rates = Rate::where('currency_id','=',$contract)->get();
    $rates = Rate::with('carrier','contract','port_origin','port_destiny')->where('contract_id','=',$id)->get();
    //dd($rates);
    $countrates = Rate::with('carrier','contract')->where('contract_id','=',$id)->count();
    $failratesFor = FailRate::where('contract_id','=',$id)->get();
    $countfailrates = FailRate::where('contract_id','=',$id)->count();

    $originV;
    $destinationV;
    $carrierV;
    $currencyV;

    $originA;
    $destinationA;
    $carrierA;
    $currencyA;
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
      $destinationA = explode("_",$failrate['destiny_port']);
      $carrierA = explode("_",$failrate['carrier_id']);
      $currencyA = explode("_",$failrate['currency_id']);

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

      $originLB = Harbor::where('id','=',$originA[0])->first();
      $destinyLB =   Harbor::where('id','=',$destinationA[0])->first();
      //            

      $colec = ['rate_id'         =>  $failrate->id,
                'contract_id'     =>  $id,

                'origin_portLb'   =>  $originLB['name'],
                'origin_port'     =>  $originA[0],   

                'destiny_portLb'  =>  $destinyLB['name'],
                'destiny_port'    =>  $destinationA[0],     

                'carrierLb'       =>  $carrierA,
                'carrierAIn'      =>  $carrAIn,

                'twuenty'         =>  $failrate['twuenty'],      
                'forty'           =>  $failrate['forty'],      
                'fortyhc'         =>  $failrate['fortyhc'],  

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

    /*dd($failrates);
        foreach($failrates as $cells){
            echo $cells['carrier_id'].' '.$cells['currency_id'].'<br>';
        }*/
    return view('contracts.FailRates',compact('rates','failrates','countfailrates','countrates','harbor','currency','carrierSelect'));
  }

  public function SaveCorrectedRate(Request $request){
    $rate_idR     =    $_REQUEST['rate_id'];
    $contract     =    $_REQUEST['contract_id'];
    $originR      =    $_REQUEST['origin'];
    $destinationR =    $_REQUEST['destination'];
    $carrierR     =    $_REQUEST['carrier'];
    $twuentyR     =    $_REQUEST['twuenty'];
    $fortyR       =    $_REQUEST['forty'];
    $fortyhcR     =    $_REQUEST['fortyhc'];
    $currencyR    =    $_REQUEST['currency'];
    $failrate = new FailRate();
    $failrate = FailRate::find($rate_idR);
    $duplicate =  Rate::where('origin_port','=',$originR)
      ->where('destiny_port','=',$destinationR)
      ->where('carrier_id','=',$carrierR)
      ->where('contract_id','=',$contract)
      ->count();
    //return $duplicate;

    if($duplicate <= 0){

      $twuentyB=false;
      $fortyB=false;
      $fortyhcB=false;

      $twuentyV;
      $fortyV;
      $fortyhcV;

      $originV = $originR;
      $destinationV = $destinationR;
      $carrierV = $carrierR;
      $currencyV =  $currencyR;

      if(empty($twuentyR) != true ){
        $twuentyB=true;
        $twuentyV = (int)$twuentyR;
      }

      if(empty($fortyR) != true ){
        $fortyB=true;
        $fortyV = (int)$fortyR;
      }

      if(empty($fortyhcR) != true ){
        $fortyhcB=true;
        $fortyhcV = (int)$fortyhcR;
      }

      if($twuentyB == true && $fortyB == true && $fortyhcB == true) {

        Rate::create([
          'origin_port'   => $originV,
          'destiny_port'  => $destinationV,
          'carrier_id'    => $carrierV,
          'contract_id'   => $contract,
          'twuenty'       => $twuentyV,
          'forty'         => $fortyV,
          'fortyhc'       => $fortyhcV,
          'currency_id'   => $currencyV,
        ]); 

        $failrate->delete();

        $origcolle   = Harbor::find($originV);
        $destcolle   = Harbor::find($destinationV);
        $carriecolle = Carrier::find($carrierV);
        $currencolle = Currency::find($currencyV);

        return $col = ['response'  => '1',
                       'origin'    => $origcolle->name,
                       'destiny'   => $destcolle->name,
                       'carrier'   => $carriecolle->name,
                       'twuenty'   => $twuentyV,
                       'forty'     => $fortyV,
                       'fortyhc'   => $fortyhcV,
                       'currency'  => $currencolle->alphacode,
                      ];

      }
      else{
        return $col = ['response'  => '0'];
      }
    } 
    else{
      return $col = ['response'  => '2'];
    }

  }

  public function UpdateRatesCorrect(Request $request){

    $rate_idR     = $_REQUEST['rate_id'];
    $contract     = $_REQUEST['contract_id'];
    $originR      = $_REQUEST['origin'];
    $destinationR = $_REQUEST['destination'];
    $carrierR     = $_REQUEST['carrier'];
    $twuentyR     = $_REQUEST['twuenty'];
    $fortyR       = $_REQUEST['forty'];
    $fortyhcR     = $_REQUEST['fortyhc'];
    $currencyR    = $_REQUEST['currency'];

    $rate = new Rate();

    /*$duplicate =  Rate::where('origin_port','=',$originR)
            ->where('destiny_port','=',$destinationR)
            ->where('carrier_id','=',$carrierR)
            ->where('contract_id','=',$contract)
            ->count();
        //return $duplicate;

        if($duplicate <= 0){*/
    $rate = Rate::find($rate_idR);
    $rate->origin_port   = $originR;
    $rate->destiny_port  = $destinationR;
    $rate->carrier_id    = $carrierR;
    $rate->twuenty       = $twuentyR;
    $rate->forty         = $fortyR;
    $rate->fortyhc       = $fortyhcR;
    $rate->currency_id   = $currencyR;
    $rate->save();

    $origcolle   = Harbor::find($rate->origin_port);
    $destcolle   = Harbor::find($rate->destiny_port);
    $carriecolle = Carrier::find($rate->carrier_id);
    $currencolle = Currency::find($rate->currency_id);

    return $col = ['response'  => '1',
                   'origin'    => $origcolle->name,
                   'destiny'   => $destcolle->name,
                   'carrier'   => $carriecolle->name,
                   'twuenty'   => $twuentyR,
                   'forty'     => $fortyR,
                   'fortyhc'   => $fortyhcR,
                   'currency'  => $currencolle->alphacode,
                  ];
    /*}
        else{
            return $col = ['response'  => '2'];
        }*/

  }

  public function DestroyRatesFailCorrect(Request $request){
    $rate_id   =  $_REQUEST['rate_id'];
    $accion    =  $_REQUEST['accion'];


      if($accion == 2){
        $rate = new Rate();
        $rate = Rate::find($rate_id);
        $rate->delete();
        return 2;
      }
    else if($accion == 1){
      $ratefail = new FailRate();
      $ratefail = FailRate::find($rate_id);
      $ratefail->delete();
      return 1;

    }
  }

  public function UploadFileSubchargeForContract(Request $request){
    //dd($request);
    try {
      $file = $request->file('file');
      $ext = strtolower($file->getClientOriginalExtension());

      $validator = \Validator::make(
        array('ext' => $ext),
        array('ext' => 'in:xls,xlsx,csv')
      );

      if ($validator->fails()) {
        $request->session()->flash('message.nivel', 'danger');
        $request->session()->flash('message.content', 'just archive with extension xlsx xls csv');
        return redirect()->route('contracts.edit',$request->contract_id);
      }

      //obtenemos el nombre del archivo
      $nombre = $file->getClientOriginalName();

      $dd = \Storage::disk('UpLoadFile')->put($nombre,\File::get($file));

      $contract = $request->contract_id;
      $errors=0;
      Excel::Load(\Storage::disk('UpLoadFile')->url($nombre),function($reader) use($contract,$errors,$request) {
        if($reader->get()->isEmpty() != true){
          LocalCharge::where('contract_id','=',$contract)
            ->delete();
          FailSurCharge::where('contract_id','=',$contract)
            ->delete();

        } else{
          $request->session()->flash('message.nivel', 'danger');
          $request->session()->flash('message.content', 'The file is it empty');
          return redirect()->route('contracts.edit',$contract);   
        }
        $i=1;
        $SurcharExist;
        $SurcharcarrierExist;
        $SurcharPortExist;

        foreach ($reader->get() as $book) {
          $surchargeBook        = "surcharge";
          $originBook           = "origin";
          $destinationBook      = "destination";
          $carrierBook          = "carrier";
          $calculationtypeBook  = "calculation_type";
          $amountBook           = "amount";
          $currencyBook         = "currency";

          $surchargeBol       = false;
          $carrierBol         = false;
          $calculationtypeBol = false;
          $currencyBol        = false;
          $ammountBol         = false;
          $originBol          = false;
          $destinationBol     = false;

          $originVar      = $book->$originBook;
          $destinationVar = $book->$destinationBook;
          $ammountVar     = (int)$book->$amountBook;
          $destinytypeVar = 3;
          $surchargeVar ="";
          $carrierVar ="";
          $calculationtypeVar ="";
          $currencyVar ="";
          $SurcharExist = "";
          $SurcharcarrierExist = "";
          $SurcharPortExist = "";
          $SurcharBootPortExist = "";

          $surcharge = Surcharge::where('name','=',$book->$surchargeBook)->where('company_user_id','=',\Auth::user()->company_user_id)->first();
          $carrier = Carrier::where('name','=',$book->$carrierBook)->first();
          $calculationtype = CalculationType::where('name','like','%'.$book->$calculationtypeBook.'%')->first();
          $currency = Currency::where('alphacode','=',$book->$currencyBook)->first();

          if(empty($surcharge) != true){
            $surchargeBol = true;
            $surchargeVar = $surcharge['id'];
          }
          else{
            $surchargeVar = $book->$surchargeBook.'_E_E';
          }

          if(empty($originVar) != true){
            $originBol = true;
          }
          else{
            $originVar = $originVar.'_E_E';
          }

          if(empty($destinationVar) != true){
            $destinationBol = true;
          }
          else{
            $destinationVar = $destinationVar.'_E_E';
          }

          if(empty($carrier) != true){
            $carrierBol = true;
            $carrierVar = $carrier['id'];
          }
          else{
            $carrierVar = $book->$carrierBook.'_E_E';
          }

          if(empty($calculationtype) != true){
            $calculationtypeBol = true;
            $calculationtypeVar = $calculationtype['id'];
          }
          else{
            $calculationtypeVar = $book->$calculationtypeBook.'_E_E';
          }

          if(empty($currency) != true){
            $currencyBol = true;
            $currencyVar = $currency['id'];
          }
          else{
            $currencyVar = $book->$currencyBook;
          }

          if(empty($ammountVar) != true){
            $ammountBol = true;
          }
          else{
            $ammountVar = $ammountVar.'_E_E';
          }

          if($surchargeBol == true 
             && $carrierBol == true 
             && $calculationtypeBol == true 
             && $currencyBol == true 
             && $originBol == true
             && $destinationBol == true
             && $ammountBol == true){ 

            $SurcharFull = LocalCharge::where('surcharge_id','=',$surchargeVar)
              ->where('typedestiny_id','=',$destinytypeVar)
              ->where('contract_id','=',$contract)
              ->where('calculationtype_id','=',$calculationtypeVar)
              ->where('ammount','=',$ammountVar)
              ->where('currency_id','=',$currencyVar)
              ->whereHas('localcharcarriers',function($q) use($carrierVar){
                $q->where('carrier_id','=',$carrierVar);
              })
              ->whereHas('localcharports', function($k) use($originVar,$destinationVar){
                $k->where('port_orig','=',$originVar);
              })->get();

            if($SurcharFull->isEmpty() != true){
              //  echo 'existe sur y port orig <br>';
              foreach($SurcharFull as $Surchar){
                $existportdest = LocalCharge::where('surcharge_id','=',$surchargeVar)
                  ->where('typedestiny_id','=',$destinytypeVar)
                  ->where('contract_id','=',$contract)
                  ->where('calculationtype_id','=',$calculationtypeVar)
                  ->where('ammount','=',$ammountVar)
                  ->where('currency_id','=',$currencyVar)
                  ->whereHas('localcharcarriers',function($q) use($carrierVar){
                    $q->where('carrier_id','=',$carrierVar);
                  })
                  ->whereHas('localcharports', function($k) use($originVar,$destinationVar){
                    $k->where('port_orig','=',$originVar)->where('port_dest','=',$destinationVar);
                  })->get();

                if($existportdest->isEmpty()){
                  //echo 'No existe puertto destno <br>';
                  foreach($SurcharFull as $Surchar){

                    LocalCharPort::create([
                      'port_orig'        => $originVar,
                      'port_dest'        => $destinationVar,
                      'localcharge_id'   => $Surchar['id']
                    ]);  
                  }



                } else{
                  //echo 'existe valor<br>';

                }

              }
            } else {
              // echo 'vacio<br>';
              $idlocalchar = LocalCharge::create([
                'surcharge_id'        => $surchargeVar,
                'typedestiny_id'      => $destinytypeVar,
                'contract_id'         => $contract,
                'calculationtype_id'  => $calculationtypeVar,
                'ammount'             => $ammountVar,
                'currency_id'         => $currencyVar,
              ]);

              LocalCharPort::create([
                'port_orig'        => $originVar,
                'port_dest'        => $destinationVar,
                'localcharge_id'   => $idlocalchar->id
              ]);

              LocalCharCarrier::create([
                'carrier_id'      => $carrierVar,
                'localcharge_id'  => $idlocalchar->id
              ]);
            }

          } else {
            if($surchargeBol == true){
              $surchargeVar = $book->$surchargeBook;
            }
            if($carrierBol == true){
              $carrierVar = $book->$carrierBook;
            }
            if($calculationtypeBol == true){
              $calculationtypeVar = $book->$calculationtypeBook;
            }
            if($currencyBol == true){
              $currencyVar = $book->$currencyBook;
            }
            if($originBol == true){
              $originVar = $book->$originBook;
            }
            if($destinationBol == true){
              $destinationVar = $book->$destinationBook;
            }
            if($ammountBol == true){
              $ammountVar = $book->$amountBook;
            }

            FailSurCharge::create([
              'surcharge_id'       => $surchargeVar,
              'port_orig'          => $originVar,
              'port_dest'          => $destinationVar,
              'typedestiny_id'     => $destinytypeVar,
              'contract_id'        => $contract,
              'calculationtype_id' => $calculationtypeVar,
              'ammount'            => $ammountVar,
              'currency_id'        => $currencyVar,
              'carrier_id'         => $carrierVar,
            ]); //*/

            $errors++;
          }

          if($errors > 0){
            $request->session()->flash('message.content', 'You successfully added the rate ');
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.title', 'Well done!');
            if($errors == 1){
              $request->session()->flash('message.content', $errors.' Subcharge is not charged correctly');
            }else{
              $request->session()->flash('message.content', $errors.' Subcharge did not load correctly');
            }
          }
          else{
            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.title', 'Well done!');
          }
          $i++;

        }
      });
      LocalCharge::onlyTrashed()->where('contract_id','=',$contract)
        ->forceDelete();
      FailSurCharge::onlyTrashed()->where('contract_id','=',$contract)
        ->forceDelete();
      return redirect()->route('Failed.Subcharge.For.Contracts',$contract);

    } catch (\Illuminate\Database\QueryException $e) {
      LocalCharge::onlyTrashed()->where('contract_id','=',$contract)
        ->restore();
      FailSurCharge::onlyTrashed()->where('contract_id','=',$contract)
        ->restore();
      $request->session()->flash('message.nivel', 'danger');
      $request->session()->flash('message.content', 'There was an error loading the file');
      return redirect()->route('contracts.edit',$request->contract_id);

    }
  }

  public function FailSubcharges($id){

    $objharbor          = new Harbor();
    $objcurrency        = new Currency();
    $objcarrier         = new Carrier();
    $objsurcharge       = new Surcharge();
    $objtypedestiny     = new TypeDestiny();
    $objCalculationType = new CalculationType();

    $typedestiny           = $objtypedestiny->all()->pluck('description','id');
    $surchargeSelect       = $objsurcharge->all()->pluck('name','id');
    $carrierSelect         = $objcarrier->all()->pluck('name','id');
    $harbor                = $objharbor->all()->pluck('name','id');
    $currency              = $objcurrency->all()->pluck('alphacode','id');
    $calculationtypeselect = $objCalculationType->all()->pluck('name','id');


    $failsurchargeS = FailSurCharge::where('contract_id','=',$id)->get();

    $failsurchargecoll = collect([]);
    foreach($failsurchargeS as $failsurcharge){

      $classdorigin           =  'color:green';
      $classddestination      =  'color:green';
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

      if($currencyC){
        $currencyA = $currencyA[0];
      }
      else{
        $currencyA      = $currencyA[0].' (error)';
        $classcurrency  = 'color:red';
      }

      $typedestinyLB    = TypeDestiny::where('id','=',$failsurcharge['typedestiny_id'])->first();

      $originLB         = Harbor::where('id','=',$originA[0])->first();
      $destinyLB        = Harbor::where('id','=',$destinationA[0])->first();

      $arreglo = [
        'surchargelb'           => $surchargeA,
        'surcharge_id'          => $surcharAin,

        'origin_portLb'         => $originLB['name'],
        'origin_port'           => $originA[0],

        'destiny_portLb'        => $destinyLB['name'],
        'destiny_port'          => $destinationA[0], 

        'carrierlb'            => $carrierA,
        'carrier_id'           => $carrAIn,
        'typedestinylb'         => $typedestinyLB['description'],
        'typedestiny'           => 3,

        'ammount'               => $ammountA,

        'calculationtypelb'     => $calculationtypeA,
        'calculationtype'       => $calculationtypeAIn,

        'currencylb'            => $currencyA,
        'currency_id'           => $currencyAIn,

        'classsurcharge'       => $classsurcharger,
        'classorigin'           => $classdorigin,
        'classdestiny'          => $classddestination,
        'classcarrier'          => $classcarrier,
        'classcalculationtype'  => $classcalculationtype,
        'classammount'          => $classammount,
        'classcurrency'         => $classcurrency,

      ];


      $failsurchargecoll->push($arreglo);
    }
    $countfailsurcharge = FailSurCharge::where('contract_id','=',$id)->count();
    $countgoodsurcharge = LocalCharge::where('contract_id','=',$id)->count();
    $goodsurcharges     = LocalCharge::where('contract_id','=',$id)->with('currency','calculationtype','surcharge','typedestiny','localcharcarriers.carrier','localcharports.portOrig','localcharports.portDest')->get();
    //  dd($goodsurcharges);
    return  view('contracts.FailSurcharge',compact('failsurchargecoll',
                                                   'countfailsurcharge',
                                                   'countgoodsurcharge',
                                                   'typedestiny',
                                                   'surchargeSelect',
                                                   'carrierSelect',
                                                   'harbor',
                                                   'currency',
                                                   'calculationtypeselect',
                                                   'goodsurcharges'
                                                  )); //*/
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

    $detailportOrig = $request->input('port_origlocal');
    $detailportDest = $request->input('port_destlocal');
    $carrier = $request->input('carrier_id');
    $deleteCarrier = LocalCharCarrier::where("localcharge_id",$id);
    $deleteCarrier->delete();
    $deletePort = LocalCharPort::where("localcharge_id",$id);
    $deletePort->delete();

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
    foreach($carrier as $key)
    {
      $detailcarrier = new LocalCharCarrier();
      $detailcarrier->carrier_id = $key;
      $detailcarrier->localcharge_id = $id;
      $detailcarrier->save();
    }

  }

  /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  public function destroy($id)
  {
    $rate = Rate::find($id);
    $rate->delete();
    return $rate;
  }

  public function destroyLocalCharges($id)
  {
    $local = LocalCharge::find($id);
    $local->delete();
  }

  public function destroyRates(Request $request,$id)
  {
    $rates = self::destroy($id);
    $request->session()->flash('message.nivel', 'success');
    $request->session()->flash('message.title', 'Well done!');
    $request->session()->flash('message.content', 'You successfully delete the rate ');
    return redirect()->action('ContractsController@index');
  }

  public function destroymsg($id)
  {
    return view('contracts/message' ,['rate_id' => $id]);
  }
}
