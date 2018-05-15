<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contract;
use App\Rate;
use App\Harbor;
class QuoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        /* $data = Contract::with('rates')->get();
        return view('quotation/index', ['arreglo' => $data]);*/

        $objharbor = new Harbor();
        $harbor = $objharbor->all()->pluck('name','id');
        return view('quotation/new', compact('harbor'));


    }
    public function listRate(Request $request)
    {


        $origin_port = $request->input('originport');
        $destiny_port = $request->input('destinyport');
        $arreglo = Rate::where('origin_port', '=',$origin_port)->where('destiny_port', '=',$destiny_port)->with('port_origin','port_destiny','contract')->whereHas('contract', function($q)
                                      {
                                          $q->where('validity', '<=', '2018-05-12')->where('expire', '>=', '2018-05-12');
                                       
                                      })->get();
        
        $formulario = $request;

      foreach($arreglo as $data){
            $subtotal = 0;
            if(!empty($request->input('twuenty'))) {
                $subtotal = ($data->twuenty * $request->input('twuenty')) + $subtotal;
            }
            if(!empty($request->input('forty'))) {
                $subtotal = ($data->forty * $request->input('forty')) + $subtotal;
            }
            if(!empty($request->input('fortyhc'))) {
                $subtotal = ($data->fortyhc * $request->input('fortyhc')) + $subtotal;
            }
          $sub[] =   $subtotal;
          
      }
  
        $objharbor = new Harbor();
        $harbor = $objharbor->all()->pluck('name','id');
        return view('quotation/index', compact('harbor','arreglo','formulario','sub'));


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
        //
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
