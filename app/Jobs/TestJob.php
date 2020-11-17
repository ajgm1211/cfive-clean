<?php

namespace App\Jobs;

use App\Rate;
use App\Harbor;
use App\Country;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class TestJob implements ShouldQueue
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

        $ratesCh = Rate::where('containers','!=','null')->where('containers','!=',"[]")->get()->chunk(200);
        //$containerRates = [];
        foreach($ratesCh as $rates){
            foreach($rates as $rate){
                if(is_string($rate->containers)){
                    $containers = json_decode($rate->containers,true);
                    if(is_string($containers)){
                        //$containerRates[$rate->id] = json_decode($containers,true);
                        $rate->containers = $containers;
                    } else {
                        $rate->containers = json_encode($containers);
                        //$containerRates[$rate->id] = $containers;
                    }
                    $rate->update();
                }
            }
        }
        //dd($containerRates);


        /*$harbors = Harbor::all();
        $caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':'];
        foreach($harbors as $harbor){
        $arreglo = null;
            $vatiationsH = json_decode($harbor->varation);
            foreach($vatiationsH->{'type'} as $variation){
                $arreglo[] =  str_replace($caracteres,'',trim(strtolower($variation)));
            }

            $type['type']       = $arreglo;
            $json               = json_encode($type);
            $harbor->varation   = $json;
            $harbor->update();
        }

        $countries  = Country::all();
        foreach($countries as $country){
        $arreglo = null;
            $vatiationsC = json_decode($country->variation);
            foreach($vatiationsC->{'type'} as $variation){
                $arreglo[] =  str_replace($caracteres,'',trim(strtolower($variation)));
            }
            $type['type']       = $arreglo;
            $json               = json_encode($type);
            $country->variation = $json;
            $country->update();
        }*/
    }
}
