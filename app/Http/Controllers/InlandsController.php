<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Harbor;
use App\Inland;
use App\InlandPort;
use App\InlandDetail;
use App\Currency;
use Illuminate\Support\Facades\Auth;
use App\Company;
use App\InlandCompanyRestriction;
use EventIntercom;
class InlandsController extends Controller
{
  /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
  public function index()
  {

    $data = Inland::where('company_user_id','=',Auth::user()->company_user_id)->with('inlandports.ports')->get();
    return view('inland/index', ['arreglo' => $data]);
  }

  public function add(){
    $company_user_id=\Auth::user()->company_user_id;
    if(\Auth::user()->hasRole('subuser')){
      $companies = Company::where('company_user_id','=',$company_user_id)->whereHas('groupUserCompanies', function($q)  {
        $q->where('user_id',\Auth::user()->id);
      })->orwhere('owner',\Auth::user()->id)->pluck('business_name','id');
    }else{
      $companies = Company::where('company_user_id','=',$company_user_id)->pluck('business_name','id');
    }

    $objharbor = new Harbor();
    $harbor = $objharbor->all()->pluck('display_name','id');
    $objcurrency = new Currency();
    $currency = $objcurrency->all()->pluck('alphacode','id');
    return view('inland/add', compact('harbor','currency','companies'));
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
    //  dd($request);
    $inland = new Inland();
    $companies = $request->input('companies');
    $inland->provider = $request->input('name');
    $inland->type = $request->input('status');
    $validation = explode('/',$request->validation_expire);
    $inland->validity = $validation[0];
    $inland->expire = $validation[1];
    $inland->company_user_id = Auth::user()->company_user_id;
    $inland->save();
    $ports = $request->input('irelandports');
    $detailstwuenty =  $request->input('lowertwuenty');
    $detailsforty =  $request->input('lowerforty');
    $detailsfortyhc =  $request->input('lowerfortyhc');
    foreach($ports as $p => $value)
    {
      $inlandport = new InlandPort();
      $inlandport->port = $request->input('irelandports.'.$p);
      $inlandport->inland()->associate($inland);
      $inlandport->save();
    }
    foreach($detailstwuenty as $t => $value)
    {
      if(!empty($request->input('ammounttwuenty.'.$t))) {
        $inlandtwuenty = new InlandDetail();
        $inlandtwuenty->lower = $request->input('lowertwuenty.'.$t);
        $inlandtwuenty->upper = $request->input('uppertwuenty.'.$t);
        $inlandtwuenty->ammount = $request->input('ammounttwuenty.'.$t);
        $inlandtwuenty->type = 'twuenty';
        $inlandtwuenty->currency_id = $request->input('currencytwuenty.'.$t);
        $inlandtwuenty->inland()->associate($inland);
        $inlandtwuenty->save();
      }
    }
    foreach($detailsforty as $t => $value)
    {
      if(!empty($request->input('ammountforty.'.$t))) {
        $inlandforty= new InlandDetail();
        $inlandforty->lower = $request->input('lowerforty.'.$t);
        $inlandforty->upper = $request->input('upperforty.'.$t);
        $inlandforty->ammount = $request->input('ammountforty.'.$t);
        $inlandforty->type = 'forty';
        $inlandforty->currency_id = $request->input('currencyforty.'.$t);
        $inlandforty->inland()->associate($inland);
        $inlandforty->save();
      }
    }

    foreach($detailsfortyhc as $t => $value)
    {
      if(!empty($request->input('ammountfortyhc.'.$t))) {
        $inlandfortyhc = new InlandDetail();
        $inlandfortyhc->lower = $request->input('lowerfortyhc.'.$t);
        $inlandfortyhc->upper = $request->input('upperfortyhc.'.$t);
        $inlandfortyhc->ammount = $request->input('ammountfortyhc.'.$t);
        $inlandfortyhc->type = 'fortyhc';
        $inlandfortyhc->currency_id = $request->input('currencyfortyhc.'.$t);
        $inlandfortyhc->inland()->associate($inland);
        $inlandfortyhc->save();
      }
    }
    if(!empty($companies)){
      foreach($companies as $key3 => $value)
      {
        $inland_company_restriction = new InlandCompanyRestriction();
        $inland_company_restriction->company_id=$value;
        $inland_company_restriction->inland_id=$inland->id;
        $inland_company_restriction->save();
      }
    }
    // EVENTO INTERCOM 
    $event = new  EventIntercom();
    $event->event_inlands();
    
    
    $request->session()->flash('message.nivel', 'success');
    $request->session()->flash('message.title', 'Well done!');
    $request->session()->flash('message.content', 'You successfully add this Inland.');
    return redirect()->action('InlandsController@index');
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
    $id = obtenerRouteKey($id);
    $inland = Inland::with('inlandports.ports','inlanddetails.currency')->get()->find($id);
    $objcurrency = new Currency();
    $currency = $objcurrency->all()->pluck('alphacode','id');

    $company_user_id=\Auth::user()->company_user_id;
    if(\Auth::user()->hasRole('subuser')){
      $companies = Company::where('company_user_id','=',$company_user_id)->whereHas('groupUserCompanies', function($q)  {
        $q->where('user_id',\Auth::user()->id);
      })->orwhere('owner',\Auth::user()->id)->pluck('business_name','id');
    }else{
      $companies = Company::where('company_user_id','=',$company_user_id)->pluck('business_name','id');
    }
    $company_restriction = InlandCompanyRestriction::where('inland_id',$inland->id)->first();
    $company = array();
    if(!empty($company_restriction)){
      $company = Company::where('id',$company_restriction->company_id)->select('id')->first();
    }
    $objharbor = new Harbor();
    $harbor = $objharbor->all()->pluck('display_name','id');
    return view('inland/edit', compact('harbor','inland','currency','company','companies'));

  }

