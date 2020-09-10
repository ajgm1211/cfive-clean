<?php

namespace App\Jobs;

use App\ApiIntegration;
use App\ApiIntegrationSetting;
use App\Company;
use App\Visualtrans;
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
    protected $company_user_id;
    protected $partner;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($response, $integration)
    {
        $this->response = $response;
        $this->partner = $integration->partner;
        $this->company_user_id = $integration->company_user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        switch ($this->partner->name) {
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
                            'company_user_id' => $this->company_user_id,
                            'api_id' => $item['id'],
                            'api_status' => 'created',
                        ]);
                    }

                    $i++;
                }
                $company_user = $this->company_user_id;
                $setting = ApiIntegration::where('module', 'Companies')->whereHas('api_integration_setting', function ($query) use ($company_user) {
                    $query->where('company_user_id', $company_user);
                })->first();
                $setting->status = 0;
                $setting->save();
                break;

            case 'Visualtrans':

                foreach ($this->response['entidades'] as $item) {
                    
                    $data = new Visualtrans();
                    $invoice = $data->getInvoices($item['codigo']);

                    if ($invoice) {
                        Company::updateOrCreate([
                            'api_id' => $item['codigo']
                        ], [
                            'business_name' => $item['nombre-fiscal'],
                            'tax_number' => $item['cif-nif'],
                            'company_user_id' => $this->company_user_id,
                            'api_id' => $item['codigo'],
                            'api_status' => 'created',
                        ]);
                    }
                }

                break;
        }
    }
}
