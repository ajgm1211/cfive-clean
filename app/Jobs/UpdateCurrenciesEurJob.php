<?php

namespace App\Jobs;

use App\Currency;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateCurrenciesEurJob implements ShouldQueue
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
            // set API Endpoint and access key (and any options of your choice)
            $endpoint = 'live';
            $access_key = 'a0a9f774999e3ea605ee13ee9373e755';

            // Initialize CURL:
            $ch = curl_init('http://apilayer.net/api/'.$endpoint.'?access_key='.$access_key.'&source=EUR');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Store the data:
            $json = curl_exec($ch);
            curl_close($ch);

            // Decode JSON response:
            $exchangeRates = json_decode($json, true);

            foreach ($exchangeRates['quotes'] as $key=>$value) {
                $currency = Currency::where('api_code_eur', $key)->first();
                if (isset($currency)) {
                    if ($currency->rates_eur != $value) {
                        Currency::where('id', $currency->id)
                            ->update(['api_code_eur' => $key, 'rates_eur' => $value]);
                    }
                }
            }
        } catch (\Exception $e) {
            $e->getMessage();
        }
    }
}
