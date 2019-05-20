<?php

namespace App\Http\Controllers;

use App\CompanyUser;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
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
use App\Country;
use App\GlobalCharCountry;
use EventIntercom;

class GlobalChargesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


        /* $global =  GlobalCharge::whereHas('companyUser', function($q) {
            $q->where('company_user_id', '=', Auth::user()->company_user_id);
        })->with('globalcharport.portOrig','globalcharport.portDest','GlobalCharCarrier.carrier','typedestiny','globalcharcountry.countryOrig','globalcharcountry.countryDest')->get();

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
        $company_user=CompanyUser::find(\Auth::user()->company_user_id);
        $currency_cfg = Currency::find($company_user->currency_id);
        $surcharge = $objsurcharge->where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');

        return view('globalcharges/index', compact('global','carrier','harbor','currency','calculationT','surcharge','typedestiny','currency_cfg'));*/

        $company_userid = \Auth::user()->company_user_id;
        return view('globalcharges.indexTw', compact('company_userid'));
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
        $calculation_type = $request->input('calculationtype');


        //$changetype = $type->find($request->input('changetype.'.$key2))->toArray();
        foreach($calculation_type as $ct => $ctype)
        {

            $global = new GlobalCharge();
            $validation = explode('/',$request->validation_expire);
            $global->validity = $validation[0];
            $global->expire = $validation[1];
            $global->surcharge_id = $request->input('type');
            $global->typedestiny_id = $request->input('changetype');
            $global->calculationtype_id = $ctype;
            $global->ammount = $request->input('ammount');
            $global->currency_id = $request->input('localcurrency_id');
            $global->company_user_id = Auth::user()->company_user_id; 
            $global->save();
            // Detalles de puertos y carriers
            //$totalCarrier = count($request->input('localcarrier'.$contador));
            //$totalport =  count($request->input('port_id'.$contador));
            $detailcarrier = $request->input('localcarrier');
            foreach($detailcarrier as $c => $value)
            {

                $detailcarrier = new GlobalCharCarrier();
                $detailcarrier->carrier_id =$value;
                $detailcarrier->globalcharge()->associate($global);
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
                        $ports = new GlobalCharPort();
                        $ports->port_orig = $value;
                        $ports->port_dest = $valuedest;
                        $ports->typedestiny_id = $request->input('changetype');
                        $ports->globalcharge()->associate($global);
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
                        $detailcountry = new GlobalCharCountry();
                        $detailcountry->country_orig = $valueC;
                        $detailcountry->country_dest =  $valuedestC;
                        $detailcountry->globalcharge()->associate($global);
                        $detailcountry->save();
                    }
                }

            }
        }
        // EVENTO INTERCOM 
        $event = new  EventIntercom();
        $event->event_globalChargesFcl();

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
        $validation = explode('/',$request->validation_expire);
        $global->validity = $validation[0];
        $global->expire = $validation[1];
        $global->surcharge_id = $request->input('surcharge_id');
        $global->typedestiny_id = $request->input('changetype');
        $global->calculationtype_id = $request->input('calculationtype_id');
        $global->ammount = $request->input('ammount');
        $global->currency_id = $request->input('currency_id');



        $carrier = $request->input('carrier_id');
        $deleteCarrier = GlobalCharCarrier::where("globalcharge_id",$id);
        $deleteCarrier->delete();
        $deletePort = GlobalCharPort::where("globalcharge_id",$id);
        $deletePort->delete();
        $deleteCountry = GlobalCharCountry::where("globalcharge_id",$id);
        $deleteCountry->delete();

        $typerate =  $request->input('typeroute');
        if($typerate == 'port'){
            $port_orig = $request->input('port_orig');
            $port_dest = $request->input('port_dest');
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
        }elseif($typerate == 'country'){

            $detailCountrytOrig =$request->input('country_orig');
            $detailCountryDest = $request->input('country_dest');
            foreach($detailCountrytOrig as $p => $valueC)
            {
                foreach($detailCountryDest as $dest => $valuedestC)
                {
                    $detailcountry = new GlobalCharCountry();
                    $detailcountry->country_orig = $valueC;
                    $detailcountry->country_dest =  $valuedestC;
                    $detailcountry->globalcharge()->associate($global);
                    $detailcountry->save();
                }
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
        /*
    $global =  GlobalCharge::whereHas('companyUser', function($q) {
      $q->where('company_user_id', '=', Auth::user()->company_user_id);
    })->with('globalcharport.portOrig','globalcharport.portDest','GlobalCharCarrier.carrier','typedestiny')->get();

    return view('globalcharges/index', compact('global','carrier','harbor','currency','calculationT','surcharge','typedestiny'));*/
        return redirect()->back()->with('globalchar','true');
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
        $countries = Country::pluck('name','id');

        $calculationT = $objcalculation->all()->pluck('name','id');
        $typedestiny = $objtypedestiny->all()->pluck('description','id');
        $surcharge = $objsurcharge->where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
        $harbor = $objharbor->all()->pluck('display_name','id');
        $carrier = $objcarrier->all()->pluck('name','id');
        $currency = $objcurrency->all()->pluck('alphacode','id');
        $globalcharges = GlobalCharge::find($id);
        $validation_expire = $globalcharges->validity ." / ". $globalcharges->expire ;
        $globalcharges->setAttribute('validation_expire',$validation_expire);
        return view('globalcharges.edit', compact('globalcharges','harbor','carrier','currency','calculationT','typedestiny','surcharge','countries'));
    }

    public function addGlobalChar(){

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
        $company_user=CompanyUser::find(\Auth::user()->company_user_id);
        $currency_cfg = Currency::find($company_user->currency_id);

        return view('globalcharges.add', compact('harbor','carrier','currency','calculationT','typedestiny','surcharge','countries','currency_cfg'));
    }

    public function duplicateGlobalCharges($id){

        $countries = Country::pluck('name','id');
        $calculationT = CalculationType::all()->pluck('name','id');
        $typedestiny = TypeDestiny::all()->pluck('description','id');
        $surcharge = Surcharge::where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
        $harbor = Harbor::all()->pluck('display_name','id');
        $carrier = Carrier::all()->pluck('name','id');
        $currency = Currency::all()->pluck('alphacode','id');
        $globalcharges = GlobalCharge::find($id);
        $validation_expire = $globalcharges->validity ." / ". $globalcharges->expire ;
        $globalcharges->setAttribute('validation_expire',$validation_expire);
        return view('globalcharges.duplicate', compact('globalcharges','harbor','carrier','currency','calculationT','typedestiny','surcharge','countries'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $globalcharges = DB::select('call  select_for_company_globalcharger('.$id.')');

        return DataTables::of($globalcharges)
            ->editColumn('surchargelb', function ($globalcharges){ 
                return $globalcharges->surcharges;
            })
            ->editColumn('origin_portLb', function ($globalcharges){ 
                if(empty($globalcharges->port_orig) != true){
                    return $globalcharges->port_orig;
                } else if(empty($globalcharges->country_orig) != true) {
                    return $globalcharges->country_orig; 
                }
            })
            ->editColumn('destiny_portLb', function ($globalcharges){ 
                if(empty($globalcharges->port_dest) != true){
                    return $globalcharges->port_dest;
                } else if(empty($globalcharges->country_dest) != true) {
                    return $globalcharges->country_dest; 
                }
            })
            ->editColumn('typedestinylb', function ($globalcharges){ 
                return $globalcharges->typedestiny;
            })
            ->editColumn('calculationtypelb', function ($globalcharges){ 
                return $globalcharges->calculationtype;
            })
            ->editColumn('currencylb', function ($globalcharges){ 
                return $globalcharges->currency;
            })
            ->editColumn('carrierlb', function ($globalcharges){ 
                return $globalcharges->carrier;
            })
            ->editColumn('validitylb', function ($globalcharges){ 
                return $globalcharges->validity.'/'.$globalcharges->expire;
            })
            ->addColumn('action', function ( $globalcharges) {
                return '
                    <a  id="edit_l" onclick="AbrirModal('."'editGlobalCharge'".','.$globalcharges->id.')" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                    <i class="la la-edit"></i>
                    </a>

                    <a  id="remove_l{{$loop->index}}"  class="m_sweetalert_demo_8 m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="delete" >
											<i id="rm_l'.$globalcharges->id.'" class="la la-times-circle"></i>
										</a>

                    <a   class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test"  title="Duplicate "  onclick="AbrirModal('."'duplicateGlobalCharge'".','.$globalcharges->id.')">
											<i class="la la-plus"></i>
										</a>
                    ';
            })
            ->addColumn('checkbox', '<input type="checkbox" name="check[]" class="checkbox_global" value="{{$id}}" />')
            ->rawColumns(['checkbox','action'])
            ->editColumn('id', 'ID: {{$id}}')->toJson();
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
        
    }
    
    public function destroyArr(Request $request)
    {
        $globals_id_array = $request->input('id');
        $global = GlobalCharge::whereIn('id', $globals_id_array);
        if($global->delete())
        {
             return response()->json(['success' => '1']);
        } else {
             return response()->json(['success' => '2']);
        }
    }
}
