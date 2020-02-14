<?php

namespace App\Http\Controllers;

use App\Harbor;
use App\Carrier;
use App\Country;
use App\Currency;
use App\Surcharge;
use App\CompanyUser;
use App\TypeDestiny;
use App\CalculationType;
use App\GlobalChargeApi;
use App\ApiProvider;
use App\GlobalChargeProvider;
use App\GlobalChargePortApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Collection as Collection;

class GlobalChargesApiController extends Controller
{
	public function index()
    {
        $globalcharges = GlobalChargeApi::with(['surcharge', 'currency', 'calculationtype', 'globalcharport.portOrig', 'globalcharport.portDest', 'typedestiny', 'globalchargeprovider.provider'])->get();

        return view('globalchargesapi.index', compact('globalcharges'));
    }

    public function create()
    {
    	$company_user = Auth::user()->companyUser;

        $harbor = Harbor::pluck('display_name','id');
        $currency = Currency::pluck('alphacode','id');
        $calculationT = CalculationType::pluck('name','id');
        $typedestiny = TypeDestiny::pluck('description','id');
        $surcharge = Surcharge::where('company_user_id','=', $company_user->id)->pluck('name','id');
        $countries = Country::pluck('name','id');
        $currency_cfg = $company_user->currency;
        $providers = ApiProvider::pluck('name','id');

        $route = 'globalchargesapi.store';

        $data = [
        	'harbor',
        	'currency',
        	'calculationT',
        	'typedestiny',
        	'surcharge',
        	'countries',
        	'currency_cfg', 
        	'providers',
        	'route' 
        ];

        return view('globalcharges.add', compact($data));
    }

    public function store(Request $request){

        $detailscharges = $request->input('type');
        $calculation_type = $request->input('calculationtype');

        foreach($calculation_type as $ct => $ctype)
        {

            $global = new GlobalChargeApi();
            $validation = explode('/',$request->validation_expire);
            $global->validity = $validation[0];
            $global->expire = $validation[1];
            $global->surcharge_id = $request->input('type');
            $global->typedestiny_id = $request->input('changetype');
            $global->calculationtype_id = $ctype;
            $global->amount = $request->input('ammount');
            $global->currency_id = $request->input('localcurrency_id');
            $global->save();

            $providers = $request->input('providers');

            foreach($providers as $p => $value)
            {
                $provider = new GlobalChargeProvider();
                $provider->provider_id = $value;
                $provider->globalcharge()->associate($global);
                $provider->save();
            }

            $detailport = $request->input('port_orig');
            $detailportDest = $request->input('port_dest');

            foreach($detailport as $p => $value)
            {
                foreach($detailportDest as $dest => $valuedest)
                {
                    $ports = new GlobalChargePortApi();
                    $ports->port_orig = $value;
                    $ports->port_dest = $valuedest;
                    $ports->typedestiny_id = $request->input('changetype');
                    $ports->globalcharge()->associate($global);
                    $ports->save();
                }
            }
        }
		
		Session::flash('globalcharge.msg', 'Global Charge Api Created'); 
        return redirect()->action('GlobalChargesApiController@index');
    }

    public function edit(GlobalChargeApi $globalchargesapi)
    {

    	$globalcharges = $globalchargesapi;
        $company_user = Auth::user()->companyUser;

        $countries = Country::pluck('name','id');
        $calculationT = CalculationType::pluck('name','id');
        $typedestiny = TypeDestiny::pluck('description','id');
        $surcharge = Surcharge::where('company_user_id','=', $company_user->id)->pluck('name','id');
		$harbor = Harbor::pluck('display_name','id');
        $currency = Currency::pluck('alphacode','id');
        $providers = ApiProvider::pluck('name','id');
        $route = 'globalchargesapi.update';
        $validation_expire = $globalcharges->validity ." / ". $globalcharges->expire ;
        $globalcharges->setAttribute('validation_expire', $validation_expire);
        $amount = $globalcharges->amount;

        $activacion = [
        	"rdrouteP" => true, 
        	"rdrouteC" => false,
        	"rdroutePC" => false,
        	"rdrouteCP" => false, 
        	'act' => 'divport'
        ];

        $data = [
        	'globalcharges',
        	'harbor',
        	'currency',
        	'calculationT',
        	'typedestiny',
        	'surcharge',
        	'countries', 
        	'providers',
        	'route',
        	'activacion',
        	'amount'
        ];

        return view('globalcharges.edit', compact($data));
    }

    public function update(Request $request, GlobalChargeApi $globalchargesapi)
    {
        $harbor = Harbor::pluck('display_name','id');
        $currency = Currency::pluck('alphacode','id');
        $calculationT = CalculationType::pluck('name','id');
        $typedestiny = TypeDestiny::pluck('description','id');

        $globalchargesapi = $globalchargesapi;

        $validation = explode('/',$request->validation_expire);
        $globalchargesapi->validity = $validation[0];
        $globalchargesapi->expire = $validation[1];
        $globalchargesapi->surcharge_id = $request->input('surcharge_id');
        $globalchargesapi->typedestiny_id = $request->input('changetype');
        $globalchargesapi->calculationtype_id = $request->input('calculationtype_id');
        $globalchargesapi->amount = $request->input('ammount');
        $globalchargesapi->currency_id = $request->input('currency_id');

        $providers = $request->input('providers');
        GlobalChargeProvider::where("globalcharge_id", $globalchargesapi->id)->delete();
        GlobalChargePortApi::where("globalcharge_id", $globalchargesapi->id)->delete();

        $port_orig = $request->input('port_orig');
        $port_dest = $request->input('port_dest');

        foreach($port_orig as  $orig => $valueorig)
        {
            foreach($port_dest as $dest => $valuedest)
            {
                $detailport = new GlobalChargePortApi();
                $detailport->port_orig = $valueorig;
                $detailport->port_dest = $valuedest;
                $detailport->typedestiny_id = $request->input('changetype');
                $detailport->globalcharge_id = $globalchargesapi->id;
                $detailport->save();
            }
        }

        foreach($providers as $key)
        {
            $detailcarrier = new GlobalChargeProvider();
            $detailcarrier->provider_id = $key;
            $detailcarrier->globalcharge_id = $globalchargesapi->id;
            $detailcarrier->save();
        }

        $globalchargesapi->update();

        Session::flash('globalcharge.msg', 'Global Charge Api Updated'); 
        return redirect()->back();
    }

    public function destroyArr(Request $request)
    {
        $globals_id_array = $request->input('id');
        $global = GlobalChargeApi::whereIn('id', $globals_id_array);
        
        if($global->delete()){
        	Session::flash('globalcharge.msg', 'Global Charges Api Deleted'); 
            return response()->json(['success' => 'Deleted successfully']);
        }
        	

        return response()->json(['error' => 'An internal error occurred!'], 404);
    }
}
