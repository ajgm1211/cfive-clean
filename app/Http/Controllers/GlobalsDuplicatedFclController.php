<?php

namespace App\Http\Controllers;

use App\User;
use App\Harbor;
use App\Country;
use App\Carrier;
use App\Currency;
use App\Surcharge;
use App\CompanyUser;
use App\TypeDestiny;
use App\GlobalCharge;
use GuzzleHttp\Client;
use App\GlobalCharPort;
use App\CalculationType;
use App\GlobalCharCountry;
use App\GlobalCharCarrier;
use Illuminate\Http\Request;
use App\GroupGlobalsCompanyUser;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;

class GlobalsDuplicatedFclController extends Controller
{

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    // CARGA LA VISTA DE DIPLICADOS CON SU REFERENCIA --------------------------------------------------------
    public function show($id)
    {
        $gp_cmpuser     = GroupGlobalsCompanyUser::with('alertCompany.company_user','globalcharger')->find($id);
        $globals_cmps   = DB::select('call  proc_globalcharge_id('.$gp_cmpuser->globalcharger->id.')');


        //dd($globals_cmps);
        foreach($globals_cmps as $globals_cmp){
            $data       = [];
            $origin     = null;
            $destiny    = null;
            if(empty($globals_cmp->port_orig) != true){
                $origin     = $globals_cmp->port_orig;
            } elseif(empty($globals_cmp->country_orig) != true){
                $origin     = $globals_cmp->country_orig;
            } 

            if(empty($globals_cmp->port_dest) != true){
                $destiny    = $globals_cmp->port_dest;
            } elseif(empty($globals_cmp->country_dest) != true){
                $destiny    = $globals_cmp->country_dest;
            } 

            $data = collect([
                'id'                 => $globals_cmp->id,
                'surcharge'          => $globals_cmp->surcharges,
                'origin'             => $origin,
                'destiny'            => $destiny,
                'carrier'            => $globals_cmp->carrier,
                'typedestiny'        => $globals_cmp->typedestiny,
                'calculationtype'    => $globals_cmp->calculationtype,
                'currency'           => $globals_cmp->currency,
                'company_name'       => $globals_cmp->company_user, 
                'amount'             => $globals_cmp->ammount, 
                'validity'           => $globals_cmp->validity, 
                'expire'             => $globals_cmp->expire 
            ]);
        }
        //dd($data);
        return view('alertsDuplicatedsGCFcl.duplicateds.index',compact('id','data'));
    }

    // LLENA EL DATATABBLE DE DUPLICADOS ---------------------------------------------------------------------
    public function edit($id)
    {
        $globals_dp = DB::select('call  proc_duplicado_globalcharge_fcl('.$id.')');
        //dd($globals_dp);

        return DataTables::of($globals_dp)
            ->addColumn('origin', function ($globals_dp){ 
                $origin = null;
                if(empty($globals_dp->port_orig) != true){
                    $origin     = $globals_dp->port_orig;
                } elseif(empty($globals_dp->country_orig) != true){
                    $origin     = $globals_dp->country_orig;
                } 
                if(strnatcasecmp($origin,'ALL') == 0){
                    $color = '#ff0d24';
                } else{
                    $color = '#575962';                    
                }
                return '<p style="color:'.$color.'">'.$origin.'</p>';
            })
            ->addColumn('destiny', function ($globals_dp){ 
                $destiny = null;
                if(empty($globals_dp->port_dest) != true){
                    $destiny    = $globals_dp->port_dest;
                } elseif(empty($globals_dp->country_dest) != true){
                    $destiny    = $globals_dp->country_dest;
                } 

                if(strnatcasecmp($destiny,'ALL') == 0){
                    $color = '#ff0d24';
                } else{
                    $color = '#575962';                    
                }
                return '<p style="color:'.$color.'">'.$destiny.'</p>';
            })
            ->addColumn('validitylb', function ($globals_dp){ 
                return $globals_dp->validity.' / '.$globals_dp->expire;
            })
            ->addColumn('action', function ($globals_dp) {
                return '
                    <a href="#" onclick="showModal('.$globals_dp->global_duplicated_id.')"  class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test"   title="Edit G.C. Duplicated">
                        <i style="color:#036aa0" class="la la-edit"></i>
				    </a>
                    &nbsp;&nbsp;
                    <a href="#" onclick="DestroyGroup('.$globals_dp->global_duplicated_id.')" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test"   title="Detele " ">
                        <i style="color:#036aa0" class="la la-trash"></i>
				    </a>
                    ';
            })
            //->editColumn('id', '{{$alerts->id}}')->toJson();
            ->toJson();

    }

    // CARGA EL MODAL DEL G.C SELECIONADO --------------------------------------------------------------------
    public function showAdm($id,$grupo_id){
        //dd($id,$grupo_id);
        $objsurcharge = new Surcharge();
        $countries          = Country::pluck('name','id');

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
        return view('alertsDuplicatedsGCFcl.duplicateds.edit', compact('globalcharges','harbor','carrier','currency','company_users','calculationT','typedestiny','surcharge','countries','grupo_id'));
    }

    public function update(Request $request, $id)
    {
        $harbor         = Harbor::pluck('display_name','id');
        $carrier        = Carrier::pluck('name','id');
        $currency       = Currency::pluck('alphacode','id');
        $calculationT   = CalculationType::pluck('name','id');
        $typedestiny    = TypeDestiny::pluck('description','id');
        $grupo_id       = $request->grupo_id;

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
        $request->session()->flash('message.content', 'You successfully updated this globalcharger.');
        return redirect()->route('GlobalsDuplicatedEspecific.show',$grupo_id);
    }

    public function destroy($id)
    {
        try{
            $groupsCmp  = GlobalCharge::find($id);
            $groupsCmp->delete();
            return response()->json(['data'=> 1]);
        } catch(\Exception $e){
            return response()->json(['data'=> 2]);            
        }
    }
}
