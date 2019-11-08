<?php

namespace App\Http\Controllers;

use Excel;
use App\User;
use App\Harbor;
use App\Company;
use App\RateLcl;
use App\Contact;
use App\Country;
use App\Carrier;
use App\Currency;
use App\Surcharge;
use EventIntercom;
use App\Direction;
use App\ContractLcl;
use App\CompanyUser;
use App\TypeDestiny;
use App\ScheduleType;
use App\ViewRatesLcl;
use App\LocalChargeLcl;
use App\Jobs\GeneralJob;
use App\LocalCharPortLcl;
use App\ViewLocalCharges;
use App\ContractCarrierLcl;
use App\CalculationTypeLcl;
use App\LocalCharCarrierLcl;
use Illuminate\Http\Request;
use App\LocalCharCountryLcl;
use App\ViewContractLclRates;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\ContractLclUserRestriction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\ContractLclCompanyRestriction;
use Illuminate\Support\Collection as Collection;

class ContractsLclController extends Controller
{

    public function index()
    {
        //$arreglo = ContractLcl::where('company_user_id','=',Auth::user()->company_user_id)->get();
        //$contractG = ContractLcl::where('company_user_id','=',Auth::user()->company_user_id)->get();
        //return view('contractsLcl/index', compact('arreglo','contractG'));
        $model      = new  RateLcl();
        $mrates     = $model->hydrate(
            DB::select(
                'call select_rates_contract_lcl('.\Auth::user()->company_user_id.')'
            )
        );


        $originsR = DB::table('views_contractlcl_rates')
            ->select('port_orig')
            ->groupBy('port_orig')
            ->where('company_user_id', Auth::user()->company_user_id)
            ->get();

        $destinationsR = DB::table('views_contractlcl_rates')
            ->select('port_dest')
            ->groupBy('port_dest')
            ->where('company_user_id', Auth::user()->company_user_id)
            ->get();

        $carriersR = DB::table('views_contractlcl_rates')
            ->select('carrier')
            ->groupBy('carrier')
            ->where('company_user_id', Auth::user()->company_user_id)
            ->get();

        $statussR = DB::table('views_contractlcl_rates')
            ->select('status')
            ->groupBy('status')
            ->where('company_user_id', Auth::user()->company_user_id)
            ->get();


        $originsAr      = [ 'null' => 'Select option'];
        $destinationAr  = [ 'null' => 'Select option'];
        $carrierAr      = [ 'null' => 'Select option'];
        $statusAr       = [ 'null' => 'Select option'];

        foreach($carriersR as $carrierR){
            $carrierAr[$carrierR->carrier] = $carrierR->carrier;
        }

        foreach($originsR as $originR){
            $originsAr[$originR->port_orig] = $originR->port_orig;
        }

        foreach($destinationsR as $destinationR){
            $destinationAr[$destinationR->port_dest] = $destinationR->port_dest;
        }

        foreach($statussR as $statusR){
            $statusAr[$statusR->status] = $statusR->status;
        }
        $values = [
            'carrier'       => $carrierAr,
            'origin'        => $originsAr,
            'destination'   => $destinationAr,
            'status'        => $statusAr
        ];

        return view('contractsLcl/index',compact('values'));
    }

    public function add()
    {

        $harbor = Harbor::all()->pluck('display_name','id');
        $country = Country::all()->pluck('name','id');
        $carrier = Carrier::all()->pluck('name','id');
        $direction      = [null=>'Please Select'];
        $direction2      = Direction::all();
        foreach($direction2 as $d){
            $direction[$d['id']]=$d->name;
        }
        $currency = Currency::all()->pluck('alphacode','id');
        $calculationT = CalculationTypeLcl::all()->pluck('name','id');
        $typedestiny = TypeDestiny::all()->pluck('description','id');
        $surcharge = Surcharge::where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
        $companies = Company::where('company_user_id', '=', \Auth::user()->company_user_id)->pluck('business_name','id');
        $company_user=CompanyUser::find(\Auth::user()->company_user_id);
        $currency_cfg = Currency::find($company_user->currency_id);
        $scheduleT   = ['null'=>'Please Select'];
        $scheduleTo  = ScheduleType::all();
        foreach($scheduleTo as $d){
            $scheduleT[$d['id']]=$d->name;
        }        
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

        return view('contractsLcl.add',compact('country','carrier','harbor','direction','currency','calculationT','surcharge','typedestiny','companies','contacts','users','currency_cfg','scheduleT'));

    }

