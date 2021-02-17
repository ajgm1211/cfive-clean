<?php

namespace App\Jobs;

use App\ApiIntegration;
use App\ApiIntegrationSetting;
use App\Company;
use App\Connection;
use App\Visualtrans;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncCompaniesPerHourJob implements ShouldQueue
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
        try {

            $integrations = ApiIntegration::where(['module' => 'Companies', 'frecuency' => 30, 'status' => 1])->with('partner')->get();

            foreach ($integrations as $setting) {
                if ($setting->partner->name == 'Visualtrans') {
                    $this->setDataVs($setting);
                } elseif ($setting->partner->name == 'VForwarding') {
                    $this->setDataVf($setting);
                }
            }
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
    }

    /**
     * setDataVs
     *
     * @param  mixed $setting
     * @return void
     */
    public function setDataVs($setting)
    {
        $data = new Connection();

        $page = 1;

        do {

            $uri =  $setting->url . '&k=' . $setting->api_key . '&p=' . $page;

            $response = $data->getData($uri);

            $max_page = ceil($response['count'] / 100);

            foreach ($response['entidades'] as $item) {

                if ($item['fecha-alta'] >= '2020-01-01') {

                    Company::updateOrCreate([
                        'tax_number' => $item['cif-nif'],
                        'company_user_id' => $setting->company_user_id,
                    ], [
                        'business_name' => $item['nombre-fiscal'],
                        'tax_number' => $item['cif-nif'],
                        'company_user_id' => $setting->company_user_id,
                        'api_id' => $item['codigo'],
                        'options->vs_code' => $item['codigo'],
                    ]);
                }
            }

            $page += 1;
        } while ($page <= $max_page);

        \Log::info('Syncronization with ' . $setting->partner->name . ' completed successfully!');
    }

    /**
     * setDataVf
     *
     * @param  mixed $setting
     * @return void
     */
    public function setDataVf($setting)
    {
        $data = new Connection();
        $page = 1;
        do {

            $uri = $setting->url . $page;

            $response = $data->getData($uri);
            $max_page = ceil($response['total_count'] / 1000);

            foreach ($response['ent_m'] as $item) {
                Company::updateOrCreate([
                    'tax_number' => $item['cif'],
                    'company_user_id' => $setting->company_user_id,
                ], [
                    'business_name' => $item['nom_com'],
                    'tax_number' => $item['cif'],
                    'address' => $item['address'],
                    'phone' => $item['tlf'],
                    'company_user_id' => $setting->company_user_id,
                    'api_id' => $item['id'],
                    'options->vf_code' => $item['id'],
                ]);
            }
            $page += 1;
        } while ($page <= $max_page);

        \Log::info('Syncronization with ' . $setting->partner->name . ' completed successfully!');
    }
}
