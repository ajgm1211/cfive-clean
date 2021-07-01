<?php

namespace App\Jobs;

use App\EndpointTable;
use App\AuthtokenToken;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ValidateTemplateLclJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $request_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request_id)
    {
        $this->request_id = $request_id;
    }

    public function handle()
    {
        $client = new \GuzzleHttp\Client();
        $token = AuthtokenToken::where('user_id',1)->first();
        $headers = [
            'Authorization' => 'token '.$token->key,
            'Accept'        => '*/*',
            'Content-Type'  => 'application/json',
            'User-Agent'    => '*/*',
            'Access-Control-Allow-Origin'    => '*',
            'Connection'    => 'keep-alive'
        ];
        $endpoint_obj = EndpointTable::where("name","barracuaep-template-lcl")->first();
        if($endpoint_obj->status == 1){
            $json = '{"spreadsheetData":false,"type":"LCL"}';
            $url = $endpoint_obj->url."contracts/processing/".$this->request_id;

            $response = $client->request('POST',$url,['headers' => $headers,'body'=>$json]);
            $response = json_decode($response->getBody()->getContents(),true);
        } 

        $endpoint_obj_cmpfile = EndpointTable::where("name","barracuaep-cmpfile-lcl")->first();
        if($endpoint_obj_cmpfile->status == 1){
            $url = $endpoint_obj_cmpfile->url."requestsLCL/cmpfiles/".$this->request_id;
            $json = '{"duplicate":true,"re_search":true}';

            $response = $client->request('POST',$url,['headers' => $headers,'body'=>$json]);
            $response = json_decode($response->getBody()->getContents(),true);
        }
    }
}
