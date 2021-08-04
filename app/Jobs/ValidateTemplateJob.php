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
        $endpoint_obj = EndpointTable::where("name","barracuaep-template")->first();
        if($endpoint_obj->status == 1){
            $json = '{"spreadsheetData":false,"type":"FCL"}';
            $url = $endpoint_obj->url."contracts/processing/".$this->request_id;

            try{
                $response = $client->request('POST',$url,['headers' => $headers,'body'=>$json]);
                $response = json_decode($response->getBody()->getContents(),true);
            }catch(\Exception $e){
                $response = false;
            }
            
        }
        
        $endpoint_obj = EndpointTable::where("name","barracuaep-generate-mask")->first();
        if($endpoint_obj->status == 1){
            $json = '{"type":"FCL"}';
            $url = $endpoint_obj->url."requests/generateMask/".$this->request_id;

            try{
                $response = $client->request('POST',$url,['headers' => $headers,'body'=>$json]);
                $response = json_decode($response->getBody()->getContents(),true);
            }catch(\Exception $e){
                $response = false;
            }
            
        }

        $endpoint_obj_cmpfile = EndpointTable::where("name","barracuaep-cmpfile")->first();
        if($endpoint_obj_cmpfile->status == 1){
            $url = $endpoint_obj_cmpfile->url."requests/cmpfiles/".$this->request_id;
            $json = '{"duplicate":true,"re_search":true}';
            try{
                $response = $client->request('POST',$url,['headers' => $headers,'body'=>$json]);
                $response = json_decode($response->getBody()->getContents(),true);
            }catch(\Exception $e){
                $response = false;
            }
        }
    }
}
