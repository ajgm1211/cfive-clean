<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\FailRate;
use App\Rate;
use App\Harbor;
use App\Carrier;
use App\Currency;
use App\Contract;
use App\User;
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

                $originEX       = explode('_',$failrate->origin_port);
                $destinyEX      = explode('_',$failrate->destiny_port);
                $carrierArr     = explode('_',$failrate->carrier_id);
                $twentyArr      = explode('_',$failrate->twuenty);
                $fortyArr       = explode('_',$failrate->forty);
                $fortyhcArr     = explode('_',$failrate->fortyhc);
                $fortynorArr    = explode('_',$failrate->fortynor);
                $fortyfiveArr   = explode('_',$failrate->fortyfive);
                $currencyArr    = explode('_',$failrate->currency_id);


                $carrierEX     = count($carrierArr);
                $twuentyEX     = count($twentyArr);
                $fortyEX       = count($fortyArr);
                $fortyhcEX     = count($fortyhcArr);
                $currencyEX    = count($currencyArr);

                if($carrierEX   <= 1 &&  $twuentyEX   <= 1 &&
                   $fortyEX     <= 1 &&  $fortyhcEX   <= 1 &&
                   $currencyEX  <= 1 ){
                    $caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':'];
                    // Origen Y Destino ------------------------------------------------------------------------

                    $originResul = str_replace($caracteres,'',strtolower($originEX[0]));
                    $originExits = Harbor::where('varation->type','like','%'.$originResul.'%')
                        ->get();
                    if(count($originExits) == 1){
                        $originB = true;
                        foreach($originExits as $originRc){
                            $originV = $originRc['id'];
                        }
                    }

                    $destinResul = str_replace($caracteres,'',strtolower($destinyEX[0]));
                    $destinationExits = Harbor::where('varation->type','like','%'.$destinResul.'%')
                        ->get();
                    if(count($destinationExits) == 1){
                        $destinyB = true;
                        foreach($destinationExits as $destinationRc){
                            $destinationV = $destinationRc['id'];
                            // dd($destinationV);
                        }
                    }

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
                        $twentyVal   = (int)$twentyArr[0];
                    }

                    //----------------- 40' -----------------------------------------------------------------

                    if(empty($fortyArr[0]) != true || (int)$fortyArr[0] == 0){
                        $fortyExiBol = true;
                        $fortyVal   = (int)$fortyArr[0];
                    }

                    //----------------- 40'HC --------------------------------------------------------------

                    if(empty($fortyhcArr[0]) != true || (int)$fortyhcArr[0] == 0){
                        $fortyhcExiBol = true;
                        $fortyhcVal   = (int)$fortyhcArr[0];
                    }

                    //----------------- 40'NOR -------------------------------------------------------------

                    if(empty($fortynorArr[0]) != true || (int)$fortynorArr[0] == 0){
                        $fortynorExiBol = true;
                        $fortynorVal   = (int)$fortynorArr[0];
                    }

                    //----------------- 45' ----------------------------------------------------------------

                    if(empty($fortyfiveArr[0]) != true || (int)$fortyfiveArr[0] == 0){
                        $fortyfiveExiBol = true;
                        $fortyfiveVal   = (int)$fortyfiveArr[0];
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

                    // Validacion de los datos en buen estado ------------------------------------------------------------------------
                    if($originB == true && $destinyB == true &&
                       $twentyExiBol   == true && $fortyExiBol    == true &&
                       $fortyhcExiBol  == true && $fortynorExiBol == true &&
                       $fortyfiveExiBol == true && $values        == true &&
                       $carriExitBol   == true && $curreExitBol   == true){
                        $collecciont = '';

                        $collecciont = Rate::create([
                            'origin_port'   => $originV,
                            'destiny_port'  => $destinationV,
                            'carrier_id'    => $carrierVal,                            
                            'contract_id'   => $id,
                            'twuenty'       => $twentyVal,
                            'forty'         => $fortyVal,
                            'fortyhc'       => $fortyhcVal,
                            'fortynor'      => $fortynorVal,
                            'fortyfive'     => $fortyfiveVal,
                            'currency_id'   => $currencyVal
                        ]);
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
