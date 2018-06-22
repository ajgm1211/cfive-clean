<?php

namespace App\Http\Controllers;

use App\Company;
use App\ContractUserRestriction;
use App\ContractCompanyRestriction;
use Illuminate\Http\Request;
use App\Contract;
use App\Contact;
use App\Country;
use App\Carrier;
use App\Harbor;
use App\Rate;
use App\Currency;
use App\CalculationType;
use App\LocalCharge;
use App\Surcharge;
use App\LocalCharCarrier;
use App\LocalCharPort;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\TypeDestiny;
class ContractsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contracts = Contract::where('user_id','=',Auth::user()->id)->with('rates')->get();
        return view('contracts/index', ['arreglo' => $contracts]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function add()
    {
        $objcountry = new Country();
        $objcarrier = new Carrier();
        $objharbor = new Harbor();
        $objcurrency = new Currency();
        $objcalculation = new CalculationType();
        $objsurcharge = new Surcharge();
        $objtypedestiny = new TypeDestiny();

        $harbor = $objharbor->all()->pluck('name','id');
        $country = $objcountry->all()->pluck('name','id');
        $carrier = $objcarrier->all()->pluck('name','id');
        $currency = $objcurrency->all()->pluck('alphacode','id');
        $calculationT = $objcalculation->all()->pluck('name','id');
        $typedestiny = $objtypedestiny->all()->pluck('description','id');
        $surcharge = $objsurcharge->where('user_id','=',Auth::user()->id)->pluck('name','id');
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

        return view('contracts.addT',compact('country','carrier','harbor','currency','calculationT','surcharge','typedestiny','companies','contacts','users'));

    }

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
        $contract = new Contract($request->all());
        $contract->user_id =Auth::user()->id;
        $validation = explode('/',$request->validation_expire);
        $contract->validity = $validation[0];
        $contract->expire = $validation[1];
        $contract->save();

        $details = $request->input('origin_id');
        $detailscharges = $request->input('type');
        $companies = $request->input('companies');
        $users = $request->input('users');
        // For Each de los rates
        $contador = 1;
        foreach($details as $key => $value)
        {
            if(!empty($request->input('twuenty.'.$key))) {
                $rates = new Rate();
                $rates->origin_port = $request->input('origin_id.'.$key);
                $rates->destiny_port = $request->input('destiny_id.'.$key);
                $rates->carrier_id = $request->input('carrier_id.'.$key);
                $rates->twuenty = $request->input('twuenty.'.$key);
                $rates->forty = $request->input('forty.'.$key);
                $rates->fortyhc = $request->input('fortyhc.'.$key);
                $rates->currency_id = $request->input('currency_id.'.$key);
                $rates->contract()->associate($contract);
                $rates->save();
            }
        }
        // For Each de los localcharge

        foreach($detailscharges as $key2 => $value)
        {
            if(!empty($request->input('ammount.'.$key2))) {
                $localcharge = new LocalCharge();
                $localcharge->surcharge_id = $request->input('type.'.$key2);
                $localcharge->typedestiny_id = $request->input('changetype.'.$key2);
                $localcharge->calculationtype_id = $request->input('calculationtype.'.$key2);
                $localcharge->ammount = $request->input('ammount.'.$key2);
                $localcharge->currency_id = $request->input('localcurrency_id.'.$key2);
                $localcharge->contract()->associate($contract);
                $localcharge->save();

                $detailportOrig = $request->input('port_origlocal'.$contador);
                $detailportDest = $request->input('port_destlocal'.$contador);

                $detailcarrier = $request->input('localcarrier_id'.$contador);
                foreach($detailcarrier as $c => $value)
                {
                    $detailcarrier = new LocalCharCarrier();
                    $detailcarrier->carrier_id =$request->input('localcarrier_id'.$contador.'.'.$c);
                    $detailcarrier->localcharge()->associate($localcharge);
                    $detailcarrier->save();
                }
                foreach($detailportOrig as $orig => $value)
                {
                    foreach($detailportDest as $dest => $value)
                    {
                        $detailport = new LocalCharPort();
                        $detailport->port_orig = $request->input('port_origlocal'.$contador.'.'.$orig);
                        $detailport->port_dest = $request->input('port_destlocal'.$contador.'.'.$dest);
                        $detailport->localcharge()->associate($localcharge);
                        $detailport->save();
                    }

                }
                $contador++;
            }
        }

        if(!empty($companies)){
            foreach($companies as $key3 => $value)
            {
                $contract_company_restriction = new ContractCompanyRestriction();
                $contract_company_restriction->company_id=$value;
                $contract_company_restriction->contract_id=$contract->id;
                $contract_company_restriction->save();
            }
        }

        if(!empty($users)){
            foreach($users as $key4 => $value)
            {
                $contract_client_restriction = new ContractUserRestriction();
                $contract_client_restriction->user_id=$value;
                $contract_client_restriction->contract_id=$contract->id;
                $contract_client_restriction->save();
            }
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully add this contract.');

        return redirect()->action('ContractsController@index');

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
        $contracts = Contract::where('id',$id)->with('rates','localcharges.localcharports','localcharges.localcharcarriers')->first();

        $objtypedestiny = new TypeDestiny();
        $objcountry = new Country();
        $objcarrier = new Carrier();
        $objharbor = new Harbor();
        $objcurrency = new Currency();
        $objcalculation = new CalculationType();
        $objsurcharge = new Surcharge();

        $harbor = $objharbor->all()->pluck('name','id');
        $country = $objcountry->all()->pluck('name','id');
        $carrier = $objcarrier->all()->pluck('name','id');
        $currency = $objcurrency->all()->pluck('alphacode','id');
        $calculationT = $objcalculation->all()->pluck('name','id');
        $typedestiny = $objtypedestiny->all()->pluck('description','id');
        $surcharge = $objsurcharge->where('user_id','=',Auth::user()->id)->pluck('name','id');
        $company_restriction = ContractCompanyRestriction::where('contract_id',$contracts->id)->first();
        $user_restriction = ContractUserRestriction::where('contract_id',$contracts->id)->first();
        if(!empty($company_restriction)){
            $company = Company::where('id',$company_restriction->company_id)->select('id')->first();
        }
        if(!empty($user_restriction)){
            $user = User::where('id',$user_restriction->user_id)->select('id')->first();
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

        return view('contracts.editT', compact('contracts','harbor','country','carrier','currency','calculationT','surcharge','typedestiny','company','companies','users','user'));
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
        $requestForm = $request->all();
        $contract = Contract::find($id);
        $validation = explode('/',$request->validation_expire);
        $contract->validity = $validation[0];
        $contract->expire = $validation[1];
        $contract->update($requestForm);

        $details = $request->input('origin_id');
        $detailscharges = $request->input('ammount');
        $companies = $request->input('companies');
        $users = $request->input('users');
        $contador = 1;
        // for each rates 
        foreach($details as $key => $value)
        {
            if(!empty($request->input('twuenty.'.$key))) {

                $rates = new Rate();
                $rates->origin_port = $request->input('origin_id.'.$key);
                $rates->destiny_port = $request->input('destiny_id.'.$key);
                $rates->carrier_id = $request->input('carrier_id.'.$key);
                $rates->twuenty = $request->input('twuenty.'.$key);
                $rates->forty = $request->input('forty.'.$key);
                $rates->fortyhc = $request->input('fortyhc.'.$key);
                $rates->currency_id = $request->input('currency_id.'.$key);
                $rates->contract()->associate($contract);
                $rates->save();

            }
        }

        // For Each de los localcharge

        foreach($detailscharges as $key2 => $value)
        {
            if(!empty($request->input('ammount.'.$key2))) {
                $localcharge = new LocalCharge();
                $localcharge->surcharge_id = $request->input('type.'.$key2);
                $localcharge->typedestiny_id  = $request->input('changetype.'.$key2);
                $localcharge->calculationtype_id = $request->input('calculationtype.'.$key2);
                $localcharge->ammount = $request->input('ammount.'.$key2);
                $localcharge->currency_id = $request->input('localcurrency_id.'.$key2);
                $localcharge->contract()->associate($contract);
                $localcharge->save();
                $detailportOrig = $request->input('port_origlocal'.$contador);
                $detailportDest = $request->input('port_destlocal'.$contador);
                $detailcarrier = $request->input('localcarrier_id'.$contador);
                $companies = $request->input('companies');
                foreach($detailcarrier as $c => $value)
                {
                    $detailcarrier = new LocalCharCarrier();
                    $detailcarrier->carrier_id =$request->input('localcarrier_id'.$contador.'.'.$c);
                    $detailcarrier->localcharge()->associate($localcharge);
                    $detailcarrier->save();
                }
                foreach($detailportOrig as $orig => $value)
                {
                    foreach($detailportDest as $dest => $value)
                    {


                        $detailport = new LocalCharPort();
                        $detailport->port_orig = $request->input('port_origlocal'.$contador.'.'.$orig);
                        $detailport->port_dest = $request->input('port_destlocal'.$contador.'.'.$dest);
                        $detailport->localcharge()->associate($localcharge);
                        $detailport->save();
                    }

                }
                $contador++;
            }
        }
        
        if(!empty($companies)){
            ContractCompanyRestriction::where('contract_id',$contract->id)->delete();
            
            foreach($companies as $key3 => $value)
            {
                $contract_company_restriction = new ContractCompanyRestriction();
                $contract_company_restriction->company_id=$value;
                $contract_company_restriction->contract_id=$contract->id;
                $contract_company_restriction->save();
            }
        }

        if(!empty($users)){
            ContractUserRestriction::where('contract_id',$contract->id)->delete();
            
            foreach($users as $key4 => $value)
            {
                $contract_client_restriction = new ContractUserRestriction();
                $contract_client_restriction->user_id=$value;
                $contract_client_restriction->contract_id=$contract->id;
                $contract_client_restriction->save();
            }
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully update this contract.');

        return redirect()->action('ContractsController@index');

    }

    public function updateRates(Request $request, $id)
    {
        $requestForm = $request->all();

        $rate = Rate::find($id);
        $rate->update($requestForm);
    }

    public function updateLocalChar(Request $request, $id)
    {
        $localC = LocalCharge::find($id);
        $localC->surcharge_id = $request->input('surcharge_id');
        $localC->typedestiny_id  = $request->input('changetype');
        $localC->calculationtype_id = $request->input('calculationtype_id');
        $localC->ammount = $request->input('ammount');
        $localC->currency_id = $request->input('currency_id');
        $localC->update();

        $detailportOrig = $request->input('port_origlocal');
        $detailportDest = $request->input('port_destlocal');
        $carrier = $request->input('carrier_id');
        $deleteCarrier = LocalCharCarrier::where("localcharge_id",$id);
        $deleteCarrier->delete();
        $deletePort = LocalCharPort::where("localcharge_id",$id);
        $deletePort->delete();

        foreach($detailportOrig as $orig => $valueOrig)
        {
            foreach($detailportDest as $dest => $valueDest)
            {
                $detailport = new LocalCharPort();
                $detailport->port_orig = $valueOrig;
                $detailport->port_dest = $valueDest;
                $detailport->localcharge_id = $id;
                $detailport->save();
            }

        }
        foreach($carrier as $key)
        {
            $detailcarrier = new LocalCharCarrier();
            $detailcarrier->carrier_id = $key;
            $detailcarrier->localcharge_id = $id;
            $detailcarrier->save();
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rate = Rate::find($id);
        $rate->delete();
        return $rate;
    }

    public function destroyLocalCharges($id)
    {
        $local = LocalCharge::find($id);
        $local->delete();
    }

    public function destroyRates(Request $request,$id)
    {
        $rates = self::destroy($id);
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully delete the rate ');
        return redirect()->action('ContractsController@index');
    }

    public function destroymsg($id)
    {
        return view('contracts/message' ,['rate_id' => $id]);
    }
}
