<?php

namespace App\Jobs;

use Excel;
use App\User;
use PrvHarbor;
use PrvCarrier;
use App\Harbor;
use PrvRatesLcl;
use App\RateLcl;
use App\Carrier;
use App\Currency;
use App\FailRateLcl;
use App\CompanyUser;
use App\ContractLcl;
use App\ScheduleType;
use App\Notifications\N_general;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ReprocesarRatesLclJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $id = $this->id;
        $failrates = FailRateLcl::where('contractlcl_id','=',$id)->get();
        // dd($failrates);
        foreach($failrates as $failrate){

            $carrierEX          = '';
            $wmEX               = '';
            $minimunEX          = '';
            $currencyEX         = '';
            $originResul        = '';
            $originExits        = '';
            $originV            = '';
            $destinResul        = '';
            $destinationExits   = '';
            $destinationV       = '';
            $originEX           = '';
            $destinyEX          = '';
            $wmVal              = '';
            $minimunVal         = '';
            $carrierVal         = '';
            $carrierArr         = '';
            $wmArr              = '';
            $minimunArr         = '';
            $scheduleTVal       = null;


            $curreExitBol       = false;
            $originB            = false;
            $destinyB           = false;
            $wmExiBol           = false;
            $minimunExiBol      = false;
            $values             = true;
            $carriExitBol       = false;
            $scheduleTBol       = false;

            $originEX       = explode('_',$failrate->origin_port);
            $destinyEX      = explode('_',$failrate->destiny_port);
            $carrierArr     = explode('_',$failrate->carrier_id);
            $wmArr          = explode('_',$failrate->uom);
            $minimunArr     = explode('_',$failrate->minimum);
            $currencyArr    = explode('_',$failrate->currency_id);
            $scheduleTArr   = explode('_',$failrate->schedule_type);

            $carrierEX     = count($carrierArr);
            $wmEX          = count($wmArr);
            $minimunEX     = count($minimunArr);
            $currencyEX    = count($currencyArr);

            $caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':'];

            if($carrierEX   <= 1 &&  $wmEX        <= 1 &&
               $minimunEX   <= 1 &&  $currencyEX  <= 1 ){

                $resultadoPortOri = PrvHarbor::get_harbor($originEX[0]);
                if($resultadoPortOri['boolean']){
                    $originB = true;    
                }
                $originV  = $resultadoPortOri['puerto'];

                $resultadoPortDes = PrvHarbor::get_harbor($destinyEX[0]);
                if($resultadoPortDes['boolean']){
                    $destinyB = true;    
                }
                $destinationV  = $resultadoPortDes['puerto'];


                //---------------- Carrier ------------------------------------------------------------------

                $carrierArr      = PrvCarrier::get_carrier($carrierArr[0]);
                $carriExitBol    = $carrierArr['boolean'];
                $carrierVal      = $carrierArr['carrier'];

                //---------------- W/M ------------------------------------------------------------------

                if(empty($wmArr[0]) != true || (int)$wmArr[0] == 0){
                    $wmExiBol = true;
                    $wmVal    = floatval($wmArr[0]);
                }

                //----------------- 40' -----------------------------------------------------------------

                if(empty($minimunArr[0]) != true || (int)$minimunArr[0] == 0){
                    $minimunExiBol = true;
                    $minimunVal    = floatval($minimunArr[0]);
                }

                if($wmVal == 0 && $minimunVal == 0){
                    $values = false;
                }
                //----------------- Currency -----------------------------------------------------------

                $currenct = Currency::where('alphacode','=',$currencyArr[0])->orWhere('id','=',$currencyArr[0])->first();

                if(empty($currenct->id) != true){
                    $curreExitBol = true;
                    $currencyVal =  $currenct->id;
                }

                //----------------- Currency -----------------------------------------------------------

                $scheduleT = ScheduleType::where('name','=',$scheduleTArr[0])->first();

                if(empty($scheduleT->id) != true || $scheduleTArr[0] == null){
                    $scheduleTBol = true;
                    if($scheduleTArr[0] != null){
                        $scheduleTVal =  $scheduleT->id;
                    } else {
                        $scheduleTVal = null;
                    }
                }

                // Validacion de los datos en buen estado ----------------------------------------------
                if($originB == true && $destinyB == true &&
                   $wmExiBol   == true && $minimunExiBol    == true &&
                   $scheduleTBol == true && $carriExitBol   == true &&
                   $curreExitBol   == true){
                    $collecciont = '';
                    if($values){
                        $collecciont = RateLcl::create([
                            'origin_port'       => $originV,
                            'destiny_port'      => $destinationV,
                            'carrier_id'        => $carrierVal,                            
                            'contractlcl_id'    => $id,
                            'uom'               => $wmVal,
                            'minimum'           => $minimunVal,
                            'currency_id'       => $currencyVal,
                            'schedule_type_id'  => $scheduleTVal,
                            'transit_time'      => (int)$failrate['transit_time'],
                            'via'               => $failrate['via']
                        ]);
                    }
                    $failrate->forceDelete();

                } 
            }
        }

        $contractData = ContractLcl::find($id);
        $usersNotifiques = User::where('type','=','admin')->get();
        foreach($usersNotifiques as $userNotifique){
            $message = 'The Rates was Reprocessed. Contract: ' . $contractData->number ;
            $userNotifique->notify(new N_general($userNotifique,$message));
        }
    }
}
