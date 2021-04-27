<?php

namespace App\Jobs;

use App\EndpointTable;
use App\AuthtokenToken;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ValidateTemplateJob implements ShouldQueue
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

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $endpoint_obj = EndpointTable::where("name","barracuaep")->first();
        if($endpoint_obj->status == 1){
            $client = new \GuzzleHttp\Client();
            $url = $endpoint_obj->url."contracts/processing/".$this->request_id;
            $json = '{"spreadsheetData":false}';
            $token = AuthtokenToken::where('user_id',1)->first();
            $headers = [
                'Authorization' => 'token '.$token->key,
                'Accept'        => '*/*',
                'Content-Type'  => 'application/json',
                'User-Agent'    => '*/*',
                'Connection'    => 'keep-alive'
            ];

            $response = $client->request('POST',$url,['headers' => $headers,'body'=>$json]);
            $response = json_decode($response->getBody()->getContents(),true);

            $url = $endpoint_obj->url."requests/cmpfiles/".$this->request_id;
            $json = '{"duplicate":true,"re_search":true}';

            $response = $client->request('POST',$url,['headers' => $headers,'body'=>$json]);
            $response = json_decode($response->getBody()->getContents(),true);
        }
    }
}
