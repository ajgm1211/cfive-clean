<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ApiIntegration;
use App\ApiIntegrationSetting;

class ApiIntegrationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $api = ApiIntegrationSetting::where('company_user_id',\Auth::user()->company_user_id)->first();
        
        return view ('api.index',compact('api'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function enable(Request $request)
    {
        $api = ApiIntegrationSetting::where('company_user_id',$request->company_user_id)->first();

        if($api){
            $api->enable = $request->value;
            $api->update();
        }else{
            $api_int = new ApiIntegrationSetting();
            $api_int->company_user_id = $request->company_user_id;
            $api_int->api_integration_id = 1;
            $api_int->enable = $request->value;
            $api_int->save();   
        }

        return response()->json(['message' => 'Ok']);
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
        $api_int = ApiIntegrationSetting::where('company_user_id',$request->company_user_id)->first();
        $api_int->api_key = $request->api_key;
        $api_int->update();

        return response()->json(['message' => 'Ok']);        
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
