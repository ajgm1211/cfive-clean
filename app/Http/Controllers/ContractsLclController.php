<?php

namespace App\Http\Controllers;

use App\Company;
use Illuminate\Http\Request;
use App\Contact;
use App\Country;
use App\Carrier;
use App\Harbor;
use App\RateLcl;
use App\Currency;
use App\CalculationTypeLcl;
use App\Surcharge;
use App\User;
use App\TypeDestiny;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Excel;
use Illuminate\Support\Facades\Log;
use Yajra\Datatables\Datatables;
use App\CompanyUser;
use App\ViewLocalCharges;
use App\ViewRates;
use App\ViewContractLclRates;
use Illuminate\Support\Collection as Collection;
use App\ContractLcl;
use App\LocalChargeLcl;
use App\LocalCharCarrierLcl;
use App\LocalCharPortLcl;
use App\LocalCharCountryLcl;

class ContractsLclController extends Controller
{
  /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
  public function index()
  {
    $arreglo = ContractLcl::where('company_user_id','=',Auth::user()->company_user_id)->get();
    $contractG = ContractLcl::where('company_user_id','=',Auth::user()->company_user_id)->get();
    return view('contractsLcl/index', compact('arreglo','contractG'));
  }

  public function add()
  {

    $harbor = Harbor::all()->pluck('display_name','id');
    $country = Country::all()->pluck('name','id');
    $carrier = Carrier::all()->pluck('name','id');
    $currency = Currency::all()->pluck('alphacode','id');
    $calculationT = CalculationTypeLcl::all()->pluck('name','id');
    $typedestiny = TypeDestiny::all()->pluck('description','id');
    $surcharge = Surcharge::where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
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

    return view('contractsLcl.add',compact('country','carrier','harbor','currency','calculationT','surcharge','typedestiny','companies','contacts','users'));

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
    $contract = new ContractLcl($request->all());
    $contract->company_user_id =Auth::user()->company_user_id;
    $validation = explode('/',$request->validation_expire);
    $contract->validity = $validation[0];
    $contract->expire = $validation[1];
    $contract->save();
    $details = $request->input('currency_id');
    $detailscharges = $request->input('localcurrency_id');


    // For Each de los rates
    $contador = 1;
    $contadorRate = 1;

    // For each de los rates 
    foreach($details as $key => $value)
    {

      $rateOrig = $request->input('origin_id'.$contadorRate);
      $rateDest = $request->input('destiny_id'.$contadorRate);

      foreach($rateOrig as $Rorig => $Origvalue)
      {
        foreach($rateDest as $Rdest => $Destvalue)
        {
          $rates = new RateLcl();
          $rates->origin_port = $request->input('origin_id'.$contadorRate.'.'.$Rorig); 
          $rates->destiny_port = $request->input('destiny_id'.$contadorRate.'.'.$Rdest); 
          $rates->carrier_id = $request->input('carrier_id.'.$key);
          $rates->uom = $request->input('uom.'.$key);
          $rates->minimum = $request->input('minimum.'.$key);         
          $rates->currency_id = $request->input('currency_id.'.$key);
          $rates->contract()->associate($contract);
          $rates->save();
        }
      }
      $contadorRate++;
    }
    // for each de los localcharges

    foreach($detailscharges as $key2 => $value)
    {
      $calculation_type = $request->input('calculationtype'.$contador); 
      if(!empty($calculation_type)){

        foreach($calculation_type as $ct => $ctype)
        {

          if(!empty($request->input('ammount.'.$key2))) {
            $localcharge = new LocalChargeLcl();
            $localcharge->surcharge_id = $request->input('type.'.$key2);
            $localcharge->typedestiny_id = $request->input('changetype.'.$key2);
            $localcharge->calculationtypelcl_id = $ctype;//$request->input('calculationtype.'.$key2);
            $localcharge->ammount = $request->input('ammount.'.$key2);
            $localcharge->currency_id = $request->input('localcurrency_id.'.$key2);
            $localcharge->contract()->associate($contract);
            $localcharge->save();

            $detailcarrier = $request->input('localcarrier_id'.$contador);

            foreach($detailcarrier as $c => $value)
            {
              $detailcarrier = new LocalCharCarrierLcl();
              $detailcarrier->carrier_id =$request->input('localcarrier_id'.$contador.'.'.$c);
              $detailcarrier->localchargelcl()->associate($localcharge);
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
                  $detailport = new LocalCharPortLcl();
                  $detailport->port_orig = $request->input('port_origlocal'.$contador.'.'.$orig);
                  $detailport->port_dest = $request->input('port_destlocal'.$contador.'.'.$dest);
                  $detailport->localchargelcl()->associate($localcharge);
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
                  $detailcountry = new LocalCharCountryLcl();
                  $detailcountry->country_orig =$request->input('country_orig'.$contador.'.'.$origC);
                  $detailcountry->country_dest = $request->input('country_dest'.$contador.'.'.$destC);
                  $detailcountry->localchargelcl()->associate($localcharge);
                  $detailcountry->save();
                }
              }
            }

          }
        }
      }
      $contador++;
    }

    $request->session()->flash('message.nivel', 'success');
    $request->session()->flash('message.title', 'Well done!');
    $request->session()->flash('message.content', 'You successfully add this contract.');

    return redirect()->action('ContractsLclController@index');

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
  public function contractlclTable(){

    $contractG = ContractLcl::where('company_user_id','=',Auth::user()->company_user_id)->get();
    return \DataTables::collection($contractG)

      ->addColumn('options', function (ContractLcl $contractG) {
        return "      <a href='contractslcl/".setearRouteKey($contractG->id)."/edit' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill'  title='Edit '>
                      <i class='la la-edit'></i>
                    </a>
                    <a  id='delete-contract' data-contract-id='$contractG->id' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill'  title='Delete'>
                      <i class='la la-eraser'></i>
                    </a>

        ";
      }) ->setRowId('id')->rawColumns(['options'])->make(true);

  }
  // DATATABLES 


  public function contractLclRates(){
    $contractRate = new  ViewContractLclRates();
    $data = $contractRate->select('id','contract_id','name','number','validy','expire','status','port_orig','port_dest','carrier','uom','minimum','currency')->where('company_user_id', Auth::user()->company_user_id);


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
}
