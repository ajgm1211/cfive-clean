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

    protected $response;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($response, $user)
    {
        $this->response = $response;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $i = 0;
        foreach ($this->response->ent_m as $item) {
            if ($item->es_emp) {

                $exist_com = Company::where('business_name', $item->nom_com)->get();

                if ($exist_com->count() == 0) {
                    $company = new Company();
                    $company->business_name = $item->nom_com;
                    $company->phone = $item->tlf;
                    $company->address = $item->address;
                    $company->email = $item->eml;
                    $company->company_user_id = $this->user->company_user_id;
                    $company->owner = $this->user->id;
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

        $setting = ApiIntegrationSetting::where('company_user_id', $this->user->company_user_id)->first();
        $setting->status = 0;
        $setting->save();
    }
}
