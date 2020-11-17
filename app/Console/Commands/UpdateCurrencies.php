<?php

namespace App\Console\Commands;

use App\Currency;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class updateCurrencies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:updateCurrenciesUsd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update daily currencies table from API Currency Layer';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		try{
			// set API Endpoint and access key (and any options of your choice)
			$endpoint = 'live';
			$access_key = 'a0a9f774999e3ea605ee13ee9373e755';

			// Initialize CURL:
			$ch = curl_init('http://apilayer.net/api/'.$endpoint.'?access_key='.$access_key.'');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			// Store the data:
			$json = curl_exec($ch);
			curl_close($ch);

			// Decode JSON response:
			$exchangeRates = json_decode($json, true);
			
			foreach($exchangeRates['quotes'] as $key=>$value){
				$currency=Currency::where('api_code',$key)->first();
				if(isset($currency)){
					if($currency->rates!=$value){
						Currency::where('id',$currency->id)
							->update(['api_code' => $key, 'rates' => $value]);
					}
				}
			}
		
		} catch(\Exception $e){
            return $this->info($e->getMessage());
        }

        $this->info('Command Update Currencies executed successfully!');
    }
}