  public function updateDetails(Request $request,$id){

    $requestForm = $request->all();
    $inland = InlandDetail::find($id);
    $inland->update($requestForm);
  }
  public function deleteDetails($id){

    $inland = InlandDetail::find($id);
    $inland->delete();
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
    $id = obtenerRouteKey($id);
    $inland = Inland::find($id);
    $companies = $request->input('companies');
    $inland->provider = $request->input('provider');
    $inland->type = $request->input('type');
    $validation = explode('/',$request->validation_expire);
    $inland->validity = $validation[0];
    $inland->expire = $validation[1];
    $inland->update();
    $ports = $request->input('inlandport');
    $detailstwuenty =  $request->input('lowertwuenty');
    $detailsforty =  $request->input('lowerforty');
    $detailsfortyhc =  $request->input('lowerfortyhc');
    $inlandport = InlandPort::where("inland_id",$id);
    $inlandport->delete();
    foreach($ports as $p => $value)
    {
      $inlandport = new InlandPort();
      $inlandport->port = $request->input('inlandport.'.$p);
      $inlandport->inland()->associate($inland);
      $inlandport->save();
    }
    foreach($detailstwuenty as $t => $value)
    {
      if(!empty($request->input('ammounttwuenty.'.$t))) {
        $inlandtwuenty = new InlandDetail();
        $inlandtwuenty->lower = $request->input('lowertwuenty.'.$t);
        $inlandtwuenty->upper = $request->input('uppertwuenty.'.$t);
        $inlandtwuenty->ammount = $request->input('ammounttwuenty.'.$t);
        $inlandtwuenty->type = 'twuenty';
        $inlandtwuenty->currency_id = $request->input('currencytwuenty.'.$t);
        $inlandtwuenty->inland()->associate($inland);
        $inlandtwuenty->save();
      }
    }
    foreach($detailsforty as $t => $value)
    {
      if(!empty($request->input('ammountforty.'.$t))) {
        $inlandforty= new InlandDetail();
        $inlandforty->lower = $request->input('lowerforty.'.$t);
        $inlandforty->upper = $request->input('upperforty.'.$t);
        $inlandforty->ammount = $request->input('ammountforty.'.$t);
        $inlandforty->type = 'forty';
        $inlandforty->currency_id = $request->input('currencyforty.'.$t);
        $inlandforty->inland()->associate($inland);
        $inlandforty->save();
      }
    }

    foreach($detailsfortyhc as $t => $value)
    {
      if(!empty($request->input('ammountfortyhc.'.$t))) {
        $inlandfortyhc = new InlandDetail();
        $inlandfortyhc->lower = $request->input('lowerfortyhc.'.$t);
        $inlandfortyhc->upper = $request->input('upperfortyhc.'.$t);
        $inlandfortyhc->ammount = $request->input('ammountfortyhc.'.$t);
        $inlandfortyhc->type = 'fortyhc';
        $inlandfortyhc->currency_id = $request->input('currencyfortyhc.'.$t);
        $inlandfortyhc->inland()->associate($inland);
        $inlandfortyhc->save();
      }
    }

    InlandCompanyRestriction::where('inland_id',$inland->id)->delete();
    if(!empty($companies)){
      foreach($companies as $key3 => $value)
      {
        $inland_company_restriction = new InlandCompanyRestriction();
        $inland_company_restriction->company_id=$value;
        $inland_company_restriction->inland_id=$inland->id;
        $inland_company_restriction->save();
      }
    }
    $request->session()->flash('message.nivel', 'success');
    $request->session()->flash('message.title', 'Well done!');
    $request->session()->flash('message.content', 'You successfully updated this Inland.');
    return redirect()->action('InlandsController@index');
  }

  public function deleteInland(Request $request,$id)
  {
    $inland = Inland::find($id);
    $inland->delete();
    $request->session()->flash('message.nivel', 'success');
    $request->session()->flash('message.title', 'Well done!');
    $request->session()->flash('message.content', 'You successfully deleted this Inland.');
    return redirect()->action('InlandsController@index');
  }
  public function destroy($id)
  {
    $inland = Inland::find($id);
    $inland->delete();
  }
}
