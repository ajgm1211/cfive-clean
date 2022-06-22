<?php

namespace App\Http\Controllers;

use App\ApiIntegration;
use App\ApiIntegrationSetting;
use App\CompanyUser;
use App\Http\Requests\StoreApiIntegration;
use App\Jobs\SyncCompaniesEvery30Job;
use App\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\ViewQuoteV2;

class ApiIntegrationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $api = ApiIntegrationSetting::with('api_integration')->first();
        $partners = Partner::pluck('name', 'id');
        $companies = CompanyUser::pluck('name', 'id');

        return view('api.index', compact('api', 'partners', 'companies'));
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
            DB::table('api_integration_settings')->update(['status' => $request->enable]);
            DB::table('api_integrations')->update(['status' => $request->enable]);
        } else {
            $api = new ApiIntegrationSetting();
            $api->company_user_id = $request->company_user_id;
            $api->status = $request->enable;
            $api->save();
        }

        return response()->json(['data' => $api]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request)
    {
        $apiU = ApiIntegration::where('id', $request->id)->first();

        if (!empty($apiU)) {
            $apiU->status = $request->status;
            $apiU->update();
        } else {
            $apiU = new ApiIntegration();
            $apiU->status = $request->status;
            $apiU->save();
        }

        return response()->json(['data' => $apiU]);
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
        $ap = new ApiIntegration($request->all());
        $ap->status = 0;
        $ap->save();

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
            'data' => ApiIntegration::find($id),
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
            'message' => 'Ok',
        ]);
    }

    public function getCompanies()
    {
        //SyncCompaniesEvery30Job::dispatch();
    }

    public function getContacts($company_id)
    {
        $api = ApiIntegrationSetting::where('company_user_id', \Auth::user()->company_user_id)->first();

        $endpoint = 'https://demoapi.vforwarding.com/rest/vERP_2_dat_dat/v2/ent_rel_m?filter%5Bent_rel%5D=' . $company_id . '&api_key=' . $api->api_key;

        $client = new Client([
            'headers' => ['Content-Type' => 'application/json', 'Accept' => '*/*'],
        ]);

        try {
            $response = $client->get($endpoint, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-Requested-With' => 'XMLHttpRequest',
                ],
            ]);

            $api_response = json_decode($response->getBody());

            return $api_response;
        } catch (\Exception $e) {
            return 'Error: ' . $e;
        }
    }
}
