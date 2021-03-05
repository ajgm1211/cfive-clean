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

class SyncCompaniesVisualtrans implements ShouldQueue
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
            $variations = ['Visualtrans', 'visualtrans', 'visual', 'vt'];

            $integration = ApiIntegration::where('status', 1)->whereHas('partner', function ($query) use ($variations) {
                $query->whereIn('name', $variations);
            })->with('partner')->get();

            foreach ($integration as $conf) {
                $this->setData($conf);
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
    public function setData($setting)
    {
        $data = new Connection();

        $page = 1;

        $total_page = 100;

        do {

            $uri =  $setting->url . '&k=' . $setting->api_key . '&p=' . $page;

            $response = $data->getData($uri);

            $max_page = ceil($response['count'] / $total_page);

            if ($response['entidades']) {
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
            }

            $page += 1;
        } while ($page <= $max_page);

        \Log::info('Syncronization with ' . $setting->partner->name . ' completed successfully!');
    }
}
