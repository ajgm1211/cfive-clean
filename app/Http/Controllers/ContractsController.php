<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contract;
use App\Country;
use App\Carrier;
use App\Harbor;
use App\Rate;
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

        $contracts = Contract::with('rates')->get();
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

        $harbor = $objharbor->all()->pluck('name','id');
        $country = $objcountry->all()->pluck('name','id');
        $carrier = $objcarrier->all()->pluck('name','id');


        return view('contracts.addT',compact('country','carrier','harbor'));
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
                $rates->currency = $request->input('currency.'.$key);
                $rates->contract()->associate($contract);
                $rates->save();

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
        $contracts = Contract::find($id);
        $contracts->rates;
        $objcountry = new Country();
        $objcarrier = new Carrier();
        $objharbor = new Harbor();

        $harbor = $objharbor->all()->pluck('name','id');
        $country = $objcountry->all()->pluck('name','id');
        $carrier = $objcarrier->all()->pluck('name','id');
        //$objcountry = new Country();
        //$objcarrier = new Carrier();
        //$country = $objcountry->all()->pluck('name','id');
        //$carrier = $objcarrier->all()->pluck('name','id');

        return view('contracts.editT', compact('contracts','harbor','country','carrier'));
    }
    /*
    public function edit($id)
    {
        $contracts = Contract::find($id);
        $objcountry = new Country();
        $objcarrier = new Carrier();
        $country = $objcountry->all()->pluck('name','id');
        $carrier = $objcarrier->all()->pluck('name','id');
        return view('contracts.edit', compact('contracts','country','carrier'));
    }*/

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
                $rates->currency = $request->input('currency.'.$key);
                $rates->contract()->associate($contract);
                $rates->save();

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
