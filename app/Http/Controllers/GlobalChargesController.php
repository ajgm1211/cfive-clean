<?php

namespace App\Http\Controllers;

use App\Rate;
use App\Harbor;
use App\Carrier;
use App\Country;
use App\Currency;
use App\Surcharge;
use EventIntercom;
use App\CompanyUser;
use App\TypeDestiny;
use App\GlobalCharge;
use App\GlobalCharPort;
use App\CalculationType;
use App\ViewGlobalCharge;
use App\GlobalCharCarrier;
use App\GlobalCharCountry;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Jobs\GlobalchargerDuplicateFclLclJob as GCDplFclLcl;
use Illuminate\Support\Collection as Collection;

class GlobalChargesController extends Controller
{
    public function index()
    {
        $company_userid = \Auth::user()->company_user_id;
        return view('globalcharges.indexTw', compact('company_userid'));
    }

    public function create()
    {
        //
    }

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
            ->editColumn('id', '{{$id}}')->toJson();
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

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

    // CRUD Administarator -----------------------------------------------------------------------------------------------------

    public function indexAdm(Request $request){
        $companies              = CompanyUser::pluck('name','id');
        $carriers               = Carrier::pluck('name','id');
        $company_user_id_selec  = $request->input('company_user_id_selec');
        $carrier_id_selec       = $request->input('carrier_id_selec');
        $reload_DT              = $request->input('reload_DT');

        return view('globalchargesAdm.index',compact('companies','carriers','company_user_id_selec','carrier_id_selec','reload_DT'));
    }

    public function createAdm(Request $request){
        $globalcharges = ViewGlobalCharge::select(['id','charge','charge_type','calculation_type','origin_port','origin_country','destination_port','destination_country','carrier','amount','currency_code','valid_from','valid_until','company_user'])->companyUser($request->company_id)->carrier($request->carrier);

        return DataTables::of($globalcharges)
            ->editColumn('surchargelb', function ($globalcharges){ 
                return $globalcharges->charge;
            })
            ->editColumn('origin_portLb', function ($globalcharges){ 
                if(empty($globalcharges->origin_port) != true){
                    return $globalcharges->origin_port;
                } else if(empty($globalcharges->origin_country) != true) {
                    return $globalcharges->origin_country; 
                }
            })
            ->editColumn('destiny_portLb', function ($globalcharges){ 
                if(empty($globalcharges->destination_port) != true){
                    return $globalcharges->destination_port;
                } else if(empty($globalcharges->destination_country) != true) {
                    return $globalcharges->destination_country; 
                }
            })
            ->editColumn('typedestinylb', function ($globalcharges){ 
                return $globalcharges->charge_type;
            })
            ->editColumn('calculationtypelb', function ($globalcharges){ 
                return $globalcharges->calculation_type;
            })
            ->editColumn('currencylb', function ($globalcharges){ 
                return $globalcharges->currency_code;
            })
            ->editColumn('carrierlb', function ($globalcharges){ 
                return $globalcharges->carrier;
            })
            ->editColumn('validitylb', function ($globalcharges){ 
                return $globalcharges->valid_from.'/'.$globalcharges->valid_until;
            })
            ->addColumn('action', function ( $globalcharges) {
                return '<a  id="edit_l" onclick="AbrirModal('."'editGlobalCharge'".','.$globalcharges->id.')" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                    <i class="la la-edit"></i>
                    </a>

                    <!--<a  id="remove_l{{$loop->index}}"  class="m_sweetalert_demo_8 m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="delete" >
											<i id="rm_l'.$globalcharges->id.'" class="la la-times-circle"></i>
										</a>-->

                    <a   class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test"  title="Duplicate "  onclick="AbrirModal('."'duplicateGlobalCharge'".','.$globalcharges->id.')">
											<i class="la la-plus"></i>
				   </a>';
            })
            //->addColumn('checkbox', '<input type="checkbox" name="check[]" class="checkbox_global" value="{{$id}}" />')
            //->rawColumns(['checkbox','action'])
            ->editColumn('id', '{{$id}}')->toJson();
    }

