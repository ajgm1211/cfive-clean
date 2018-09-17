<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\GlobalCharge;
use App\Carrier;
use App\Harbor;
use App\Rate;
use App\Currency;
use App\CalculationType;
use App\Surcharge;
use App\GlobalCharPort;
use App\GlobalCharCarrier;
use App\TypeDestiny;

class GlobalChargesController extends Controller
{
  /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
  public function index()
  {

    $global =  GlobalCharge::whereHas('companyUser', function($q) {
      $q->where('company_user_id', '=', Auth::user()->company_user_id);
    })->with('globalcharport.portOrig','globalcharport.portDest','GlobalCharCarrier.carrier','typedestiny')->get();


    $objcarrier = new Carrier();
    $objharbor = new Harbor();
    $objcurrency = new Currency();
    $objcalculation = new CalculationType();
    $objsurcharge = new Surcharge();
    $objtypedestiny = new TypeDestiny();
    $harbor = $objharbor->all()->pluck('display_name','id');
    $carrier = $objcarrier->all()->pluck('name','id');
    $currency = $objcurrency->all()->pluck('alphacode','id');
    $calculationT = $objcalculation->all()->pluck('name','id');
    $typedestiny = $objtypedestiny->all()->pluck('description','id');
    $surcharge = $objsurcharge->where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');

    return view('globalcharges/index', compact('global','carrier','harbor','currency','calculationT','surcharge','typedestiny'));
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
  public function store(Request $request){
    $detailscharges = $request->input('type');
    //$changetype = $type->find($request->input('changetype.'.$key2))->toArray();
    $global = new GlobalCharge();
    $global->surcharge_id = $request->input('type');
    $global->typedestiny_id = $request->input('changetype');
    $global->calculationtype_id = $request->input('calculationtype');
    $global->ammount = $request->input('ammount');
    $global->currency_id = $request->input('localcurrency_id');
    $global->company_user_id = Auth::user()->company_user_id; 
    $global->save();

    // Detalles de puertos y carriers
    //$totalCarrier = count($request->input('localcarrier'.$contador));
    //$totalport =  count($request->input('port_id'.$contador));
    $detailport = $request->input('port_orig');
    $detailportDest = $request->input('port_dest');
    $detailcarrier = $request->input('localcarrier');



    foreach($detailcarrier as $c => $value)
    {

      $detailcarrier = new GlobalCharCarrier();
      $detailcarrier->carrier_id =$value;
      $detailcarrier->globalcharge()->associate($global);
      $detailcarrier->save();
    }
    foreach($detailport as $p => $value)
    {
      foreach($detailportDest as $dest => $valuedest)
      {
        $ports = new GlobalCharPort();
        $ports->port_orig = $value;
        $ports->port_dest = $valuedest;
        $ports->typedestiny_id = $request->input('changetype');
        $ports->globalcharge()->associate($global);
        $ports->save();
      }

    }



    $request->session()->flash('message.nivel', 'success');
    $request->session()->flash('message.title', 'Well done!');
    $request->session()->flash('message.content', 'You successfully add this contract.');

    return redirect()->action('GlobalChargesController@index');
  }


  public function updateGlobalChar(Request $request, $id)
  {


    $objcarrier = new Carrier();
    $objharbor = new Harbor();
    $objcurrency = new Currency();
    $objcalculation = new CalculationType();
    $objsurcharge = new Surcharge();
    $objtypedestiny = new TypeDestiny();
    $harbor = $objharbor->all()->pluck('display_name','id');
    $carrier = $objcarrier->all()->pluck('name','id');
    $currency = $objcurrency->all()->pluck('alphacode','id');
    $calculationT = $objcalculation->all()->pluck('name','id');
    $typedestiny = $objtypedestiny->all()->pluck('description','id');
    //dd($request);
    /* $type =  TypeDestiny::all();
        $changetype = $type->find($request->input('changetype'))->toArray();*/
    $global = GlobalCharge::find($id);
    $global->surcharge_id = $request->input('surcharge_id');
    $global->typedestiny_id = $request->input('changetype');
    $global->calculationtype_id = $request->input('calculationtype_id');
    $global->ammount = $request->input('ammount');
    $global->currency_id = $request->input('currency_id');


    $port_orig = $request->input('port_orig');
    $port_dest = $request->input('port_dest');

    $carrier = $request->input('carrier_id');
    $deleteCarrier = GlobalCharCarrier::where("globalcharge_id",$id);
    $deleteCarrier->delete();
    $deletePort = GlobalCharPort::where("globalcharge_id",$id);
    $deletePort->delete();
    foreach($port_orig as  $orig => $valueorig)
    {
      foreach($port_dest as $dest => $valuedest)
      {
        $detailport = new GlobalCharPort();
        $detailport->port_orig = $valueorig;
        $detailport->port_dest = $valuedest;
        $detailport->typedestiny_id = $request->input('changetype');
        $detailport->globalcharge_id = $id;
        $detailport->save();
      }
    }
    foreach($carrier as $key)
    {
      $detailcarrier = new GlobalCharCarrier();
      $detailcarrier->carrier_id = $key;
      $detailcarrier->globalcharge_id = $id;
      $detailcarrier->save();
    }

    $global->update();
    $global =  GlobalCharge::whereHas('companyUser', function($q) {
      $q->where('company_user_id', '=', Auth::user()->company_user_id);
    })->with('globalcharport.portOrig','globalcharport.portDest','GlobalCharCarrier.carrier','typedestiny')->get();
    return view('globalcharges/index', compact('global','carrier','harbor','currency','calculationT','surcharge','typedestiny'));
  }
  public function destroyGlobalCharges($id)
  {

    $global = GlobalCharge::find($id);
    $global->delete();

  }
  public function editGlobalChar($id){
    $objcarrier = new Carrier();
    $objharbor = new Harbor();
    $objcurrency = new Currency();
    $objtypedestiny = new TypeDestiny();
    $objcalculation = new CalculationType();
    $objsurcharge = new Surcharge();

    $calculationT = $objcalculation->all()->pluck('name','id');
    $typedestiny = $objtypedestiny->all()->pluck('description','id');
    $surcharge = $objsurcharge->where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
    $harbor = $objharbor->all()->pluck('display_name','id');
    $carrier = $objcarrier->all()->pluck('name','id');
    $currency = $objcurrency->all()->pluck('alphacode','id');
    $globalcharges = GlobalCharge::find($id);
    return view('globalcharges.edit', compact('globalcharges','harbor','carrier','currency','calculationT','typedestiny','surcharge'));
  }

  public function addGlobalChar(){

    $objcarrier = new Carrier();
    $objharbor = new Harbor();
    $objcurrency = new Currency();
    $objtypedestiny = new TypeDestiny();
    $objcalculation = new CalculationType();
    $objsurcharge = new Surcharge();

    $calculationT = $objcalculation->all()->pluck('name','id');
    $typedestiny = $objtypedestiny->all()->pluck('description','id');
    $surcharge = $objsurcharge->where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
    $harbor = $objharbor->all()->pluck('display_name','id');
    $carrier = $objcarrier->all()->pluck('name','id');
    $currency = $objcurrency->all()->pluck('alphacode','id');

    return view('globalcharges.add', compact('harbor','carrier','currency','calculationT','typedestiny','surcharge'));
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
