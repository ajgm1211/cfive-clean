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
use App\CalculationType;
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
use App\ViewContractRates;
use Illuminate\Support\Collection as Collection;
use App\ContractLcl;

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
    $calculationT = CalculationType::all()->pluck('name','id');
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
}