    public function createAdm_proc(Request $request){
        /*  $globalcharges = ViewGlobalCharge::select(['id','charge','charge_type','calculation_type','origin_port','origin_country','destination_port','destination_country','carrier','amount','currency_code','valid_from','valid_until','company_user'])->companyUser($request->company_id)->carrier($request->carrier);*/

        $co=0;
        $ca=0;

        if($request->company_id){
            $co = $request->company_id; 
        }

        if($request->carrier){
            $ca =  $request->carrier;
        }

        $data1 = \DB::select(\DB::raw('call select_globalcharge_adm('.$co.','.$ca.')'));
        $globalcharges = new Collection;
        for ($i = 0; $i < count($data1); $i++) {
            $globalcharges->push([
                'id' => $data1[$i]->id,
                'charge' =>  $data1[$i]->charge,
                'charge_type' =>   $data1[$i]->charge_type,
                'calculation_type' =>   $data1[$i]->calculation_type,
                'origin_port' =>  $data1[$i]->origin_port,
                'origin_country' =>   $data1[$i]->origin_country,
                'destination_port' =>  $data1[$i]->destination_port,
                'destination_country' =>   $data1[$i]->destination_country,
                'carrier' => $data1[$i]->carrier,
                'amount' =>   $data1[$i]->amount,
                'currency_code' =>   $data1[$i]->currency_code,
                'valid_from' =>   $data1[$i]->valid_from,
                'valid_until' =>   $data1[$i]->valid_until,
                'company_user' =>   $data1[$i]->company_user

            ]);
        }



        return DataTables::of($globalcharges)
            ->addColumn('surchargelb', function ($globalcharges){ 
                return $globalcharges['charge'];
            })
            ->addColumn('origin_portLb', function ($globalcharges){ 
                if(empty($globalcharges['origin_port']) != true){
                    return $globalcharges['origin_port'];
                } else if(empty($globalcharges['origin_country']) != true) {
                    return $globalcharges['origin_country']; 
                }
            })
            ->addColumn('destiny_portLb', function ($globalcharges){ 
                if(empty($globalcharges['destination_port']) != true){
                    return $globalcharges['destination_port'];
                } else if(empty($globalcharges['destination_country']) != true) {
                    return $globalcharges['destination_country']; 
                }
            })
            ->addColumn('typedestinylb', function ($globalcharges){ 
                return $globalcharges['charge_type'];
            })
            ->addColumn('calculationtypelb', function ($globalcharges){ 
                return $globalcharges['calculation_type'];
            })
            ->addColumn('currencylb', function ($globalcharges){ 
                return $globalcharges['currency_code'];
            })
            ->addColumn('carrierlb', function ($globalcharges){ 
                return $globalcharges['carrier'];
            })
            ->addColumn('validitylb', function ($globalcharges){ 
                return $globalcharges['valid_from'].'/'.$globalcharges['valid_until'];
            })
            ->addColumn('action', function ( $globalcharges) {
                return '<a  id="edit_l" onclick="AbrirModal('."'editGlobalCharge'".','.$globalcharges['id'].')" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                    <i class="la la-edit"></i>
                    </a>

                    <!--<a  id="remove_l{{$loop->index}}"  class="m_sweetalert_demo_8 m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="delete" >
											<i id="rm_l'.$globalcharges['id'].'" class="la la-times-circle"></i>
										</a>-->

                    <a   class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test"  title="Duplicate "  onclick="AbrirModal('."'duplicateGlobalCharge'".','.$globalcharges['id'].')">
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
        $reload_DT      = $request->input('reload_DT');
        $countries      = Country::pluck('name','id');
        $calculationT   = CalculationType::pluck('name','id');
        $typedestiny    = TypeDestiny::pluck('description','id');
        $harbor         = Harbor::pluck('display_name','id');
        $carrier        = Carrier::pluck('name','id');
        $currency       = Currency::pluck('alphacode','id');
        $company_usersO = CompanyUser::all();
        $currency_cfg   = Currency::pluck('name','id');
        $company_users  = ['null'=>'Please Select'];
        foreach($company_usersO as $d){
            $company_users[$d['id']]=$d->name;
        }
        return view('globalchargesAdm.add', compact('harbor','carrier','currency','calculationT','company_users','typedestiny','countries','currency_cfg','company_user_id_selec','carrier_id_selec','reload_DT'));
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
        //dd($request);
        $company_user_id_selec  = $request->input('company_user_id_selec');
        $carrier_id_selec       = $request->input('carrier_id_selec');
        $reload_DT      = $request->input('reload_DT');

        $detailscharges = $request->input('type');
        $calculation_type = $request->input('calculationtype');

        //$changetype = $type->find($request->input('changetype.'.$key2))->toArray();
        foreach($calculation_type as $ct => $ctype)
        {

            $global                     = new GlobalCharge();
            $validation                 = explode('/',$request->validation_expire);
            $global->validity           = $validation[0];
            $global->expire             = $validation[1];
            $global->surcharge_id       = $request->input('type');
            $global->typedestiny_id     = $request->input('changetype');
            $global->calculationtype_id = $ctype;
            $global->ammount            = $request->input('ammount');
            $global->currency_id        = $request->input('localcurrency_id');
            $global->company_user_id    = $request->company_user_id; 
            $global->save();

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
                        $ports                  = new GlobalCharPort();
                        $ports->port_orig       = $value;
                        $ports->port_dest       = $valuedest;
                        $ports->typedestiny_id  = $request->input('changetype');
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

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully add this contract.');

        return redirect()->route('gcadm.index',compact('company_user_id_selec','carrier_id_selec','reload_DT'));
    }

    public function showAdm(Request $request,$id){

        $objsurcharge = new Surcharge();
        $countries          = Country::pluck('name','id');
        $company_user_id_selec  = $request->input('company_user_id_selec');
        $carrier_id_selec       = $request->input('carrier_id_selec');
        $reload_DT              = $request->input('reload_DT');

        $globalcharges      = GlobalCharge::find($id);
        $calculationT       = CalculationType::pluck('name','id');
        $typedestiny        = TypeDestiny::pluck('description','id');
        $surcharge          = Surcharge::where('company_user_id','=',$globalcharges->company_user_id)->pluck('name','id');
        $harbor             = Harbor::pluck('display_name','id');
        $carrier            = Carrier::pluck('name','id');
        $currency           = Currency::pluck('alphacode','id');
        $company_users      = CompanyUser::pluck('name','id');
        $validation_expire  = $globalcharges->validity ." / ". $globalcharges->expire ;
        $globalcharges->setAttribute('validation_expire',$validation_expire);
        return view('globalchargesAdm.edit', compact('globalcharges','harbor','carrier','currency','company_users','calculationT','typedestiny','surcharge','countries','company_user_id_selec','carrier_id_selec','reload_DT'));
    }

    public function updateAdm(Request $request, $id){
        //dd($request->all()) ;

        $harbor         = Harbor::pluck('display_name','id');
        $carrier        = Carrier::pluck('name','id');
        $currency       = Currency::pluck('alphacode','id');
        $calculationT   = CalculationType::pluck('name','id');
        $typedestiny    = TypeDestiny::pluck('description','id');

        $company_user_id_selec  = $request->input('company_user_id_selec');
        $carrier_id_selec       = $request->input('carrier_id_selec');
        $reload_DT      = $request->input('reload_DT');

        $global                     = GlobalCharge::find($id);
        $validation                 = explode('/',$request->validation_expire);
        $global->validity           = $validation[0];
        $global->expire             = $validation[1];
        $global->surcharge_id       = $request->input('surcharge_id');
        $global->typedestiny_id     = $request->input('changetype');
        $global->calculationtype_id = $request->input('calculationtype_id');
        $global->ammount            = $request->input('ammount');
        $global->currency_id        = $request->input('currency_id');
        $global->company_user_id    = $request->input('company_user_id');

        $carrierInp     = $request->input('carrier_id');
        $deleteCarrier  = GlobalCharCarrier::where("globalcharge_id",$id);
        $deleteCarrier->delete();
        $deletePort     = GlobalCharPort::where("globalcharge_id",$id);
        $deletePort->delete();
        $deleteCountry  = GlobalCharCountry::where("globalcharge_id",$id);
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
                    $detailport->port_orig       = $valueorig;
                    $detailport->port_dest       = $valuedest;
                    $detailport->typedestiny_id  = $request->input('changetype');
                    $detailport->globalcharge_id = $id;
                    $detailport->save();
                }
            }
        } elseif($typerate == 'country'){

            $detailCountrytOrig = $request->input('country_orig');
            $detailCountryDest  = $request->input('country_dest');
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

        foreach($carrierInp as $key)
        {
            $detailcarrier = new GlobalCharCarrier();
            $detailcarrier->carrier_id      = $key;
            $detailcarrier->globalcharge_id = $id;
            $detailcarrier->save();
        }

        $global->update();

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully updated this contract.');
        return redirect()->route('gcadm.index',compact('company_user_id_selec','carrier_id_selec','reload_DT'));
    }

    public function dupicateAdm(Request $request,$id){

        $company_user_id_selec  = $request->input('company_user_id_selec');
        $carrier_id_selec       = $request->input('carrier_id_selec');
        $reload_DT          = $request->input('reload_DT');
        $globalcharges      = GlobalCharge::find($id);
        $harbor             = Harbor::pluck('display_name','id');
        $carrier            = Carrier::pluck('name','id');
        $currency           = Currency::pluck('alphacode','id');
        $surcharge          = Surcharge::where('company_user_id','=',$globalcharges->company_user_id)->pluck('name','id');
        $countries          = Country::pluck('name','id');
        $typedestiny        = TypeDestiny::pluck('description','id');
        $calculationT       = CalculationType::pluck('name','id');
        $company_users      = CompanyUser::pluck('name','id');
        $validation_expire  = $globalcharges->validity ." / ". $globalcharges->expire ;
        $globalcharges->setAttribute('validation_expire',$validation_expire);

        return view('globalchargesAdm.duplicate', compact('globalcharges','harbor','carrier','company_users','currency','calculationT','typedestiny','surcharge','countries','company_user_id_selec','carrier_id_selec','reload_DT'));
    }

    public function dupicateArrAdm(Request $request){
        $company_user_id_selec  = $request->input('company_user_id_selec');
        $carrier_id_selec       = $request->input('carrier_id_selec');
        $reload_DT      = $request->input('reload_DT');

        $company_users      = CompanyUser::pluck('name','id');
        $globals_id_array   = $request->input('id');
        $count = count($globals_id_array);
        //$global             = $global->toArray();
        //dd($globals_id_array);
        return view('globalchargesAdm.duplicateArray',compact('count','company_users','globals_id_array','company_user_id_selec','carrier_id_selec','reload_DT'));
    }

    public function storeArrayAdm(Request $request){
        //dd($request->all());
        $company_user_id_selec  = $request->input('company_user_id_selec');
        $carrier_id_selec       = $request->input('carrier_id_selec');
        $reload_DT      = $request->input('reload_DT');
        $requestJob = $request->all();
        GCDplFclLcl::dispatch($requestJob,'fcl');

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully add this contract.');
        return redirect()->route('gcadm.index',compact('company_user_id_selec','carrier_id_selec','reload_DT'));
    }

    public function editDateArrAdm(Request $request){
        $company_user_id_selec  = $request->input('company_user_id_selec');
        $carrier_id_selec       = $request->input('carrier_id_selec');
        $reload_DT      = $request->input('reload_DT');

        $company_users      = CompanyUser::pluck('name','id');
        $globals_id_array   = $request->input('idAr');
        $count = count($globals_id_array);
        //$global             = $global->toArray();
        return view('globalchargesAdm.EditDatesArray',compact('count','company_users','globals_id_array','company_user_id_selec','carrier_id_selec','reload_DT'));
    }

    public function updateDateArrAdm(Request $request){
        //dd($request->all());
        $date = explode('/',$request->validation_expire);
        $date_start = trim($date[0]);
        $date_end   = trim($date[1]);
        foreach($request->idArray as $global){
            $globalObj = null;
            $globalObj = GlobalCharge::find($global);
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
        return redirect()->route('gcadm.index',compact('company_user_id_selec','carrier_id_selec','reload_DT'));
    }
}
