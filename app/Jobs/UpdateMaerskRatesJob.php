<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\RateApi;
use Illuminate\Support\Facades\DB;

class UpdateMaerskRatesJob implements ShouldQueue
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
        
        $rates = DB::table('rate_apis')
            ->select('origin_port', 'destiny_port')
            ->join('contract_apis', function ($join) {

                $join->on('contract_apis.id', '=', 'rate_apis.contract_id')
                    ->where('number', '=', 'MAERSK'); 
                })
            ->groupBy('origin_port', 'destiny_port')
            ->get();


        foreach ($rates as $rate) {
            
            DB::table('rate_apis')
                ->where('origin_port', '=', $rate->origin_port)
                ->where('destiny_port', '=', $rate->destiny_port)
                ->join('contract_apis', function ($join) { 

                    $join->on('contract_apis.id', '=', 'rate_apis.contract_id')
                        ->where('number', '=', 'MAERSK'); 
                    })
                ->delete();

            #Execute Class or Client
        }
    }
}
