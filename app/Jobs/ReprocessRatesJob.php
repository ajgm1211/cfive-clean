<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Rate;
use App\User;
use PrvHarbor;
use App\Harbor;
use App\Carrier;
use App\Currency;
use App\Contract;
use App\FailRate;
use App\ScheduleType;
use App\Notifications\N_general;

class ReprocessRatesJob implements ShouldQueue
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

        $failrates = FailRate::where('contract_id','=',$id)->get();
        foreach($failrates as $failrate){

                $carrierEX          = '';
                $twuentyEX          = '';
                $fortyEX            = '';
                $fortyhcEX          = '';
                $currencyEX         = '';
                $originResul        = '';
                $originExits        = '';
                $originV            = '';
                $destinResul        = '';
                $destinationExits   = '';
                $destinationV       = '';
                $originEX           = '';
                $destinyEX          = '';
                $twentyVal          = '';
                $fortyVal           = '';
                $fortyhcVal         = '';
                $carrierVal         = '';
                $carrierArr         = '';
                $twentyArr          = '';
                $fortyArr           = '';
                $fortyhcArr         = '';
                $currencyArr        = '';
                $currencyVal        = '';
                $currenct           = '';
                $fortynorVal        = '';
                $fortyfiveVal       = '';
                $scheduleTVal       = null;

                $curreExitBol       = false;
                $originB            = false;
                $destinyB           = false;
                $twentyExiBol       = false;
                $fortyExiBol        = false;
                $fortyhcExiBol      = false;
                $values             = true;
                $carriExitBol       = false;
                $fortynorExiBol     = false;
                $fortyfiveExiBol    = false;
                $scheduleTBol       = false;

                $originEX       = explode('_',$failrate->origin_port);
                $destinyEX      = explode('_',$failrate->destiny_port);
                $carrierArr     = explode('_',$failrate->carrier_id);
                $twentyArr      = explode('_',$failrate->twuenty);
                $fortyArr       = explode('_',$failrate->forty);
                $fortyhcArr     = explode('_',$failrate->fortyhc);
                $fortynorArr    = explode('_',$failrate->fortynor);
                $fortyfiveArr   = explode('_',$failrate->fortyfive);
                $currencyArr    = explode('_',$failrate->currency_id);
                $scheduleTArr   = explode('_',$failrate->schedule_type);


                $carrierEX     = count($carrierArr);
                $twuentyEX     = count($twentyArr);
                $fortyEX       = count($fortyArr);
                $fortyhcEX     = count($fortyhcArr);
                $currencyEX    = count($currencyArr);

                $caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':'];

                if($carrierEX   <= 1 &&  $twuentyEX   <= 1 &&
                   $fortyEX     <= 1 &&  $fortyhcEX   <= 1 &&
                   $currencyEX  <= 1 ){

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

                    $carrierResul = str_replace($caracteres,'',$carrierArr[0]);
                    $carrier = Carrier::where('name','=',$carrierResul)->first();
                    if(empty($carrier->id) != true){
                        $carriExitBol = true;
                        $carrierVal = $carrier->id;
                    }

                    //---------------- 20' ------------------------------------------------------------------

                    if(empty($twentyArr[0]) != true || (int)$twentyArr[0] == 0){
                        $twentyExiBol = true;
                        $twentyVal   = floatval($twentyArr[0]);
                    }

                    //----------------- 40' -----------------------------------------------------------------

                    if(empty($fortyArr[0]) != true || (int)$fortyArr[0] == 0){
                        $fortyExiBol = true;
                        $fortyVal   = floatval($fortyArr[0]);
                    }

                    //----------------- 40'HC --------------------------------------------------------------

                    if(empty($fortyhcArr[0]) != true || (int)$fortyhcArr[0] == 0){
                        $fortyhcExiBol = true;
                        $fortyhcVal   = floatval($fortyhcArr[0]);
                    }

                    //----------------- 40'NOR -------------------------------------------------------------

                    if(empty($fortynorArr[0]) != true || (int)$fortynorArr[0] == 0){
                        $fortynorExiBol = true;
                        $fortynorVal   = floatval($fortynorArr[0]);
                    }

                    //----------------- 45' ----------------------------------------------------------------

                    if(empty($fortyfiveArr[0]) != true || (int)$fortyfiveArr[0] == 0){
                        $fortyfiveExiBol = true;
                        $fortyfiveVal   = floatval($fortyfiveArr[0]);
                    }

                    if($twentyVal == 0
                       && $fortyVal == 0
                       && $fortyhcVal == 0
                       && $fortynorVal == 0
                       && $fortyfiveVal == 0){
                        $values = false;
                    }
                    //----------------- Currency -----------------------------------------------------------

                    $currenct = Currency::where('alphacode','=',$currencyArr[0])->orWhere('id','=',$currencyArr[0])->first();

                    if(empty($currenct->id) != true){
                        $curreExitBol = true;
                        $currencyVal =  $currenct->id;
                    }

                    $scheduleT = ScheduleType::where('name','=',$scheduleTArr[0])->first();

                    if(empty($scheduleT->id) != true || $scheduleTArr[0] == null){
                        $scheduleTBol = true;
                        if($scheduleTArr[0] != null){
                            $scheduleTVal =  $scheduleT->id;
                        } else {
                            $scheduleTVal = null;
                        }
                    }

                    $array = [
                        'ori' => $originB,
                        'des' => $destinyB,
                        '20' => $twentyExiBol,
                        '40' => $fortyExiBol,
                        '40h' => $fortyhcExiBol,
                        '40n' => $fortynorExiBol,
                        '45' => $fortyfiveExiBol,
                        'val' => $values,
                        'sch' => $scheduleTBol,
                        'car' => $carriExitBol,
                        'curr' => $curreExitBol
                    ];
                    //dd($array);


                    // Validacion de los datos en buen estado ------------------------------------------------------------------------
                    if($originB == true && $destinyB == true &&
                       $twentyExiBol   == true && $fortyExiBol    == true &&
                       $fortyhcExiBol  == true && $fortynorExiBol == true &&
                       $fortyfiveExiBol == true && $values        == true &&
                       $scheduleTBol == true && $carriExitBol   == true && 
                       $curreExitBol   == true){
                        $collecciont = '';
                        $exists = null;
                        $exists = Rate::where('origin_port',$originV)
                            ->where('destiny_port',$destinationV)
                            ->where('carrier_id',$carrierVal)
                            ->where('contract_id',$id)
                            ->where('twuenty',$twentyVal)
                            ->where('forty',$fortyVal)
                            ->where('fortyhc',$fortyhcVal)
                            ->where('fortynor',$fortynorVal)
                            ->where('fortyfive',$fortyfiveVal)
                            ->where('currency_id',$currencyVal)
                            ->where('schedule_type_id',$scheduleTVal)
                            ->where('transit_time',(int)$failrate['transit_time'])
                            ->where('via',$failrate['via'])
                            ->first();
                        if(count($exists) == 0){
                            $collecciont = Rate::create([
                                'origin_port'       => $originV,
                                'destiny_port'      => $destinationV,
                                'carrier_id'        => $carrierVal,                            
                                'contract_id'       => $id,
                                'twuenty'           => $twentyVal,
                                'forty'             => $fortyVal,
                                'fortyhc'           => $fortyhcVal,
                                'fortynor'          => $fortynorVal,
                                'fortyfive'         => $fortyfiveVal,
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

        $contractData = Contract::find($id);
        $usersNotifiques = User::where('type','=','admin')->get();
        foreach($usersNotifiques as $userNotifique){
            $message = 'The Rates was Reprocessed. Contract: ' . $contractData->number ;
            $userNotifique->notify(new N_general($userNotifique,$message));
        }
    }
}
