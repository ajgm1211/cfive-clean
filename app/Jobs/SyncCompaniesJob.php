<?php

namespace App\Jobs;

use App\ApiIntegration;
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
    protected  $partner;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($response, $user, $partner)
    {
        $this->response = $response;
        $this->user = $user;
        $this->partner = $partner;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        switch($this->partner->name){
            case 'vForwarding':
                $i = 0;
                
                foreach ($this->response['ent_m'] as $item) {
                    if ($item['es_emp']) {

                        Company::updateOrCreate([
                            'api_id' => $item['id']
                        ], [
                            'business_name' => $item['nom_com'],
                            'phone' => $item['tlf'],
                            'address' => $item['address'],
                            'email' => $item['eml'],
                            'tax_number' => $item['cif'],
                            'company_user_id' => $this->user->company_user_id,
                            'owner' => $this->user->id,
                            'api_id' => $item['id'],
                            'api_status' => 'created',
                        ]);
                    }
        
                    $i++;
                }
                $company_user = $this->user->company_user_id;
                $setting = ApiIntegration::where('module', 'Companies')->whereHas('api_integration_setting', function ($query) use($company_user) {
                    $query->where('company_user_id', $company_user);
                })->first();
                $setting->status = 0;
                $setting->save();
            break;

            case 'Visualtrans':
                $i = 0;

                foreach ($this->response['entidades'] as $item) {

                        Company::updateOrCreate([
                            'api_id' => $item['codigo']
                        ], [
                            'business_name' => $item['nombre-fiscal'],
                            'tax_number' => $item['cif-nif'],
                            'company_user_id' => $this->user->company_user_id,
                            'owner' => $this->user->id,
                            'api_id' => $item['id'],
                            'api_status' => 'created',
                        ]);
        
                    $i++;
                }

            break;
        }
    }
}