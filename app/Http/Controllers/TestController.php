<?php

namespace App\Http\Controllers;
use App\User;
use GuzzleHttp\Client;
use App\RequetsCarrierFcl;
use App\NewContractRequest;
use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_adm = User::where('email','admin@example.com')->orWhere('email','info@cargofive.com')->first();
        $client = new Client(['base_uri' => 'http://contractsai/']);
        // Send a request to https://foo.com/api/test
        //$response = $client->get('login?email=admin@example.com&password=secret');
        //$auth = json_decode((string)$response->getBody());
        //$response = $client->request('GET','ConverterFile/CFIndex', [
        $response = $client->request('GET','ConverterFile/CFDispatchJob/5', [
            'headers' => [
                //'Authorization' => $auth->api_key,
                'Authorization' => 'Bearer '.$user_adm->api_token,
                'Accept'        => 'application/json',
            ]
        ]);
        $dataGen = json_decode($response->getBody()->getContents(),true);
        dd($dataGen);
        return $dataGen;
        //dd($dataGen);
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
        
        dd($id);
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
