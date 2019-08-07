<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ApiIntegration;
use App\ApiIntegrationSetting;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Request as ClienteR;
use GuzzleHttp\Message\Response;
use App\Company;

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

    public function getCompanies(){

        $api = ApiIntegrationSetting::where('company_user_id',\Auth::user()->company_user_id)->first();

        $endpoint = "https://demoapi.vforwarding.com/rest/vERP_2_dat_dat/v2/ent_m?api_key=".$api->api_key;

        $client = new Client([
            'headers' => ['Content-Type'=>'application/json','Accept'=>'*/*'],
        ]);

        try {

            $response = $client->get($endpoint, [
                'headers' => [
                    'Content-Type'=>'application/json',
                    'X-Requested-With'=>'XMLHttpRequest',
                ]
            ]);

            $api_response = json_decode( $response->getBody() );

            $this->syncCompanies($api_response);
            
            return response()->json(['message' => 'Ok']);
            
        } catch (GuzzleHttp\Exception\BadResponseException $e) {
            return "Unable to retrieve access token.";
        }
    }

    public function syncCompanies($response){
        $i=0;
        foreach($response->ent_m as $item){
            $exist_com = Company::where('business_name',$item->nom_com)->count();
            if($exist_com==0){
                $company = new Company();
                $company->business_name = $item->nom_com;
                $company->phone = $item->tlf;
                $company->address = $item->address;
                $company->email = $item->eml;
                $company->company_user_id = \Auth::user()->company_user_id;
                $company->owner = \Auth::user()->id;
                $company->save();
            }
            $i++;
        }
        
        return 'Done';
    }
}
