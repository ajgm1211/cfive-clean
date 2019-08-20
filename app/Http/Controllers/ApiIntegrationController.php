<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ApiIntegration;
use App\ApiIntegrationSetting;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Request as ClienteR;
use GuzzleHttp\Message\Response;
use App\Company;
use App\Contact;

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
        $api = ApiIntegrationSetting::where('company_user_id',$request->company_user_id)->count();

        if($api>0){
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
            $exist_com = Company::where('business_name',$item->nom_com)->get();

            if($exist_com->count()==0){
                $company = new Company();
                $company->business_name = $item->nom_com;
                $company->phone = $item->tlf;
                $company->address = $item->address;
                $company->email = $item->eml;
                $company->company_user_id = \Auth::user()->company_user_id;
                $company->owner = \Auth::user()->id;
                $company->api_id = $item->id;
                $company->api_status = 'created';
                $company->save();

                $contacts = $this->getContacts($item->id);
                
                foreach($contacts->ent_rel_m as $v){
                    $exist_cont = Contact::where('api_id',$item->ent_rel)->count();

                    if($exist_cont==0){
                        $contact = new Contact();
                        $contact->first_name = $v->name;
                        $contact->phone = $item->tlf;
                        $contact->email = $item->eml;
                        $contact->position = $v->dsc;
                        $contact->company_id = $v->ent_rel;
                        $contact->api_id = $v->ent_rel;
                        $contact->save();
                    }
                }
            }

            $i++;
        }

        return 'Done';
    }

    public function getContacts($company_id){
        $api = ApiIntegrationSetting::where('company_user_id',\Auth::user()->company_user_id)->first();

        $endpoint = "https://demoapi.vforwarding.com/rest/vERP_2_dat_dat/v2/ent_rel_m?filter%5Bent_rel%5D=".$company_id."&api_key=".$api->api_key;

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

            return $api_response;

        } catch (GuzzleHttp\Exception\BadResponseException $e) {
            return "Unable to retrieve access token.";
        }
    }
}
