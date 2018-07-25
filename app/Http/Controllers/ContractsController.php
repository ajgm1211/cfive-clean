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
use Illuminate\Support\Facades\Storage;

class ContractsController extends Controller
{
  /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

  public function index()
  {
    $arreglo = Contract::where('company_user_id','=',Auth::user()->company_user_id)->with('rates')->get();
    $contractG = Contract::where('company_user_id','=',Auth::user()->company_user_id)->get();


    return view('contracts/index', compact('arreglo','contractG'));
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

    $nombre='';

    try {

      $now = new \DateTime();
      $now = $now->format('dmY_His');
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
      $nombre = $now.'_'.$nombre;

      $dd = \Storage::disk('UpLoadFile')->put($nombre,\File::get($file));
      //dd(\Storage::disk('UpLoadFile')->url($nombre));

      $contract = $request->contract_id;
      $errors=0;
      Excel::Load(\Storage::disk('UpLoadFile')->url($nombre),function($reader) use($contract,$errors,$request) {
        if($reader->get()->isEmpty() != true){
          Rate::where('contract_id','=',$contract)
            ->delete();
          FailRate::where('contract_id','=',$contract)
            ->delete();

        } else{
          $request->session()->flash('message.nivel', 'danger');
          $request->session()->flash('message.content', 'The file is it empty');
          return redirect()->route('contracts.edit',$contract);   
        }
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
            $originExits = Harbor::where('varation->type','like','%'.strtolower($book->$origin).'%')
              ->get();

            if(count($originExits) == 1){
              $origB=true;
              foreach($originExits as $originRc){
                $originV = $originRc['id'];
              }
            }else{
              $originV = $book->$origin.'_E_E';
            }

            $destinationExits = Harbor::where('varation->type','like','%'.strtolower($book->$destination).'%')
              ->get();

            if(count($destinationExits) == 1){
              $destiB=true;
              foreach($destinationExits as $destinationRc){
                $destinationV = $destinationRc['id'];
              }
            }else{
              $destinationV = $book->$destination.'_E_E';
            }


            if(empty($carrier->id) != true){
              $carriB=true;
              $carrierV = $carrier->id;
            }else{
              $carrierV = $book->carrier.'_E_E';
            }

            if(empty($book->$twuenty) != true ){
              $twuentyB=true;
              $twuentyV = (int)$book->$twuenty;
            }
            else{
              $twuentyV = $book->$twuenty.'_E_E';
            }

            if(empty($book->$forty) != true ){
              $fortyB=true;
              $fortyV = (int)$book->$forty;
            }
            else{
              $fortyV = $book->$forty.'_E_E';
            }

            if(empty($book->$fortyhc) != true ){
              $fortyhcB=true;
              $fortyhcV = (int)$book->$fortyhc;
            }
            else{
              $fortyhcV = $book->$fortyhc.'_E_E';
            }

            if(empty($currenc->id) != true){
              $curreB=true;
              $currencyV =  $currenc->id;
            }
            else{
              $currencyV = $book->currency.'_E_E';
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
              if($origB == true){
                $originV = $book->$origin;
              }
              if($destiB == true){
                $destinationV = $book->$destination;
              }
              if($curreB == true){
                $currencyV = $book->currency;
              }
              if($carriB == true){
                $carrierV = $book->carrier;
              }


              if( empty($book->$origin) == true
                 && empty($book->$destination) == true
                 && empty($book->carrier) == true
                 && empty($book->currency) == true
                 && empty($book->$twuenty) == true
                 && empty($book->$forty) == true
                 && empty($book->$fortyhc) == true ) {

              }else{

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
      Storage::delete($nombre);
      Rate::onlyTrashed()->where('contract_id','=',$contract)
        ->forceDelete();
      FailRate::onlyTrashed()->where('contract_id','=',$contract)
        ->forceDelete();
      return redirect()->route('Failed.Rates.For.Contracts',$contract);

      //dd($res);*/

    } catch (\Illuminate\Database\QueryException $e) {
      Storage::delete($nombre);
      Rate::onlyTrashed()->where('contract_id','=',$contract)
        ->restore();
      FailRate::onlyTrashed()->where('contract_id','=',$contract)
        ->restore();
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

    //  dd($failrates);
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

        $idrate = Rate::create([
          'origin_port'   => $originV,
          'destiny_port'  => $destinationV,
          'carrier_id'    => $carrierV,
          'contract_id'   => $contract,
          'twuenty'       => $twuentyV,
          'forty'         => $fortyV,
          'fortyhc'       => $fortyhcV,
          'currency_id'   => $currencyV,
        ]); 

        $failrate->forceDelete();

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
                       'idrate'    => $idrate->id
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
    try{
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
    }catch(\Exception $e){
      return $col = ['response'  => '2'];
    }

  }

  public function DestroyRatesFailCorrect(Request $request){
    $rate_id   =  $_REQUEST['rate_id'];
    $accion    =  $_REQUEST['accion'];


    if($accion == 2){
      $rate = new Rate();
      $rate = Rate::find($rate_id);
      $rate->forceDelete();
      return 2;
    }
    else if($accion == 1){
      $ratefail = new FailRate();
      $ratefail = FailRate::find($rate_id);
      $ratefail->forceDelete();
      return 1;

    }
  }

  public function UploadFileSubchargeForContract(Request $request){
    //dd($request);
    $nombre='';
    try {
      $now = new \DateTime();
      $now = $now->format('dmY_His');
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
      $nombre = $now.'_'.$nombre;

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

          $originExits = Harbor::where('varation->type','like','%'.strtolower($originVar).'%')
            ->get();

          if(count($originExits) == 1){
            $originBol=true;
            foreach($originExits as $originRc){
              $originVar = $originRc['id'];
            }
          }else{
            $originVar = $originVar.'_E_E';
          }

          $destinationExits = Harbor::where('varation->type','like','%'.strtolower($destinationVar).'%')
            ->get();

          if(count($destinationExits) == 1){
            $destinationBol=true;
            foreach($destinationExits as $destinationRc){
              $destinationVar = $destinationRc['id'];
            }
          }else{
            $destinationVar = $destinationVar.'_E_E';
          }

          if(empty($surcharge) != true){
            $surchargeBol = true;
            $surchargeVar = $surcharge['id'];
          }
          else{
            $surchargeVar = $book->$surchargeBook.'_E_E';
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
      Storage::delete($nombre);
      LocalCharge::onlyTrashed()->where('contract_id','=',$contract)
        ->forceDelete();
      FailSurCharge::onlyTrashed()->where('contract_id','=',$contract)
        ->forceDelete();
      return redirect()->route('Failed.Subcharge.For.Contracts',$contract);

    } catch (\Illuminate\Database\QueryException $e) {
      Storage::delete($nombre);
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

      if($currencyC){
        $currencyA = $currencyA[0];
      }
      else{
        $currencyA      = $currencyA[0].' (error)';
        $classcurrency  = 'color:red';
      }

      $typedestinyLB    = TypeDestiny::where('id','=',$failsurcharge['typedestiny_id'])->first();
      ////////////////////////////////////////////////////////////////////////////////////
      //$originLB         = Harbor::where('id','=',$originA[0])->first();
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

        'carrierlb'            => $carrierA,
        'carrier_id'           => $carrAIn,
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
                                                   'goodsurcharges',
                                                   'id'
                                                  )); //*/
  }

  public function SaveCorrectedSurcharge(Request $request){

    try {


      $idSurchargeVar        =    $_REQUEST['idSurcharge'];
      $surchargeVar          =    $_REQUEST['surcharge'];
      $contractVar           =    $_REQUEST['contract_id'];
      $originVarArr          =    $_REQUEST['origin'];
      $destinationVarArr     =    $_REQUEST['destination'];
      $typedestinyVar        =    $_REQUEST['typedestiny'];
      $calculationtypeVar    =    $_REQUEST['calculationtype'];
      $ammountVar            =    $_REQUEST['ammount'];
      $currencyVar           =    $_REQUEST['currency'];
      $carrierVarArr         =    $_REQUEST['carrier'];
      //return $carrierVar;
      //*/

      $failSurcharge = new FailSurCharge();
      $failSurcharge = FailSurCharge::find($idSurchargeVar);

      $SurchargeId = LocalCharge::create([
        'surcharge_id'          => $surchargeVar,
        'typedestiny_id'        => $typedestinyVar,
        'contract_id'           => $contractVar,
        'calculationtype_id'    => $calculationtypeVar,
        'ammount'               => $ammountVar,
        'currency_id'           => $currencyVar
      ]);

      foreach($originVarArr as $originVar){
        foreach($destinationVarArr as $destinationVar){
          LocalCharPort::create([
            'port_orig'         => $originVar,
            'port_dest'         => $destinationVar,
            'localcharge_id'    => $SurchargeId->id
          ]);
        }
      }

      foreach($carrierVarArr as $carrierVar){
        LocalCharCarrier::create([
          'carrier_id'        => $carrierVar,
          'localcharge_id'    => $SurchargeId->id  
        ]);
      }
      $failSurcharge->forceDelete(); //*/
      $goodsurcharges     = LocalCharge::where('id','=',$SurchargeId->id)
        //$goodsurcharges     = LocalCharge::where('id','=',16)
        ->where('contract_id','=',$contractVar)
        ->with('currency',
               'calculationtype',
               'surcharge',
               'typedestiny',
               'localcharcarriers.carrier',
               'localcharports.portOrig',
               'localcharports.portDest'
              )->get();
      $colle = collect([]);
      foreach($goodsurcharges as $goodsurcharge){
        //dd($goodsurcharge->calculationtype);
        $portOri ='';
        foreach($goodsurcharge->localcharports->pluck('portOrig')->unique()->pluck('name') as $name){
          $portOri = $portOri.' '.$name.'.';
        }

        $portDest ='';
        foreach($goodsurcharge->localcharports->pluck('portDest')->unique()->pluck('name') as $name){
          $portDest = $portDest.' '.$name.'.';
        }
        $carriercoll ='';
        foreach($goodsurcharge->localcharcarriers->pluck('carrier')->unique()->pluck('name') as $name){
          $carriercoll = $carriercoll.' '.$name.'.';
        }

        $arreglo  = [
          'surcharge_id'          => $goodsurcharge->id,
          'surchargeLB'           => $goodsurcharge->surcharge['name'],

          'typedestinyLB'         => $goodsurcharge->typedestiny['description'],

          'calculationtypeLB'     => $goodsurcharge->calculationtype['name'],
          'ammount'               => $goodsurcharge->ammount,
          'currencyLB'            => $goodsurcharge->currency['alphacode'],

          'port_origLB'           => $portOri,
          'port_destLB'           => $portDest,
          'carrier'               => $carriercoll,
          'response'              => 1
        ];
        //dd($arreglo);
        return $arreglo;
      }
    } catch (\Exception $e){
      return $arreglo = ['response'  => '0'];
    }
  }

  public function DestroySurchargeFailCorrect(Request $request){

    try{
      $surcharge_id   =  $_REQUEST['surcharge_id'];
      $accion         =  $_REQUEST['accion'];

      if($accion == 2){
        $surcharge = new LocalCharge();
        $surcharge = LocalCharge::find($surcharge_id);
        $surcharge->forceDelete();
        return 2;
      }
      else if($accion == 1){
        $surchargefail = new FailSurCharge();
        $surchargefail = FailSurCharge::find($surcharge_id);
        $surchargefail->forceDelete();
        return 1;

      }
    }catch(\Exception $e){
      return 3;
    }
  }

  public function UpdateSurchargeCorrect(Request $request){
    try {


      $surchargeVar          =  $_REQUEST['surcharge']; // id de la columna surchage_id
      $idSurchargeVar        =  $_REQUEST['idSurcharge']; // id del localcherge
      $contractVar           =  $_REQUEST['contract_id'];
      $originVarArr          =  $_REQUEST['origin'];
      $destinationVarArr     =  $_REQUEST['destination'];
      $typedestinyVar        =  $_REQUEST['typedestiny'];
      $calculationtypeVar    =  $_REQUEST['calculationtype'];
      $ammountVar            =  $_REQUEST['ammount'];
      $currencyVar           =  $_REQUEST['currency'];
      $carrierVarArr         =  $_REQUEST['carrier'];
      // return response()->json(['message' => 'Ok']);
      //*/



      $SurchargeId = new LocalCharge();
      $SurchargeId  = LocalCharge::find($idSurchargeVar);

      $SurchargeId->surcharge_id          = $surchargeVar;
      $SurchargeId->typedestiny_id        = $typedestinyVar;
      $SurchargeId->contract_id           = $contractVar;
      $SurchargeId->calculationtype_id    = $calculationtypeVar;
      $SurchargeId->ammount               = $ammountVar;
      $SurchargeId->currency_id           = $currencyVar;
      $SurchargeId->update();

      LocalCharPort::where('localcharge_id','=',$SurchargeId->id)->forceDelete();
      foreach($originVarArr as $originVar){
        foreach($destinationVarArr as $destinationVar){
          LocalCharPort::create([
            'port_orig'         => $originVar,
            'port_dest'         => $destinationVar,
            'localcharge_id'    => $SurchargeId->id
          ]); //
        }
      }

      LocalCharCarrier::where('localcharge_id','=',$SurchargeId->id)->forceDelete();
      foreach($carrierVarArr as $carrierVar){
        LocalCharCarrier::create([
          'carrier_id'        => $carrierVar,
          'localcharge_id'    => $SurchargeId->id  
        ]); //
      }

      $goodsurcharges     = LocalCharge::where('id','=',$SurchargeId->id)
        //$goodsurcharges     = LocalCharge::where('id','=',16)
        ->where('contract_id','=',$contractVar)
        ->with('currency',
               'calculationtype',
               'surcharge',
               'typedestiny',
               'localcharcarriers.carrier',
               'localcharports.portOrig',
               'localcharports.portDest'
              )->get();

      foreach($goodsurcharges as $goodsurcharge){
        //dd($goodsurcharge->calculationtype);
        $portOri ='';
        foreach($goodsurcharge->localcharports->pluck('portOrig')->unique()->pluck('name') as $name){
          $portOri = $portOri.' '.$name.'.';
        }

        $portDest ='';
        foreach($goodsurcharge->localcharports->pluck('portDest')->unique()->pluck('name') as $name){
          $portDest = $portDest.' '.$name.'.';
        }
        $carriercoll ='';
        foreach($goodsurcharge->localcharcarriers->pluck('carrier')->unique()->pluck('name') as $name){
          $carriercoll = $carriercoll.' '.$name.'.';
        }

        $arreglo  = [
          'surcharge_id'          => $goodsurcharge->id,
          'surchargeLB'           => $goodsurcharge->surcharge['name'],

          'typedestinyLB'         => $goodsurcharge->typedestiny['description'],

          'calculationtypeLB'     => $goodsurcharge->calculationtype['name'],
          'ammount'               => $goodsurcharge->ammount,
          'currencyLB'            => $goodsurcharge->currency['alphacode'],

          'port_origLB'           => $portOri,
          'port_destLB'           => $portDest,
          'carrier'               => $carriercoll,
          'response'              => 1
        ];
        //dd($arreglo);
        return $arreglo;
      }
    } catch (\Exception $e){
      return $arreglo = ['response'  => 0];
    } //*/
  }

  public function LoadViewImporContractFcl(){
    $harbor  = harbor::all()->pluck('name');
    $carrier = carrier::all()->pluck('name');
    return view('contracts.ImporContractFcl',compact('harbor','carrier'));
  }

  public function UploadFileNewContract(Request $request){
    //dd($request);
    $now = new \DateTime();
    $now = $now->format('dmY_His');

    $request->type;

    $carrierVal = $request->carrier;
    $destinyArr = $request->destiny;
    $originArr  = $request->origin;

    $carrierBol = false;
    $destinyBol = false;
    $originBol  = false;

    $data= collect([]);

    $harbor  = harbor::all()->pluck('name');
    $carrier = carrier::all()->pluck('name');
    // try {
    $file = $request->file('file');
    $ext = strtolower($file->getClientOriginalExtension());

    $validator = \Validator::make(
      array('ext' => $ext),
      array('ext' => 'in:xls,xlsx,csv')
    );
    $Contract_id;
    if ($validator->fails()) {
      $request->session()->flash('message.nivel', 'danger');
      $request->session()->flash('message.content', 'just archive with extension xlsx xls csv');
      return redirect()->route('contracts.edit',$request->contract_id);
    }

    //obtenemos el nombre del archivo
    $nombre = $file->getClientOriginalName();
    $nombre = $now.'_'.$nombre;
    \Storage::disk('UpLoadFile')->put($nombre,\File::get($file));

    $contract     = new Contract();

    $contract->name             = $request->name;
    $contract->number           = $request->number;
    $validity                   = explode('/',$request->validation_expire);
    $contract->validity         = $validity[0];
    $contract->expire           = $validity[1];
    $contract->status           = 'incomplete';
    $contract->company_user_id  = \Auth::user()->company_user_id;
    $contract->save(); //*/

    $Contract_id = $contract->id;

    $fileTmp = new FileTmp();
    $fileTmp->contract_id = $Contract_id;
    $fileTmp->name_file   = $nombre;
    $fileTmp->save();

    $targetsArr =[ 0 => 'Currency', 1 => "20'", 2 => "40'", 3 => "40'HC"];

    if($request->DatOri == false){
      array_push($targetsArr,'Origin');
    }
    else{
      $originBol = true;
      $originArr;
    }

    if($request->DatDes == false){
      array_push($targetsArr,'Destiny');
    } else {
      $destinyArr;
      $destinyBol = true;
    }

    if($request->DatCar == false){
      array_push($targetsArr,'Carrier');
    } else {
      $carrierVal;
      $carrierBol = true;
    }
    //dd($targetsArr);
    //  dd($data);
    $coordenates = collect([]);
    Excel::selectSheetsByIndex(0)
      ->Load(\Storage::disk('UpLoadFile')
             ->url($nombre),function($reader) use($request,$coordenates) {
               $reader->noHeading = true;
               $reader->ignoreEmpty();

               foreach($reader->get() as $read){
                 // dd($read);
                 $columna= array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','Ñ','O');
                 for($i=0;$i<count($read);$i++){
                   $coordenates->push($columna[$i].' '.$read[$i]);
                 }
                 break;
               }
               //dd($coordenates);

             });
    $boxdinamy = [
      'existorigin'   => $originBol,
      'origin'        => $originArr,

      'existdestiny'  => $destinyBol,
      'destiny'       => $destinyArr,

      'existcarrier'  => $carrierBol,
      'carrier'       => $carrierVal,

      'Contract_id'   => $Contract_id,
      'number'        => $request->number,
      'name'          => $request->name,
      'fileName'      => $nombre,
      'validatiion'   => $request->validation_expire,
    ];
    $data->push($boxdinamy);
    $countTarges = count($targetsArr);
    //dd($data);

    return view('contracts.ContractFclProcess',compact('harbor','carrier','coordenates','targetsArr','data','countTarges'));
    /*}catch(\Exception $e){
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.content', 'Error with the archive');
            return redirect()->route('importaion.fcl');
        }//*/
  }

  public function ProcessContractFcl(Request $request){
    //Storage::delete($request->);
    dd($request);
    $currency   = "Currency";
    $twenty     = "20'";
    $forty      = "40'";
    $fortyhc    = "40'HC";
    $origin     = "Origin";
    $destiny    = "Destiny";
    $carrier    = "Carrier";

    $currency   = $request->$currency;
    $twenty     = $request->$twenty;
    $forty      = $request->$forty;
    $fortyhc    = $request->$fortyhc;

    $coordenates = collect([]); 

    Excel::selectSheetsByIndex(0)
      ->Load(\Storage::disk('UpLoadFile')
             ->url($request ->FileName),function($reader) use($request,$coordenates) {
               $reader->noHeading = true;
               $reader->ignoreEmpty();

               foreach($reader->get() as $read){

                 $carrierVal = '';
                 $originVal = '';
                 $destinyVal = '';

                 $originBol = false;
                 $destinyBol = false;

                 if($loop->iteration != 1){
                   // 0 => 'Currency', 1 => "20'", 2 => "40'", 3 => "40'HC"
                   if($request->existcarrier == 1){
                     $carrierVal = $request->carrier; // cuando se indica que no posee carrier 
                   } else {
                     $carrierVal = $read[$request->$carrier]; // cuando el carrier existe en el excel
                   }
                   //---------------------------------------------------------------
                   if($request->existorigin == 1){
                     $originBol = true;
                     $randons = $request->$origin;
                   } else {
                     $originVal = $read[$request->$origin];
                     // validar y traer id del port
                   }
                   //---------------------------------------------------------------
                   if($request->existdestiny == 1){
                     $destinyBol = true;
                     $randons = $request->$destiny;
                   } else {
                     $originVal = $read[$request->$destiny];
                     // validar y traer id del port
                   }

                   /*if(){
                               //good rates
                               if($originBol == true || $destinyBol == true){
                                   foreach($randons as  $randon){
                                   //insert por arreglo de puerto
                                       Rate::create([
                                           'origin_port'   => '',
                                           'destiny_port'  => '',
                                           'carrier_id'    => $carrierVal,
                                           'contract_id'   => $request->Contract_id,
                                           'twuenty'       => '',
                                           'forty'         => '',
                                           'fortyhc'       => '',
                                           'currency_id'   => '',
                                       ]);
                                   } else {
                                   // fila por puerto
                                         Rate::create([
                                           'origin_port'   => '',
                                           'destiny_port'  => '',
                                           'carrier_id'    => $carrierVal,
                                           'contract_id'   => $request->Contract_id,
                                           'twuenty'       => '',
                                           'forty'         => '',
                                           'fortyhc'       => '',
                                           'currency_id'   => '',
                                       ]);
                                   }
                               }
                               } else {
                                 // fail rates

                                 FailRate::create([
                                           'origin_port'   => '',
                                           'destiny_port'  => '',
                                           'carrier_id'    => $carrierVal,
                                           'contract_id'   => $request->Contract_id,
                                           'twuenty'       => '',
                                           'forty'         => '',
                                           'fortyhc'       => '',
                                           'currency_id'   => '',
                                       ]);
                               }
                               */


                 }
               }
               //dd($coordenates);

             });
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
    $local->delete();
  }

  public function destroyRates(Request $request,$id)
  {
    $rate = Rate::find($id);
    $rate->delete();
    return $rate;

  }

  public function destroymsg($id)
  {
    return view('contracts/message' ,['rate_id' => $id]);
  }
}
