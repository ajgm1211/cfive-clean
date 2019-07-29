<?php

namespace App\Jobs;

use App\User;
use GuzzleHttp\Client;
use App\AutoImportation;
use App\NewContractRequest;
use App\Jobs\SendEmailAutoImporJob;
use GuzzleHttp\Exception\RequestException;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SelectionAutoImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $id_req,$selector;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id_req,$selector)
    {
        $this->id_req = $id_req;
        $this->selector = $selector;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->selector == 'fcl'){
            $req_id = $this->id_req;
            $request_cont = NewContractRequest::find($req_id);
            $request_cont = $request_cont->load('Requestcarriers');
            $user_adm_rq = User::where('email','admin@example.com')->orWhere('email','info@cargofive.com')->first();
            $admins = User::where('type','admin')->get();
            if(count($request_cont->Requestcarriers) == 1){
                $autoImp = AutoImportation::whereHas('carriersAutoImportation',function($query) use($request_cont) {
                    $query->whereIn('carrier_id',$request_cont->Requestcarriers->pluck('carrier_id'));
                })->where('status',1)->first();
                if(!empty($autoImp)){
                    try{
                        if(env('APP_ENV') == 'local'){
                            $client = new Client(['base_uri' => 'http://contractsai/']);                            
                        }else if(env('APP_ENV') == 'developer'){
                            $client = new Client(['base_uri' => 'dev.contractsai.cargofive.com']);
                        }else{
                            $client = new Client(['base_uri' => 'prod.contractsai.cargofive.com']);
                        }
                        //$response = $client->get('login?email=admin@example.com&password=secret');
                        //$response = $client->request('GET','ConverterFile/CFIndex', [
                        $response = $client->request('GET','ConverterFile/CFDispatchJob/'.$req_id, [
                            'headers' => [
                                //'Authorization' => $auth->api_key,
                                'Authorization' => 'Bearer '.$user_adm_rq->api_token,
                                'Accept'        => 'application/json',
                            ]
                        ]);
                        $dataGen = json_decode($response->getBody()->getContents(),true);
                        //return $dataGen;
                    } catch (RequestException $e) {
                        //Enviar correo falla de conexion
                        $message = 'connection failure, Request Id: '.$req_id.' I qualify for Auto-Import';
                        foreach($admins as $userNotifique){
                            SendEmailAutoImporJob::dispatch($userNotifique->email,$message);
                        }
                    }
                }
            } else{
                $autoImp = AutoImportation::whereHas('carriersAutoImportation',function($query) use($request_cont) {
                    $query->whereIn('carrier_id',$request_cont->Requestcarriers->pluck('carrier_id'));
                })->where('status',1)->first();
                if(!empty($autoImp)){
                    //Enviar correo
                    $message = 'There is more than one carrier and one of them are listed in the Auto Import. Request Id: '.$req_id;
                    foreach($admins as $userNotifique){
                        SendEmailAutoImporJob::dispatch($userNotifique->email,$message);
                    }
                } 
            }
        }
    } 
}
