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
use App\Http\Requests\StoreApiIntegration;

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
        $partners = Partner::pluck('name', 'id');
        return view('api.index', compact('api', 'partners'));
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
    public function store(StoreApiIntegration $request)
    {
        ApiIntegration::create($request->all());

        $request->session()->flash('message.content', 'Record saved successfully');
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
        return response()->json([
            'data' => ApiIntegration::find($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $api = ApiIntegration::find($request->api_integration_id);

        $api->update($request->all());

        $request->session()->flash('message.content', 'Record updated successfully');
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $api = ApiIntegration::find($id)->delete();

        return response()->json([
            'message' => 'Ok'
        ]);
    }

    public function getCompanies()
    {
        $setting = ApiIntegration::where('module', 'Companies')->whereHas('api_integration_setting', function ($query) {
            $query->where('company_user_id', \Auth::user()->company_user_id);
        })->with('partner')->first();

        $setting->status = 1;
        $setting->save();

        $endpoint = $setting->url . "=" . $setting->api_key;

        try {

            $client = new Client([
                'verify' => false,
                'headers' => ['content-type' => 'application/json', 'Accept' => 'applicatipon/json', 'charset' => 'utf-8']
            ]);

            $response = $client->get($endpoint);

            $type = $response->getHeader('content-type');

            $type = explode(';', $type[0]);

            $api_response = $response->getBody()->getContents();

            if ($type[1] == 'charset=iso-8859-1') {
                $api_response = iconv("iso-8859-1", "UTF-8", $api_response);
            }

            $result = json_decode($api_response, true);

            SyncCompaniesJob::dispatch($result, \Auth::user(), $setting->partner);

            return response()->json(['message' => 'Ok']);

        } catch (\Exception $e) {
            $setting->status = 0;
            $setting->save();
            return response()->json(['error' => $e->getCode()]);
        }
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
