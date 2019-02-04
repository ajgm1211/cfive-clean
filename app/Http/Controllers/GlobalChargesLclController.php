<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\GlobalChargeLcl;
use App\Carrier;
use App\Harbor;
use App\Rate;
use App\Currency;
use App\CalculationTypeLcl;
use App\Surcharge;
use App\GlobalCharPortLcl;
use App\GlobalCharCarrierLcl;
use App\TypeDestiny;
use App\Country;
use App\GlobalCharCountryLcl;

class GlobalChargesLclController extends Controller
{
  /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
  public function index()
  {

    $global =  GlobalChargeLcl::whereHas('companyUser', function($q) {
      $q->where('company_user_id', '=', Auth::user()->company_user_id);
    })->with('globalcharportlcl.portOrig','globalcharportlcl.portDest','globalcharcarrierslcl.carrier','typedestiny','globalcharcountrylcl.countryOrig','globalcharcountrylcl.countryDest')->get();

    $harbor = Harbor::all()->pluck('display_name','id');
    $carrier = Carrier::all()->pluck('name','id');
    $currency = Currency::all()->pluck('alphacode','id');
    $calculationT = CalculationTypeLcl::all()->pluck('name','id');
    $typedestiny = TypeDestiny::all()->pluck('description','id');
    $surcharge = Surcharge::where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
    return view('globalchargeslcl/index', compact('global','carrier','harbor','currency','calculationT','surcharge','typedestiny'));
  }

  public function addGlobalChar(){

    $countries = Country::pluck('name','id');
    $calculationT = CalculationTypeLcl::all()->pluck('name','id');
    $typedestiny = TypeDestiny::all()->pluck('description','id');
    $surcharge = Surcharge::where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
    $harbor = Harbor::all()->pluck('display_name','id');
    $carrier = Carrier::all()->pluck('name','id');
    $currency = Currency::all()->pluck('alphacode','id');

    return view('globalchargeslcl.add', compact('harbor','carrier','currency','calculationT','typedestiny','surcharge','countries'));
  }

  public function editGlobalChar($id){

    $countries = Country::pluck('name','id');
    $calculationT = CalculationTypeLcl::all()->pluck('name','id');
    $typedestiny = TypeDestiny::all()->pluck('description','id');
    $surcharge = Surcharge::where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
    $harbor = Harbor::all()->pluck('display_name','id');
    $carrier = Carrier::all()->pluck('name','id');
    $currency = Currency::all()->pluck('alphacode','id');
    $globalcharges = GlobalChargeLcl::find($id);
    $validation_expire = $globalcharges->validity ." / ". $globalcharges->expire ;
    $globalcharges->setAttribute('validation_expire',$validation_expire);
    return view('globalchargeslcl.edit', compact('globalcharges','harbor','carrier','currency','calculationT','typedestiny','surcharge','countries'));
  }


  public function store(Request $request)
  {
    $detailscharges = $request->input('type');
    $calculation_type = $request->input('calculationtype');


    //$changetype = $type->find($request->input('changetype.'.$key2))->toArray();
    foreach($calculation_type as $ct => $ctype)
    {

      $global = new GlobalChargeLcl();
      $validation = explode('/',$request->validation_expire);
      $global->validity = $validation[0];
      $global->expire = $validation[1];
      $global->surcharge_id = $request->input('type');
      $global->typedestiny_id = $request->input('changetype');
      $global->calculationtypelcl_id = $ctype;
      $global->ammount = $request->input('ammount');
      $global->minimum = $request->input('minimum');
      $global->currency_id = $request->input('localcurrency_id');
      $global->company_user_id = Auth::user()->company_user_id; 
      $global->save();
      // Detalles de puertos y carriers
      //$totalCarrier = count($request->input('localcarrier'.$contador));
      //$totalport =  count($request->input('port_id'.$contador));
      $detailcarrier = $request->input('localcarrier');
      foreach($detailcarrier as $c => $value)
      {

        $detailcarrier = new GlobalCharCarrierLcl();
        $detailcarrier->carrier_id =$value;
        $detailcarrier->globalchargelcl()->associate($global);
        $detailcarrier->save();
      }
      $typerate =  $request->input('typeroute');
      if($typerate == 'port'){
        $detailport = $request->input('port_orig');
        $detailportDest = $request->input('port_dest');
        foreach($detailport as $p => $value)
        {
          foreach($detailportDest as $dest => $valuedest)
          {
            $ports = new GlobalCharPortLcl();
            $ports->port_orig = $value;
            $ports->port_dest = $valuedest;
            $ports->globalchargelcl()->associate($global);
            $ports->save();
          }
        }
      }elseif($typerate == 'country'){
        $detailCountrytOrig =$request->input('country_orig');
        $detailCountryDest = $request->input('country_dest');
        foreach($detailCountrytOrig as $p => $valueC)
        {
          foreach($detailCountryDest as $dest => $valuedestC)
          {
            $detailcountry = new GlobalCharCountryLcl();
            $detailcountry->country_orig = $valueC;
            $detailcountry->country_dest =  $valuedestC;
            $detailcountry->globalchargelcl()->associate($global);
            $detailcountry->save();
          }
        }

      }
    }
    $request->session()->flash('message.nivel', 'success');
    $request->session()->flash('message.title', 'Well done!');
    $request->session()->flash('message.content', 'You successfully add this contract.');
    return redirect()->action('GlobalChargesLclController@index');
  }

