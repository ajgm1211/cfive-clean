<?php

namespace App\Http\Clients;
use GuzzleHttp\Client as GuzzleClient;

class ShenlongClient 
{
    private $client;
    private $accessToken;

    public function __construct()
    {   
        $baseUrl = 'https://demoapi.cargofive.com';
        
        $this->client = new GuzzleClient(['base_uri' => $baseUrl]);
        //$this->accessToken = $this->getAccessToken();
        //dd($this->accessToken);
    }

    private function getAccessToken()
    {   
        $params = [
            "username" => "testapi@cargofive.com",
            "password" => "t3st22Ap1*",
            "secret" => "LlkYz4acUAu5mAqsBOcuTv2SUkXykWosL4lmtMBv",
            "client_id" => 16
        ];
        
        $response = $this->client->request(
            'POST', 
            '/api/login', 
            [
                "headers" => ['Content-Type' => 'application/json', 'Accept' => '*/*'],
                "form_params" => $params
            ]
        );
        //dd($response->getBody());
        $data = json_decode($response->getBody(), true);

        return $data['access_token'];
    }

    private function getHeaders()
    {
        return [
            'Accept' => 'application/json',
            // 'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json'
        ];
    }

    public function schedules($origin, $destiny)
    {
        //dd("hola");
        $response = $this->client->request(
            'GET', 
            '/schedules'. '/' . $origin .'/' . $destiny, 
            ['headers' => $this->getHeaders()]
        );
        dd($response);
        return json_decode($response->getBody(), true);
    }

     /*
      try {
          return $this->shenglongClient->schedules($origin, $destiny);
      } catch (ClientException $exception) {
          dd($exception->getResponse()->getBody()->getContents());
      }
      */

} 
