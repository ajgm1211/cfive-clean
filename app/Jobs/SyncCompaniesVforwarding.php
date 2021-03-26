<?php

namespace App\Jobs;

use App\ApiIntegration;
use App\Company;
use App\Connection;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncCompaniesVforwarding implements ShouldQueue
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
            $variations = ['VForwarding', 'Vforwarding', 'VF', 'Vf', 'vforwarding'];

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
     * setDataVf
     *
     * @param  mixed $setting
     * @return void
     */
    public function setData($setting)
    {
        $data = new Connection();
        $page = 1;

        do {

            $uri = $setting->url . $page;

            $response = $data->getData($uri);
            $max_page = ceil($response['total_count'] / 1000);

            if ($response['ent_m']) {
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
                        'api_status' => 'VForwarding',
                        'options->vf_code' => $item['id'],
                    ]);
                }
            }
            $page += 1;
        } while ($page <= $max_page);

        \Log::info('Syncronization with ' . $setting->partner->name . ' completed successfully!');
    }
}
