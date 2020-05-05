<?php

namespace App\Jobs;

use App\ApiIntegrationSetting;
use App\Company;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncCompaniesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $api = ApiIntegrationSetting::where('company_user_id', \Auth::user()->company_user_id)->first();

        $endpoint = $api->url . "ent_m?" . $api->key_name . "=" . $api->api_key;

        $client = new Client([
            'verify' => false,
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

            $this->syncCompanies($api_response);

            return response()->json(['message' => 'Ok']);
        } catch (GuzzleHttp\Exception\BadResponseException $e) {
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
}