  public function updateGlobalChar(Request $request, $id)
  {
    $harbor = Harbor::all()->pluck('display_name','id');
    $carrier = Carrier::all()->pluck('name','id');
    $currency = Currency::all()->pluck('alphacode','id');
    $calculationT = CalculationTypeLcl::all()->pluck('name','id');
    $typedestiny = TypeDestiny::all()->pluck('description','id');
    $global = GlobalChargeLcl::find($id);
    $validation = explode('/',$request->validation_expire);
    $global->validity = $validation[0];
    $global->expire = $validation[1];
    $global->surcharge_id = $request->input('surcharge_id');
    $global->typedestiny_id = $request->input('changetype');
    $global->calculationtypelcl_id = $request->input('calculationtype_id');
    $global->ammount = $request->input('ammount');
    $global->minimum = $request->input('minimum');
    $global->currency_id = $request->input('currency_id');
    $carrier = $request->input('carrier_id');
    $deleteCarrier = GlobalCharCarrierLcl::where("globalchargelcl_id",$id);
    $deleteCarrier->delete();
    $deletePort = GlobalCharPortLcl::where("globalchargelcl_id",$id);
    $deletePort->delete();
    $deleteCountry = GlobalCharCountryLcl::where("globalchargelcl_id",$id);
    $deleteCountry->delete();

    $typerate =  $request->input('typeroute');
    if($typerate == 'port'){
      $port_orig = $request->input('port_orig');
      $port_dest = $request->input('port_dest');
      foreach($port_orig as  $orig => $valueorig)
      {
        foreach($port_dest as $dest => $valuedest)
        {
          $detailport = new GlobalCharPortLcl();
          $detailport->port_orig = $valueorig;
          $detailport->port_dest = $valuedest;
          $detailport->globalchargelcl_id = $id;
          $detailport->save();
        }
      }
    }elseif($typerate == 'country'){
      $detailCountrytOrig =$request->input('country_orig');
      $detailCountryDest = $request->input('country_dest');
      foreach($detailCountrytOrig as $p => $valueC)
      {
        foreach($detailCountryDest as $dest => $valuedestC)
        {
          $detailcountry = new GlobalCharCountryLcl();
          $detailcountry->country_orig = $valueC;
          $detailcountry->country_dest =  $valuedestC;
          $detailcountry->globalcharge()->associate($global);
          $detailcountry->save();
        }
      }
    }
    foreach($carrier as $key)
    {
      $detailcarrier = new GlobalCharCarrierLcl();
      $detailcarrier->carrier_id = $key;
      $detailcarrier->globalchargelcl_id = $id;
      $detailcarrier->save();
    }
    $global->update();
    return redirect()->back()->with('globalchar','true');
  }
  public function duplicateGlobalCharges($id){

    $countries = Country::pluck('name','id');
    $calculationT = CalculationTypeLcl::all()->pluck('name','id');
    $typedestiny = TypeDestiny::all()->pluck('description','id');
    $surcharge = Surcharge::where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
    $harbor = Harbor::all()->pluck('display_name','id');
    $carrier = Carrier::all()->pluck('name','id');
    $currency = Currency::all()->pluck('alphacode','id');
    $globalcharges = GlobalChargeLcl::find($id);
    $validation_expire = $globalcharges->validity ." / ". $globalcharges->expire ;
    $globalcharges->setAttribute('validation_expire',$validation_expire);
    return view('globalchargeslcl.duplicate', compact('globalcharges','harbor','carrier','currency','calculationT','typedestiny','surcharge','countries'));
  }
  public function destroyGlobalCharges($id)
  {
    $global = GlobalChargeLcl::find($id);
    $global->delete();

  }
}
