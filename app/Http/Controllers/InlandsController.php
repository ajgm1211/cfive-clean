<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Harbor;
use App\Inland;
use App\InlandPort;
use App\InlandDetail;

class InlandsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Inland::with('inlandports.ports')->get();
        return view('inland/index', ['arreglo' => $data]);
    }

    public function add(){

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
        $inland = new Inland();
        $inland->provider = $request->input('name');
        $inland->type = $request->input('status');
        $validation = explode('/',$request->validation_expire);
        $inland->validity = $validation[0];
        $inland->expire = $validation[1];
        $inland->save();
        $ports = $request->input('irelandports');
        $detailstwuenty =  $request->input('lowertwuenty');
        $detailsforty =  $request->input('lowerforty');
        $detailsfortyhc =  $request->input('lowerfortyhc');
        foreach($ports as $p => $value)
        {
            $inlandport = new InlandPort();
            $inlandport->port = $request->input('irelandports.'.$p);
            $inlandport->inland()->associate($inland);
            $inlandport->save();
        }
        foreach($detailstwuenty as $t => $value)
        {
            if(!empty($request->input('ammounttwuenty.'.$t))) {
                $inlandtwuenty = new InlandDetail();
                $inlandtwuenty->lower = $request->input('lowertwuenty.'.$t);
                $inlandtwuenty->upper = $request->input('uppertwuenty.'.$t);
                $inlandtwuenty->ammount = $request->input('ammounttwuenty.'.$t);
                $inlandtwuenty->type = 'twuenty';
                $inlandtwuenty->currency_id = $request->input('currencytwuenty.'.$t);
                $inlandtwuenty->inland()->associate($inland);
                $inlandtwuenty->save();
            }
        }
        foreach($detailsforty as $t => $value)
        {
            if(!empty($request->input('ammountforty.'.$t))) {
                $inlandforty= new InlandDetail();
                $inlandforty->lower = $request->input('lowerforty.'.$t);
                $inlandforty->upper = $request->input('upperforty.'.$t);
                $inlandforty->ammount = $request->input('ammountforty.'.$t);
                $inlandforty->type = 'forty';
                $inlandforty->currency_id = $request->input('currencyforty.'.$t);
                $inlandforty->inland()->associate($inland);
                $inlandforty->save();
            }
        }

        foreach($detailsfortyhc as $t => $value)
        {
            if(!empty($request->input('ammountfortyhc.'.$t))) {
                $inlandfortyhc = new InlandDetail();
                $inlandfortyhc->lower = $request->input('lowerfortyhc.'.$t);
                $inlandfortyhc->upper = $request->input('upperfortyhc.'.$t);
                $inlandfortyhc->ammount = $request->input('ammountfortyhc.'.$t);
                $inlandfortyhc->type = 'fortyhc';
                $inlandfortyhc->currency_id = $request->input('currencyfortyhc.'.$t);
                $inlandfortyhc->inland()->associate($inland);
                $inlandfortyhc->save();
            }
        }

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
        $inland = Inland::with('inlandports.ports','inlanddetails.currency')->get()->find($id);


        $objharbor = new Harbor();
        $harbor = $objharbor->all()->pluck('name','id');
        return view('inland/edit', compact('harbor','inland'));

    }

    public function updateDetails(Request $request,$id){

        $requestForm = $request->all();
        $inland = InlandDetail::find($id);
        $inland->update($requestForm);
    }
    public function deleteDetails($id){

  
        $inland = InlandDetail::find($id);
        $inland->delete();
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
