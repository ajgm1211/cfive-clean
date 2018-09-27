<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FailRate;
use App\Rate;
use App\Harbor;
use App\Carrier;
use App\Currency;
use App\Contract;
use App\Surcharge;
use App\FailSurCharge;
use App\User;
use App\Jobs\ReprocessRatesJob;
use App\Notifications\N_general;

class ImportationController extends Controller
{

    public function ReprocesarRates(Request $request, $id){

        $countfailrates = FailRate::where('contract_id','=',$id)->count();
        if($countfailrates <= 150){
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

                $curreExitBol       = false;
                $originB            = false;
                $destinyB           = false;
                $twentyExiBol       = false;
                $fortyExiBol        = false;
                $fortyhcExiBol      = false;
                $values             = true;
                $carriExitBol       = false;

                $originEX    = explode('_',$failrate->origin_port);
                $destinyEX   = explode('_',$failrate->destiny_port);
                $carrierArr  = explode('_',$failrate->carrier_id);
                $twentyArr   = explode('_',$failrate->twuenty);
                $fortyArr    = explode('_',$failrate->forty);
                $fortyhcArr  = explode('_',$failrate->fortyhc);
                $currencyArr = explode('_',$failrate->currency_id);


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

                    if($twentyVal == 0
                       && $fortyVal == 0
                       && $fortyhcVal == 0){
                        $values = false;
                    }
                    //----------------- Currency -----------------------------------------------------------

                    $currenct = Currency::where('alphacode','=',$currencyArr[0])->first();

                    if(empty($currenct->id) != true){
                        $curreExitBol = true;
                        $currencyVal =  $currenct->id;
                    }

                    // Validacion de los datos en buen estado ------------------------------------------------------------------------
                    if($originB == true && $destinyB == true &&
                       $twentyExiBol   == true && $fortyExiBol  == true &&
                       $fortyhcExiBol  == true && $values       == true &&
                       $carriExitBol   == true && $curreExitBol == true){
                        $collecciont = '';

                        $collecciont = Rate::create([
                            'origin_port'   => $originV,
                            'destiny_port'  => $destinationV,
                            'carrier_id'    => $carrierVal,                            
                            'contract_id'   => $id,
                            'twuenty'       => $twentyVal,
                            'forty'         => $fortyVal,
                            'fortyhc'       => $fortyhcVal,
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

        } else {
            ReprocessRatesJob::dispatch($id);
            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.content', 'The rates are reprocessing in the background');
            return redirect()->route('Failed.Rates.Developer.For.Contracts',[$id,'1']);
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'The rates are being reprocessed');
        $countfailratesNew = FailRate::where('contract_id','=',$id)->count();
        if($countfailratesNew > 0){
            return redirect()->route('Failed.Rates.Developer.For.Contracts',[$id,'1']);
        }else{
            return redirect()->route('Failed.Rates.Developer.For.Contracts',[$id,'0']);
        }
    }

    public function ReprocesarSurchargers(Request $request, $id){
        $countfailsurchargers = FailSurCharge::where('contract_id','=',$id)->count();
        if($countfailsurchargers <= 150){
            $failsurchargers = FailSurCharge::where('contract_id','=',$id)->get();
            foreach($failsurchargers as $FailSurchager){

                $surchargerEX       = '';
                $origenEX           = '';
                $destinyEX          = '';
                $typedestinyEX      = '';
                $calculationtypeEX  = '';
                $ammountEX          = '';
                $currencyEX         = '';
                $carrierEX          = '';
                $originResul        = '';
                $originExits        = '';
                $originV            = '';
                $destinResul        = '';
                $destinationExits   = '';
                $destinationV       = '';
                $surchargerV        = '';

                $originB        = false;
                $destinyB       = false;
                $surcharB       = false;


                $surchargerEX       = explode('_',$FailSurchager['surcharge_id']);
                $originEX           = explode('_',$FailSurchager['port_orig']);
                $destinyEX          = explode('_',$FailSurchager['port_dest']);
                $typedestinyEX      = explode('_',$FailSurchager['typedestiny_id']);
                $calculationtypeEX  = explode('_',$FailSurchager['calculationtype_id']);
                $ammountEX          = explode('_',$FailSurchager['ammount']);
                $currencyEX         = explode('_',$FailSurchager['currency_id']);
                $carrierEX          = explode('_',$FailSurchager['carrier_id']);

                if(count($surchargerEX) == 1     && count($typedestinyEX) == 1
                   && count($typedestinyEX) == 1 && count($calculationtypeEX) == 1
                   && count($ammountEX) == 1     && count($currencyEX) == 1
                   && count($carrierEX) == 1){

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
                    //          ----------------------------------------------------------------------------------

                    $surchargerV = Surcharge::where('name','=',$surchargerEX[0])->first();
                    if(count($surchargerV) == 1){
                        $surcharB = true;
                        $surchargerV = $surchargerV['id'];
                    }
                }

            }
        } else {

        }
    }

}
