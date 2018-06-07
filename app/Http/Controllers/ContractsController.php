<?php

namespace App\Http\Controllers;

use App\Company;
use App\ContractClientRestriction;
use App\ContractCompanyRestriction;
use Illuminate\Http\Request;
use App\Contract;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
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
        //$contracts->rates;
        //dd($contracts);
        /* foreach ($contracts as $arr) {
            foreach ($arr->rates as $rates) {
                echo $arr->name."  ".$rates->port_origin->name."<br>";

            }
        }*/
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

        $harbor = $objharbor->all()->pluck('name','id');
        $country = $objcountry->all()->pluck('name','id');
        $carrier = $objcarrier->all()->pluck('name','id');
        $currency = $objcurrency->all()->pluck('alphacode','id');
        $calculationT = $objcalculation->all()->pluck('name','id');
        $surcharge = $objsurcharge->where('user_id','=',Auth::user()->id)->pluck('name','id');
        $companies = Company::all()->pluck('business_name','id');
        $contacts = Contract::all()->pluck('name','id');

        return view('contracts.addT',compact('country','carrier','harbor','currency','calculationT','surcharge','companies','contacts'));
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
                $localcharge->changetype = $request->input('changetype.'.$key2);
                $localcharge->calculationtype_id = $request->input('calculationtype.'.$key2);
                $localcharge->ammount = $request->input('ammount.'.$key2);
                $localcharge->currency_id = $request->input('localcurrency_id.'.$key2);
                $localcharge->contract()->associate($contract);
                $localcharge->save();
                $detailport = $request->input('port_id'.$contador);
                $detailcarrier = $request->input('localcarrier_id'.$contador);
                foreach($detailcarrier as $c => $value)
                {
                    $detailcarrier = new LocalCharCarrier();
                    $detailcarrier->carrier_id =$request->input('localcarrier_id'.$contador.'.'.$c);
                    $detailcarrier->localcharge()->associate($localcharge);
                    $detailcarrier->save();
                }
                foreach($detailport as $p => $value)
                {
                    $detailport = new LocalCharPort();
                    $detailport->port = $request->input('port_id'.$contador.'.'.$p);
                    $detailport->localcharge()->associate($localcharge);
                    $detailport->save();
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

        if(!empty($users->isEmpty)){
            foreach($users as $key4 => $value)
            {
                $contract_client_restriction = new ContractClientRestriction();
                $contract_client_restriction->contact_id=$value;
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
        // $contracts = Contract::with('rates')->get();


        $contracts = Contract::with('rates','localcharges.localcharports','localcharges.localcharcarriers')->get()->find($id);



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
        $surcharge = $objsurcharge->where('user_id','=',Auth::user()->id)->pluck('name','id');

        return view('contracts.editT', compact('contracts','harbor','country','carrier','currency','calculationT','surcharge'));
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
                $localcharge->changetype = $request->input('changetype.'.$key2);
                $localcharge->calculationtype_id = $request->input('calculationtype.'.$key2);
                $localcharge->ammount = $request->input('ammount.'.$key2);
                $localcharge->currency_id = $request->input('localcurrency_id.'.$key2);
                $localcharge->contract()->associate($contract);
                $localcharge->save();
                $detailport = $request->input('port_id'.$contador);
                $detailcarrier = $request->input('localcarrier_id'.$contador);
                foreach($detailcarrier as $c => $value)
                {
                    $detailcarrier = new LocalCharCarrier();
                    $detailcarrier->carrier_id =$request->input('localcarrier_id'.$contador.'.'.$c);
                    $detailcarrier->localcharge()->associate($localcharge);
                    $detailcarrier->save();
                }
                foreach($detailport as $p => $value)
                {
                    $detailport = new LocalCharPort();
                    $detailport->port = $request->input('port_id'.$contador.'.'.$p);
                    $detailport->localcharge()->associate($localcharge);
                    $detailport->save();
                }
                $contador++;

            }


        }


        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully update this contract.');

        return redirect()->action('ContractsController@index');


    }



    public function updateRates(Request $request, $id)
    {
        //dd("imi here");
        $requestForm = $request->all();

        $rate = Rate::find($id);
        $rate->update($requestForm);

    }

    public function updateLocalChar(Request $request, $id)
    {

        $requestForm = $request->all();
        $localC = LocalCharge::find($id);
        $localC->update($requestForm);

        $port = $request->input('port');
        $carrier = $request->input('carrier_id');
        $deleteCarrier = LocalCharCarrier::where("localcharge_id",$id);
        $deleteCarrier->delete();
        $deletePort = LocalCharPort::where("localcharge_id",$id);
        $deletePort->delete();

        foreach($port as $key2)
        {
            $detailport = new LocalCharPort();
            $detailport->port = $key2;
            $detailport->localcharge_id = $id;
            $detailport->save();
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
