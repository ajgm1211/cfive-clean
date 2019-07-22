<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
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
        $client = new Client(['base_uri' => 'http://contractsai/']);
        // Send a request to https://foo.com/api/test

        $response = $client->request('GET','ConverterFile/CFIndex',[
            'form_params' => [
                                    'email' => 'admin@example.com'
                                ]
        ]);

        /*$client = new Client();
        $response = $client->post($event->data['auth_post'].'oauth/token', [
            'form_params' => [
                'client_id' => $event->data['client_id'],
                // The secret generated when you ran: php artisan passport:install
                'client_secret' => $event->data['client_secret'],
                'grant_type' => 'password',
                'username' => $event->data['user_name'],
                'password' => $event->data['password'],
                'scope' => '*',
            ]
        ]);
        $auth = json_decode((string)$response->getBody());
        $response = $client->get($event->data['auth_post'].$event->data['url_get'], [
            'headers' => [
                'Authorization' => 'Bearer '.$auth->access_token,
            ]
        ]);*/
        $dataGen = json_decode($response->getBody()->getContents(),true);
        //return $dataGen;
        dd($dataGen);
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
