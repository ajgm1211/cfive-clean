<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FailRate;
use App\Rate;
use App\Harbor;

class ImportationController extends Controller
{
    public function ReprocesarRates($id){

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

                $originB  = false;
                $destinyB = false;

                $originEX   = explode('_',$failrate->origin_port);
                $destinyEX  = explode('_',$failrate->destiny_port);
                $carrierEX  = count(explode('_',$failrate->carrier_id));
                $twuentyEX  = count(explode('_',$failrate->twuenty));
                $fortyEX    = count(explode('_',$failrate->forty));
                $fortyhcEX  = count(explode('_',$failrate->fortyhc));
                $currencyEX = count(explode('_',$failrate->currency_id));

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

                    // Validacion de los datos en buen estado ------------------------------------------------------------------------
                    if($originB == true && $destinyB == true){
                        $collecciont = collect([]);
                        
                        $collecciont = [
                            'origin_port'  => $originV,
                            'destiny_port' => $destinationV,
                        ];
                        dd($collecciont);

                    } else {
                        dd('No existe alguno de los puertos');
                    }
                }
            }

        } else {
            //se ejecuta un en un job
        }
    }
}
