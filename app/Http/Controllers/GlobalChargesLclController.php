<?php

namespace App\Http\Controllers;

use App\Rate;
use App\Harbor;
use App\Carrier;
use App\Country;
use App\Currency;
use EventIntercom;
use App\Surcharge;
use App\CompanyUser;
use App\TypeDestiny;
use App\GlobalChargeLcl;
use App\GlobalCharPortLcl;
use App\CalculationTypeLcl;
use App\ViewGlobalchargeLcl;
use Illuminate\Http\Request;
use App\GlobalCharCountryLcl;
use App\GlobalCharCarrierLcl;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Jobs\GlobalchargerDuplicateFclLclJob as GCDplFclLcl;

class GlobalChargesLclController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company_userid = \Auth::user()->company_user_id;
        return view('globalchargeslcl.indexTw', compact('company_userid'));
    }

    public function show($id){
        $globalcharges = DB::select('call  select_for_company_globalcharger_lcl('.$id.')');

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

                    <a  id="edit_l{{$loop->index}}" onclick="AbrirModal('."'editGlobalCharge'".','.$globalcharges->id.')" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                      <i class="la la-edit"></i>
                    </a>    

                    <a  id="remove_l{{$loop->index}}"  class="m_sweetalert_demo_8 m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="delete" >
                      <i id="rm_l'.$globalcharges->id.'" class="la la-times-circle"></i>
                    </a>

                    <a   class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test"  title="Duplicate "  onclick="AbrirModal('."'duplicateGlobalCharge'".','.$globalcharges->id.')">
                      <i class="la la-plus"></i>
                    </a>';
            })
            ->addColumn('checkbox', '<input type="checkbox" name="check[]" class="checkbox_global" value="{{$id}}" />')
            ->rawColumns(['checkbox','action'])
            ->editColumn('id', 'ID: {{$id}}')->toJson();
    }

    public function addGlobalChar(){

        $countries = Country::pluck('name','id');
        $calculationT = CalculationTypeLcl::all()->pluck('name','id');
        $typedestiny = TypeDestiny::all()->pluck('description','id');
        $surcharge = Surcharge::where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
        $harbor = Harbor::all()->pluck('display_name','id');
        $carrier = Carrier::all()->pluck('name','id');
        $currency = Currency::all()->pluck('alphacode','id');
        $company_user=CompanyUser::find(\Auth::user()->company_user_id);
        $currency_cfg = Currency::find($company_user->currency_id);
        return view('globalchargeslcl.add', compact('harbor','carrier','currency','calculationT','typedestiny','surcharge','countries','currency_cfg'));
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


    public function store(Request $request){
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
        // EVENTO INTERCOM
        $event = new  EventIntercom();
        $event->event_globalChargesLcl();

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully add this contract.');
        return redirect()->action('GlobalChargesLclController@index');
    }

    public function updateGlobalChar(Request $request, $id){
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
                    $detailcountry->globalchargelcl_id = $global->id;
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
    public function destroyArr(Request $request)
    {
        $globals_id_array = $request->input('id');
        $global = GlobalChargeLcl::whereIn('id', $globals_id_array);
        if($global->delete())
        {
            return response()->json(['success' => '1']);
        } else {
            return response()->json(['success' => '2']);
        }
    }

    // CRUD Administarator -----------------------------------------------------------------------------------------------------

    public function indexAdm(Request $request){
        $companies = CompanyUser::pluck('name','id');
        $carriers = Carrier::pluck('name','id');
        $company_user_id_selec  = $request->input('company_user_id_selec');
        $carrier_id_selec       = $request->input('carrier_id_selec');
        $reload_DT      = $request->input('reload_DT');
        return view('globalchargesLclAdm.index',compact('companies','carriers','company_user_id_selec','carrier_id_selec','reload_DT'));
    }

    public function createAdm(Request $request){
        $globalcharges = ViewGlobalchargeLcl::select(['id','port_orig','port_dest','country_orig','country_dest','carrier','surcharges','typedestiny','calculationtype','ammount','minimum','validity','expire','currency','company_user'])->companyUser($request->company_id)->carrier($request->carrier);
        //dd($globalcharges);
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

                    <a  id="edit_l{{$loop->index}}" onclick="AbrirModal('."'editGlobalCharge'".','.$globalcharges->id.')" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                      <i class="la la-edit"></i>
                    </a>    

                    <a   class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test"  title="Duplicate "  onclick="AbrirModal('."'duplicateGlobalCharge'".','.$globalcharges->id.')">
                      <i class="la la-plus"></i>
                    </a>';
            })
            //->addColumn('checkbox', '<input type="checkbox" name="check[]" class="checkbox_global" value="{{$id}}" />')
            //->rawColumns(['checkbox','action'])
            ->editColumn('id', '{{$id}}')->toJson();
    }

    public function addAdm(Request $request){
        $company_user_id_selec  = $request->input('company_user_id_selec');
        $carrier_id_selec       = $request->input('carrier_id_selec');
        $reload_DT              = $request->input('reload_DT');
        $countries              = Country::pluck('name','id');
        $calculationT           = CalculationTypeLcl::all()->pluck('name','id');
        $typedestiny            = TypeDestiny::all()->pluck('description','id');
        $surcharge              = Surcharge::where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
        $harbor                 = Harbor::all()->pluck('display_name','id');
        $carrier                = Carrier::all()->pluck('name','id');
        $currency               = Currency::all()->pluck('alphacode','id');
        $company_user           = CompanyUser::find(\Auth::user()->company_user_id);
        $currency_cfg           = Currency::find($company_user->currency_id);
        $company_usersO         = CompanyUser::all();
        $company_users          = ['null'=>'Please Select'];
        foreach($company_usersO as $d){
            $company_users[$d['id']]=$d->name;
        }
        return view('globalchargesLclAdm.add', compact('harbor','carrier','company_users','currency',
                                                       'calculationT','typedestiny','surcharge',
                                                       'countries','currency_cfg','company_user_id_selec',
                                                       'carrier_id_selec','reload_DT'));
    }

    public function typeChargeAdm(Request $request,$id){
        if($request->ajax()){
            if($id != 'null'){
                $surcharges = Surcharge::where('company_user_id','=',$id)->get();
            } else{
                $surcharges = null;
            }
            return response()->json($surcharges);
        }
    }

    public function storeAdm(Request $request){
        $detailscharges         = $request->input('type');
        $calculation_type       = $request->input('calculationtype');
        $company_user_id_selec  = $request->input('company_user_id_selec');
        $carrier_id_selec       = $request->input('carrier_id_selec');
        $reload_DT              = $request->input('reload_DT');

        //$changetype = $type->find($request->input('changetype.'.$key2))->toArray();
        foreach($calculation_type as $ct => $ctype)
        {
            $global                         = new GlobalChargeLcl();
            $validation                     = explode('/',$request->validation_expire);
            $global->validity               = $validation[0];
            $global->expire                 = $validation[1];
            $global->surcharge_id           = $request->input('type');
            $global->typedestiny_id         = $request->input('changetype');
            $global->calculationtypelcl_id  = $ctype;
            $global->ammount                = $request->input('ammount');
            $global->minimum                = $request->input('minimum');
            $global->currency_id            = $request->input('localcurrency_id');
            $global->company_user_id        = $request->company_user_id;
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
        return redirect()->route('gclcladm.index',compact('company_user_id_selec','carrier_id_selec','reload_DT'));
    }

    public function showAdm(Request $request,$id){
        $company_user_id_selec  = $request->input('company_user_id_selec');
        $carrier_id_selec       = $request->input('carrier_id_selec');
        $reload_DT      = $request->input('reload_DT');
        $globalcharges  = GlobalChargeLcl::find($id);
        $countries      = Country::pluck('name','id');
        $calculationT   = CalculationTypeLcl::all()->pluck('name','id');
        $typedestiny    = TypeDestiny::all()->pluck('description','id');
        $surcharge      = Surcharge::where('company_user_id','=',$globalcharges->company_user_id)->pluck('name','id');
        $harbor         = Harbor::all()->pluck('display_name','id');
        $carrier        = Carrier::all()->pluck('name','id');
        $currency       = Currency::all()->pluck('alphacode','id');
        $validation_expire = $globalcharges->validity ." / ". $globalcharges->expire ;
        $globalcharges->setAttribute('validation_expire',$validation_expire);
        $company_users   = CompanyUser::pluck('name','id');
        return view('globalchargesLclAdm.edit', compact('globalcharges','harbor','carrier','currency','calculationT','company_users','typedestiny','surcharge','countries','company_user_id_selec','carrier_id_selec','reload_DT'));
    }

    public function updateAdm(Request $request, $id){
        $company_user_id_selec  = $request->input('company_user_id_selec');
        $carrier_id_selec       = $request->input('carrier_id_selec');
        $reload_DT              = $request->input('reload_DT');

        $harbor         = Harbor::pluck('display_name','id');
        $carrier        = Carrier::pluck('name','id');
        $currency       = Currency::pluck('alphacode','id');
        $calculationT   = CalculationTypeLcl::pluck('name','id');
        $typedestiny    = TypeDestiny::pluck('description','id');
        $global         = GlobalChargeLcl::find($id);

        $validation             = explode('/',$request->validation_expire);
        $global->validity       = $validation[0];
        $global->expire         = $validation[1];
        $global->surcharge_id   = $request->input('surcharge_id');
        $global->typedestiny_id = $request->input('changetype');
        $global->calculationtypelcl_id = $request->input('calculationtype_id');
        $global->ammount        = $request->input('ammount');
        $global->minimum        = $request->input('minimum');
        $global->currency_id    = $request->input('currency_id');
        $global->company_user_id = $request->input('company_user_id');
        $carrier                = $request->input('carrier_id');

        $deleteCarrier  = GlobalCharCarrierLcl::where("globalchargelcl_id",$id);
        $deleteCarrier->delete();
        $deletePort     = GlobalCharPortLcl::where("globalchargelcl_id",$id);
        $deletePort->delete();
        $deleteCountry  = GlobalCharCountryLcl::where("globalchargelcl_id",$id);
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
                    $detailcountry->globalchargelcl_id = $global->id;
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
        return redirect()->route('gclcladm.index',compact('company_user_id_selec','carrier_id_selec','reload_DT'));
    }

    public function duplicateAdm(Request $request,$id){
        $company_user_id_selec  = $request->input('company_user_id_selec');
        $carrier_id_selec       = $request->input('carrier_id_selec');
        $reload_DT              = $request->input('reload_DT');

        $countries          = Country::pluck('name','id');
        $calculationT       = CalculationTypeLcl::all()->pluck('name','id');
        $typedestiny        = TypeDestiny::all()->pluck('description','id');
        $globalcharges      = GlobalChargeLcl::find($id);
        $surcharge          = Surcharge::where('company_user_id','=',$globalcharges->company_user_id)->pluck('name','id');
        $harbor             = Harbor::all()->pluck('display_name','id');
        $carrier            = Carrier::all()->pluck('name','id');
        $currency           = Currency::all()->pluck('alphacode','id');
        $validation_expire  = $globalcharges->validity ." / ". $globalcharges->expire ;
        $company_users      = CompanyUser::pluck('name','id');
        $globalcharges->setAttribute('validation_expire',$validation_expire);
        return view('globalchargesLclAdm.duplicate', compact('globalcharges','harbor','carrier','currency','company_users','calculationT','typedestiny','surcharge','countries','company_user_id_selec','carrier_id_selec','reload_DT'));
    }

    public function duplicateArrAdm(Request $request){
        $company_user_id_selec  = $request->input('company_user_id_selec');
        $carrier_id_selec       = $request->input('carrier_id_selec');
        $reload_DT              = $request->input('reload_DT');

        $company_users      = CompanyUser::pluck('name','id');
        $globals_id_array   = $request->input('id');
        $count = count($globals_id_array);
        //$global             = $global->toArray();
        //dd($globals_id_array);
        return view('globalchargesLclAdm.duplicateArray',compact('count','company_users','globals_id_array','company_user_id_selec','carrier_id_selec','reload_DT'));
    }

    public function storeArrayAdm(Request $request){
        //dd($request->all());
        $company_user_id_selec  = $request->input('company_user_id_selec');
        $carrier_id_selec       = $request->input('carrier_id_selec');
        $reload_DT              = $request->input('reload_DT');
        $requestJob = $request->all();
        GCDplFclLcl::dispatch($requestJob,'lcl');
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully add this contract.');
        return redirect()->route('gclcladm.index',compact('company_user_id_selec','carrier_id_selec','reload_DT'));
    }
    
    public function editDateArrAdm(Request $request){
        $company_user_id_selec  = $request->input('company_user_id_selec');
        $carrier_id_selec       = $request->input('carrier_id_selec');
        $reload_DT      = $request->input('reload_DT');

        $company_users      = CompanyUser::pluck('name','id');
        $globals_id_array   = $request->input('idAr');
        $count = count($globals_id_array);
        //$global             = $global->toArray();
        return view('globalchargesLclAdm.EditDatesArray',compact('count','company_users','globals_id_array','company_user_id_selec','carrier_id_selec','reload_DT'));
    }

    public function updateDateArrAdm(Request $request){
        //dd($request->all());
        $date = explode('/',$request->validation_expire);
        $date_start = trim($date[0]);
        $date_end   = trim($date[1]);
        foreach($request->idArray as $global){
            $globalObj = null;
            $globalObj              = GlobalChargeLcl::find($global);
            $globalObj->validity    = $date_start;
            $globalObj->expire      = $date_end;
            $globalObj->update();
        }

        $company_user_id_selec  = $request->input('company_user_id_selec');
        $carrier_id_selec       = $request->input('carrier_id_selec');
        $reload_DT              = $request->input('reload_DT');

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully updated.');
        return redirect()->route('gclcladm.index',compact('company_user_id_selec','carrier_id_selec','reload_DT'));
    }

}
