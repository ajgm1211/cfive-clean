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
        try {
            $integrations = ApiIntegration::where(['module', 'Companies','status'=>1])->with('partner')->get();
            foreach ($integrations as $setting) {
                $this->setData($setting);
            }
        } catch (\Exception $e) {
            $e->getMessage();
        }
    }
    public function setData($setting)
    {
        $data = new Connection();
        $page = 1;
        do {
            $uri =  $setting->url . '&k=' . $setting->api_key . '&p=' . $page;
            $response = $data->getData($uri);
            $max_page = ceil($response['count'] / 100);
            foreach ($response['entidades'] as $item) {
                $data = new Connection();
                $invoice = $data->getInvoices($item['codigo']);
                if ($invoice) {
                    Company::updateOrCreate([
                        'api_id' => $item['codigo']
                    ], [
                        'business_name' => $item['nombre-fiscal'],
                        'tax_number' => $item['cif-nif'],
                        'company_user_id' => $setting->company_user_id,
                        'api_id' => $item['codigo'],
                        'api_status' => 'created',
                    ]);
                }
            }
            $page += 1;
        } while ($page <= $max_page);
        \Log::info('Syncronization with vForwarding completed successfully!');
    }
}