    public function create()
    {
        //
    }

    public function deleteContract($id){

        $contract = ContractLcl::find($id);
        if(isset($contract->rates)){
            if(isset($contract->localcharges)){
                return response()->json(['message' => count($contract->rates),'local' => count($contract->localcharges) ]);
            }else{
                return response()->json(['message' => count($contract->rates),'local' => 0]);
            }
        }
        return response()->json(['message' => 'SN','local' => 0]);
    }
    public function destroyContract($id){

        try {

            /*  $FileTmp = FileTmp::where('contract_id',$id)->first();
            if(count($FileTmp) > 0){
              Storage::Delete($FileTmp->name_file);
              $FileTmp->delete();
            }*/

            $contract = ContractLcl::find($id);
            $contract->delete();

            return response()->json(['message' => 'Ok']);
        }
        catch (\Exception $e) {
            return response()->json(['message' => $e]);
        }


    }

    public function store(Request $request)
    {
        $contract = new ContractLcl($request->all());
        $contract->company_user_id  = Auth::user()->company_user_id;
        $validation                 = explode('/',$request->validation_expire);
        $contract->validity         = $validation[0];
        $contract->expire           = $validation[1];
        $contract->direction_id     = $request->direction;
        $contract->comments         = $request->input('comments');
        $contract->save();

        foreach($request->carrierAr as $carrierFA){
            ContractCarrierLcl::create([
                'carrier_id'    => $carrierFA,
                'contract_id'   => $contract->id
            ]);
        }

        $details        = $request->input('currency_id');
        $detailscharges = $request->input('localcurrency_id');
        $companies      = $request->input('companies');
        $users          = $request->input('users');


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
                    $sch = null;
                    if($request->input('scheduleT.'.$key) != 'null'){
                        $sch = $request->input('scheduleT.'.$key);
                    }
                    $rates = new RateLcl();
                    $rates->origin_port         = $request->input('origin_id'.$contadorRate.'.'.$Rorig);
                    $rates->destiny_port        = $request->input('destiny_id'.$contadorRate.'.'.$Rdest);
                    $rates->carrier_id          = $request->input('carrier_id.'.$key);
                    $rates->uom                 = $request->input('uom.'.$key);
                    $rates->minimum             = $request->input('minimum.'.$key);
                    $rates->currency_id         = $request->input('currency_id.'.$key);
                    $rates->schedule_type_id    = $sch;
                    $rates->transit_time        = $request->input('transitTi.'.$key);
                    $rates->via                 = $request->input('via.'.$key);
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
                        $localcharge->minimum = $request->input('minimumL.'.$key2);
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

        if(!empty($companies)){

            foreach($companies as $key3 => $value)
            {
                $contract_company_restriction = new ContractLclCompanyRestriction();
                $contract_company_restriction->company_id=$value;
                $contract_company_restriction->contractlcl_id=$contract->id;
                $contract_company_restriction->save();
            }
        }

        if(!empty($users)){
            foreach($users as $key4 => $value)
            {
                $contract_client_restriction = new ContractLclUserRestriction();
                $contract_client_restriction->user_id=$value;
                $contract_company_restriction->contractlcl_id=$contract->id;
                $contract_client_restriction->save();
            }
        }
        // EVENTO INTERCOM
        $event = new  EventIntercom();
        $event->event_contractLcl();
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully add this contract.');

        return redirect()->action('ContractsLclController@index');

    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request,$id)
    {
        $id = obtenerRouteKey($id);
        $contracts      = ContractLcl::where('id',$id)->first();
        $harbor         = Harbor::all()->pluck('display_name','id');
        $country        = Country::all()->pluck('name','id');
        $carrier        = Carrier::all()->pluck('name','id');
        $direction      = Direction::pluck('name','id');
        $currency       = Currency::all()->pluck('alphacode','id');
        $calculationT   = CalculationTypeLcl::all()->pluck('name','id');
        $typedestiny    = TypeDestiny::all()->pluck('description','id');
        $surcharge      = Surcharge::where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');

        $company_restriction    = ContractLclCompanyRestriction::where('contractlcl_id',$contracts->id)->get();
        $user_restriction       = ContractLclUserRestriction::where('contractlcl_id',$contracts->id)->get();
        $company_user           =CompanyUser::find(\Auth::user()->company_user_id);
        $currency_cfg           = Currency::find($company_user->currency_id);
        if(!empty($company_restriction)){
            $company = Company::wherein('id',$company_restriction->pluck('company_id'))->get();
            $company = $company->pluck('id');
        }
        if(!empty($user_restriction)){
            $user = User::wherein('id',$user_restriction->pluck('user_id'))->get();
            $user = $user->pluck('id');
        }
        $companies = Company::where('company_user_id', '=', \Auth::user()->company_user_id)->pluck('business_name','id');
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
        //dd($contracts);
        if (!$request->session()->exists('activeSLcl')) {
            $request->session()->flash('activeRLcl', 'active');
        }

        return view('contractsLcl.edit', compact('contracts','harbor','country','carrier','currency','direction','calculationT','surcharge','typedestiny','company','companies','users','user','id','currency_cfg'));
    }

