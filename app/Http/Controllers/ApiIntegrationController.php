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
use App\Jobs\SyncCompaniesJob;
use App\Partner;

class ApiIntegrationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $api = ApiIntegrationSetting::where('company_user_id', \Auth::user()->company_user_id)->with('api_integration')->first();
        $partners = Partner::pluck('name','id');
        return view('api.index', compact('api','partners'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function enable(Request $request)
    {
        $api = ApiIntegrationSetting::where('company_user_id', $request->company_user_id)->first();

        if (!empty($api)) {
            $api->enable = $request->enable;
            $api->update();
        } else {
            $api = new ApiIntegrationSetting();
            $api->company_user_id = $request->company_user_id;
            $api->enable = $request->enable;
            $api->save();
        }

        return response()->json(['data' => $api]);
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
        $api_int = new ApiIntegration();
        $api_int->name = $request->name;
        $api_int->api_key = $request->api_key;
        $api_int->url = $request->url;
        $api_int->module = $request->module;
        $api_int->api_integration_setting_id = $request->api_integration_setting_id;
        $api_int->save();

        $request->session()->flash('message.content', 'Record saved successfully' );
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');

        return redirect()->back(); 
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

    public function getCompanies()
    {
        $user = \Auth::user();

        $client = new Client([
            'verify' => false,
            'headers' => ['Content-Type' => 'application/json', 'Accept' => '*/*'],
        ]);

        $setting = ApiIntegration::where('module', 'Companies')->whereHas('api_integration_setting', function ($query) {
            $query->where('company_user_id', \Auth::user()->company_user_id);
        })->with('partner')->first();
        
        $setting->status = 1;
        $setting->save();

        $endpoint = $setting->url . "=" . $setting->api_key;
        //$endpoint = 'https://pr-altius.visualtrans.net/rest/api1-entidades.pro?k=ENTICARGOFIVE75682100';

        try {

            $response = $client->get($endpoint, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-Requested-With' => 'XMLHttpRequest',
                ]
            ]);
            
            $api_response = json_decode($response->getBody());
            
            SyncCompaniesJob::dispatch($api_response, $user, $setting->partner);

            return response()->json(['message' => 'Ok']);
            
        } catch (\Exception $e) {
            $setting->status = 0;
            $setting->save();
            return "Error: " . $e;
        }
    }

    public function syncCompanies($response)
    {
        $i = 0;
        foreach ($response->ent_m as $item) {
            if ($item->es_emp) {

                $exist_com = Company::where('business_name', $item->nom_com)->get();

                if ($exist_com->count() == 0) {
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

                    /*$contacts = $this->getContacts($item->id);
                
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
                }*/
                }
            }

            $i++;
        }

        return 'Done';
    }

    public function getContacts($company_id)
    {
        $api = ApiIntegrationSetting::where('company_user_id', \Auth::user()->company_user_id)->first();

        $endpoint = "https://demoapi.vforwarding.com/rest/vERP_2_dat_dat/v2/ent_rel_m?filter%5Bent_rel%5D=" . $company_id . "&api_key=" . $api->api_key;

        $client = new Client([
            'headers' => ['Content-Type' => 'application/json', 'Accept' => '*/*'],
        ]);

        try {

            $response = $client->get($endpoint, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-Requested-With' => 'XMLHttpRequest',
                ]
            ]);

            $api_response = json_decode($response->getBody());

            return $api_response;
        } catch (\Exception $e) {
            return "Error: " . $e;
        }
    }
}
