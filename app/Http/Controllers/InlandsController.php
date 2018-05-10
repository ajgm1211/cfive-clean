<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Harbor;
use App\Ireland;
use App\IrelandPort;
use App\IrelandDetail;

class InlandsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $objharbor = new Harbor();
        $harbor = $objharbor->all()->pluck('name','id');
        return view('inland/add', compact('harbor'));


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
    public function store(Request $request)
    {
      //  dd($request);
        $ireland = new Ireland();
        $ireland->provider = $request->input('name');
        $ireland->type = $request->input('status');
        $validation = explode('/',$request->validation_expire);
        $ireland->validity = $validation[0];
        $ireland->expire = $validation[1];
        $ireland->save();
        $ports = $request->input('irelandports');
        $detailstwuenty =  $request->input('lowertwuenty');
        $detailsforty =  $request->input('lowerforty');
        $detailsfortyhc =  $request->input('lowerfortyhc');
        foreach($ports as $p => $value)
        {
            $irelandport = new IrelandPort();
            $irelandport->port = $request->input('irelandports.'.$p);
            $irelandport->ireland()->associate($ireland);
            $irelandport->save();
        }
        foreach($detailstwuenty as $t => $value)
        {
            if(!empty($request->input('ammounttwuenty.'.$t))) {
                $irelandtwuenty = new IrelandDetail();
                $irelandtwuenty->lower = $request->input('lowertwuenty.'.$t);
                $irelandtwuenty->upper = $request->input('uppertwuenty.'.$t);
                $irelandtwuenty->ammount = $request->input('ammounttwuenty.'.$t);
                $irelandtwuenty->type = 'twuenty';
                $irelandtwuenty->currency_id = $request->input('currencytwuenty.'.$t);
                $irelandtwuenty->ireland()->associate($ireland);
                $irelandtwuenty->save();
            }
        }
        foreach($detailsforty as $t => $value)
        {
            if(!empty($request->input('ammountforty.'.$t))) {
                $irelandforty= new IrelandDetail();
                $irelandforty->lower = $request->input('lowerforty.'.$t);
                $irelandforty->upper = $request->input('upperforty.'.$t);
                $irelandforty->ammount = $request->input('ammountforty.'.$t);
                $irelandforty->type = 'forty';
                $irelandforty->currency_id = $request->input('currencyforty.'.$t);
                $irelandforty->ireland()->associate($ireland);
                $irelandforty->save();
            }
        }

        foreach($detailsfortyhc as $t => $value)
        {
            if(!empty($request->input('ammountfortyhc.'.$t))) {
                $irelandfortyhc = new IrelandDetail();
                $irelandfortyhc->lower = $request->input('lowerfortyhc.'.$t);
                $irelandfortyhc->upper = $request->input('upperfortyhc.'.$t);
                $irelandfortyhc->ammount = $request->input('ammountfortyhc.'.$t);
                $irelandfortyhc->type = 'fortyhc';
                $irelandfortyhc->currency_id = $request->input('currencyfortyhc.'.$t);
                $irelandfortyhc->ireland()->associate($ireland);
                $irelandfortyhc->save();
            }
        }




        // dd($request);
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