    public function update(Request $request, $id)
    {
        $requestForm            = $request->all();
        $contract               = ContractLcl::find($id);
        $validation             = explode('/',$request->validation_expire);
        $contract->validity     = $validation[0];
        $contract->expire       = $validation[1];
        $contract->direction_id = $request->direction;
        $contract->comments     = $request->input('comments');
        $contract->update($requestForm);

        ContractCarrierLcl::where('contract_id',$id)->delete();
        foreach($request->carrierAr as $carrierFA){
            ContractCarrierLcl::create([
                'carrier_id'    => $carrierFA,
                'contract_id'   => $id
            ]);
        }

        $companies = $request->input('companies');
        $users = $request->input('users');
        ContractLclCompanyRestriction::where('contractlcl_id',$contract->id)->delete();
        if(!empty($companies)){


            foreach($companies as $key3 => $value)
            {
                $contract_company_restriction = new ContractLclCompanyRestriction();
                $contract_company_restriction->company_id=$value;
                $contract_company_restriction->contractlcl_id=$contract->id;
                $contract_company_restriction->save();
            }
        }
        ContractLclUserRestriction::where('contractlcl_id',$contract->id)->delete();
        if(!empty($users)){

            foreach($users as $key4 => $value)
            {
                $contract_client_restriction = new ContractLclUserRestriction();
                $contract_client_restriction->user_id=$value;
                $contract_client_restriction->contractlcl_id=$contract->id;
                $contract_client_restriction->save();
            }

        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully update this contract.');
        return redirect()->action('ContractsLclController@index');
    }

    public function destroy($id)
    {
        //
    }

    //RATES
    public function addRates($id){
        $objcarrier = new Carrier();
        $objharbor = new Harbor();
        $objcurrency = new Currency();
        $harbor = $objharbor->all()->pluck('display_name','id');
        $carrier = $objcarrier->all()->pluck('name','id');
        $currency = $objcurrency->all()->pluck('alphacode','id');
        $scheduleT   = [null=>'Please Select'];
        $scheduleTo  = ScheduleType::all();
        foreach($scheduleTo as $d){
            $scheduleT[$d['id']]=$d->name;
        } 
        return view('contractsLcl.addRates', compact('harbor','carrier','currency','id','scheduleT'));
    }
    public function editRates($id){
        $objcarrier = new Carrier();
        $objharbor = new Harbor();
        $objcurrency = new Currency();
        $schedulesT   = [null=>'Please Select'];
        $scheduleTo  = ScheduleType::all();
        foreach($scheduleTo as $d){
            $schedulesT[$d['id']]=$d->name;
        }
        $harbor = $objharbor->all()->pluck('display_name','id');
        $carrier = $objcarrier->all()->pluck('name','id');
        $currency = $objcurrency->all()->pluck('alphacode','id');

        $rates = RateLcl::find($id);
        return view('contractsLcl.editRates', compact('rates','harbor','carrier','currency','schedulesT'));
    }

    public function storeRates(Request $request,$id){

        $rateOrig = $request->input('origin_port');
        $rateDest = $request->input('destiny_port');

        foreach($rateOrig as $Rorig => $Origvalue)
        {
            foreach($rateDest as $Rdest => $Destvalue)
            {

                $rates = new RateLcl();
                $rates->origin_port     =$Origvalue;
                $rates->destiny_port    =$Destvalue;
                $rates->carrier_id      = $request->input('carrier_id');
                $rates->uom             = $request->input('uom');
                $rates->minimum         = $request->input('minimum');
                $rates->currency_id     = $request->input('currency_id');
                $rates->schedule_type_id = $request->input('schedule_type_id');
                $rates->transit_time    = $request->input('transit_time');
                $rates->via             = $request->input('via');
                $rates->contractlcl_id  = $id;
                $rates->save();
            }
        }
        return redirect()->back()->with('ratesSave','true');
    }

    public function updateRates(Request $request, $id){
        $requestForm = $request->all();
        $rate = RateLcl::find($id);
        $rate->update($requestForm);
        return redirect()->back()->with('editRateLcl','true');
    }
    public function deleteRates(Request $request,$id)
    {

        $rate = RateLcl::find($id);
        $rate->forceDelete();
        return $rate;

    }

    public function duplicateRates($id){
        $objcarrier = new Carrier();
        $objharbor = new Harbor();
        $objcurrency = new Currency();
        $harbor = $objharbor->all()->pluck('display_name','id');
        $carrier = $objcarrier->all()->pluck('name','id');
        $currency = $objcurrency->all()->pluck('alphacode','id');
        $rates = RateLcl::find($id);
        return view('contractsLcl.duplicateRates', compact('rates','harbor','carrier','currency'));
    }

    //LOCALCHARGES
    public function addLocalChar($id){

        $countries = Country::pluck('name','id');
        $harbor = Harbor::all()->pluck('display_name','id');
        $carrier = Carrier::all()->pluck('name','id');
        $currency = Currency::all()->pluck('alphacode','id');
        $calculationT = CalculationTypeLcl::all()->pluck('name','id');
        $typedestiny = TypeDestiny::all()->pluck('description','id');
        $surcharge = Surcharge::where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
        $company_user=CompanyUser::find(\Auth::user()->company_user_id);
        $currency_cfg = Currency::find($company_user->currency_id);
        return view('contractsLcl.addLocalCharge', compact('harbor','carrier','currency','calculationT','typedestiny','surcharge','id','countries','currency_cfg'));

    }
    public function storeLocalChar(Request $request,$id){

        $calculation_type  = $request->input('calculationtype_id');
        foreach($calculation_type as $ct => $ctype)
        {
            $localcharge = new LocalChargeLcl();
            $request->request->add(['contractlcl_id' => $id,'calculationtypelcl_id'=>$ctype]);
            $localcharge =  $localcharge->create($request->all());
            $detailcarrier = $request->input('carrier_id');

            foreach($detailcarrier as $c => $value)
            {
                $detailcarrier = new LocalCharCarrierLcl();
                $detailcarrier->carrier_id =$value;
                $detailcarrier->localchargelcl()->associate($localcharge);
                $detailcarrier->save();
            }
            $typeroute =  $request->input('typeroute');
            if($typeroute == 'port'){
                $detailportOrig = $request->input('port_origlocal');
                $detailportDest = $request->input('port_destlocal');
                foreach($detailportOrig as $orig => $valueOrig){
                    foreach($detailportDest as $dest => $valueDest){
                        $detailport = new LocalCharPortLcl();
                        $detailport->port_orig =$valueOrig;
                        $detailport->port_dest =$valueDest;
                        $detailport->localchargelcl()->associate($localcharge);
                        $detailport->save();
                    }
                }
            }elseif($typeroute == 'country'){

                $detailcountryOrig = $request->input('country_orig');
                $detailcountryDest = $request->input('country_dest');

                foreach($detailcountryOrig as $orig => $valueOrigC){
                    foreach($detailcountryDest as $dest => $valueDestC){
                        $detailcountry = new LocalCharCountryLcl();
                        $detailcountry->country_orig =$valueOrigC;
                        $detailcountry->country_dest = $valueDestC;
                        $detailcountry->localchargelcl()->associate($localcharge);
                        $detailcountry->save();

                    }
                }
            }
        }
        return redirect()->back()->with('localcharSaveLcl','true')->with('activeSLcl','active');
    }
    public function editLocalChar($id){

        $countries = Country::pluck('name','id');
        $calculationT = CalculationTypeLcl::all()->pluck('name','id');
        $typedestiny = TypeDestiny::all()->pluck('description','id');
        $surcharge = Surcharge::where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
        $harbor = Harbor::all()->pluck('display_name','id');
        $carrier = Carrier::all()->pluck('name','id');
        $currency = Currency::all()->pluck('alphacode','id');
        $localcharges = LocalChargeLcl::find($id);
        return view('contractsLcl.editLocalCharge', compact('localcharges','harbor','carrier','currency','calculationT','typedestiny','surcharge','countries'));
    }
    public function updateLocalChar(Request $request, $id){
        $localC = LocalChargeLcl::find($id);
        $localC->surcharge_id = $request->input('surcharge_id');
        $localC->typedestiny_id  = $request->input('changetype');
        $localC->calculationtypelcl_id = $request->input('calculationtype_id');
        $localC->ammount = $request->input('ammount');
        $localC->minimum = $request->input('minimum');
        $localC->currency_id = $request->input('currency_id');
        $localC->update();


        $carrier = $request->input('carrier_id');
        $deleteCarrier = LocalCharCarrierLcl::where("localchargelcl_id",$id);
        $deleteCarrier->delete();
        $deletePort = LocalCharPortLcl::where("localchargelcl_id",$id);
        $deletePort->delete();
        $deleteCountry = LocalCharCountryLcl::where("localchargelcl_id",$id);
        $deleteCountry->delete();
        $typerate =  $request->input('typeroute');
        if($typerate == 'port'){
            $detailportOrig = $request->input('port_origlocal');
            $detailportDest = $request->input('port_destlocal');
            foreach($detailportOrig as $orig => $valueOrig)
            {
                foreach($detailportDest as $dest => $valueDest)
                {
                    $detailport = new LocalCharPortLcl();
                    $detailport->port_orig = $valueOrig;
                    $detailport->port_dest = $valueDest;
                    $detailport->localchargelcl_id = $id;
                    $detailport->save();
                }
            }
        }elseif($typerate == 'country'){
            $detailCountrytOrig =$request->input('country_orig');
            $detailCountryDest = $request->input('country_dest');
            foreach($detailCountrytOrig as $orig => $valueOrigC)
            {
                foreach($detailCountryDest as $dest => $valueDestC)
                {
                    $detailcountry = new LocalCharCountryLcl();
                    $detailcountry->country_orig = $valueOrigC;
                    $detailcountry->country_dest =  $valueDestC;
                    $detailcountry->localchargelcl_id = $id;
                    $detailcountry->save();
                }
            }
        }

        foreach($carrier as $key)
        {
            $detailcarrier = new LocalCharCarrierLcl();
            $detailcarrier->carrier_id = $key;
            $detailcarrier->localchargelcl_id = $id;
            $detailcarrier->save();
        }
        return redirect()->back()->with('localcharLcl','true')->with('activeSLcl','active');
    }

    public function deleteLocalCharges($id)
    {
        $local = LocalChargeLcl::find($id);
        $local->forceDelete();
    }

    public function duplicateLocalCharges($id){

        $countries = Country::pluck('name','id');
        $calculationT = CalculationTypeLcl::all()->pluck('name','id');
        $typedestiny = TypeDestiny::all()->pluck('description','id');
        $surcharge = Surcharge::where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
        $harbor = Harbor::all()->pluck('display_name','id');
        $carrier = Carrier::all()->pluck('name','id');
        $currency = Currency::all()->pluck('alphacode','id');
        $localcharges = LocalChargeLcl::find($id);
        return view('contractsLcl.duplicateLocalCharge', compact('localcharges','harbor','carrier','currency','calculationT','typedestiny','surcharge','countries'));
    }

    // DATATABLES
    public function contractLclRates(Request $request){
        $contractRate = new  ViewContractLclRates();
        $data = $contractRate->select('id','contract_id','name','number','validy','expire','status','port_orig','port_dest','carrier','uom','minimum','currency','schedule_type','transit_time','via')->where('company_user_id', Auth::user()->company_user_id)->status($request->status)->carrier($request->carrier)->destPort($request->destination)->origPort($request->origin);

        return \DataTables::of($data)

            ->addColumn('schedule_type', function ($data) {
                if(empty($data['schedule_type']) != true){
                    return $data['schedule_type'];
                } else {
                    return '-------------';
                }
            })
            ->addColumn('transit_time', function ($data) {
                if(empty($data['transit_time']) != true){
                    return $data['transit_time'];
                } else {
                    return '-----';
                }
            })
            ->addColumn('via', function ($data) {
                if(empty($data['via']) != true){
                    return $data['via'];
                } else {
                    return '-----';
                }
            })
            ->addColumn('validity', function ($data) {
                return $data['validy'] ." / ".$data['expire'];
            })
            ->addColumn('options', function ($data) {
                return "<a href='contractslcl/".setearRouteKey($data['contract_id'])."/edit' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill'  title='Edit '>
                      <i class='la la-edit'></i>
                    </a>

                    <a href='#' id='delete-rate-lcl' data-ratelcl-id='$data[id]' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' title='Delete' >
                    <i  class='la la-times-circle'></i>
                    </a>




        ";
            }) ->setRowId('id')->rawColumns(['options'])->make(true);
    }
    public function dataRatesLcl($id){

        $rate = new  ViewRatesLcl();
        $data = $rate->select('id','port_orig','port_dest','carrier','uom','minimum','currency','schedule_type','transit_time','via')->where('contract_id',$id);

        return \DataTables::of($data)
            ->addColumn('schedule_type', function ($data) {
                if(empty($data['schedule_type']) != true){
                    return $data['schedule_type'];
                } else {
                    return '-------------';
                }
            })
            ->addColumn('transit_time', function ($data) {
                if(empty($data['transit_time']) != true){
                    return $data['transit_time'];
                } else {
                    return '-----';
                }
            })
            ->addColumn('via', function ($data) {
                if(empty($data['via']) != true){
                    return $data['via'];
                } else {
                    return '-----';
                }
            })
            ->addColumn('options', function ($data) {
                return " <a   class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test' title='Edit'  onclick='AbrirModal(\"editRate\",$data[id])'>
          <i class='la la-edit'></i>
          </a>
             <a id='delete-rate-lcl' data-ratelcl-id='$data[id]' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' title='Delete' >
                    <i  class='la la-times-circle'></i>
                    </a>
             <a  class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill'  title='duplicate '  onclick='AbrirModal(\"duplicateRate\",$data[id])'>
                      <i class='la la-plus'></i>
             </a>
        ";
            }) ->setRowId('id')->rawColumns(['options'])->make(true);

    }
    public function dataLcl($id){

        $data1 = \DB::select(\DB::raw('call proc_localchar_lcl('.$id.')'));
        $data = new Collection;
        for ($i = 0; $i < count($data1); $i++) {
            $data->push([
                'id' => $data1[$i]->id,
                'surcharge' =>  $data1[$i]->surcharge,
                'port_orig' =>   $data1[$i]->port_orig,
                'port_dest' =>   $data1[$i]->port_dest,
                'country_orig' =>  $data1[$i]->country_orig,
                'country_dest' =>   $data1[$i]->country_dest,
                'changetype' =>  $data1[$i]->changetype,
                'carrier' =>   $data1[$i]->carrier,
                'calculation_type' => $data1[$i]->calculation_type,
                'ammount' =>   $data1[$i]->ammount,
                'minimum' =>   $data1[$i]->minimum,
                'currency' =>   $data1[$i]->currency,

            ]);
        }
        return \DataTables::of($data)
            ->addColumn('origin', function ($data) {
                if($data['country_orig'] != null){
                    return $data['country_orig'];
                }else{
                    return $data['port_orig'];
                }

            })
            ->addColumn('destiny', function ($data) {
                if($data['country_dest'] != null){
                    return $data['country_dest'];
                }else{
                    return $data['port_dest'];
                }
            })
            ->addColumn('options', function ($data) {
                return " <a   class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test'  title='Edit '  onclick='AbrirModal(\"editLocalCharge\",$data[id])'>
          <i class='la la-edit'></i>
          </a>
            <a  data-locallcl-id='$data[id]'    class='delete-local-lcl  m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill'  title='delete' >
          <i id='rm_l' class='la la-times-circle'></i></a>
          <a   class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test'  title='duplicate '  onclick='AbrirModal(\"duplicateLocalCharge\",$data[id])'>
          <i class='la la-plus'></i>
          </a>
        ";
            }) ->setRowId('id')->rawColumns(['options'])->make(true);
    }// local charges en edit
    public function contractlclTable(){

        $contractG = ContractLcl::where('company_user_id','=',Auth::user()->company_user_id)
            ->with('carriers','direction')
            ->get();

        return \DataTables::collection($contractG)
            ->addColumn('carrier',function(ContractLcl $contractG){
                if(count($contractG->carriers->pluck('carrier')->pluck('name')) != 0){
                    return str_replace(['[',']','"'],' ',$contractG->carriers->pluck('carrier')->pluck('name'));
                }
                return '-----------------';
            })
            ->addColumn('direction', function (ContractLcl $contractG) {
                if(count($contractG->direction) != 0){
                    return $contractG->direction->name;
                } else {
                    return '-----------------';
                }
            }) 
            ->addColumn('options', function (ContractLcl $contractG) {
                return "      <a href='contractslcl/".setearRouteKey($contractG->id)."/edit' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill'  title='Edit '>
                      <i class='la la-edit'></i>
                    </a>
                    <a href='#' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' onclick='AbrirModal(\"DuplicatedContract\",".$contractG->id.")'  title='Duplicate '>
                      <i class='la la-copy'></i>
                    </a>
                    <a  id='delete-contract-lcl' data-contractlcl-id='$contractG->id' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill'  title='Delete'>
                      <i class='la la-eraser'></i>
                    </a>";
            }) ->setRowId('id')->rawColumns(['options'])->make(true);

    }

    public function duplicatedContractShow($id){
        $carrier    = Carrier::pluck('name','id');
        $directions = Direction::pluck('name','id');
        $contract   = ContractLcl::find($id);
        $contract->load('carriers');
        //dd($contract);
        return view('contractsLcl.Body-Modals.Duplicatedscontracts',compact('contract','carrier','directions'));
    }

    public function duplicatedContractStore(Request $request, $id){
        $requestArray   = $request->all();
        $data           = ['id'=> $id,'data' => $requestArray];
        GeneralJob::dispatch('duplicated_lcl',$data);

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'The contract is duplicating, please do not delete it');
        return redirect()->route('contractslcl.index');
    }

}
