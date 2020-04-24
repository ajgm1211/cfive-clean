<?php
//app/Helpers/Envato/User.php
namespace App\Helpers\Rates;

use App\Rate;
use App\Contract;
use App\Container;
use App\GroupContainer;
use Illuminate\Support\Facades\DB;

class HelperRates {
    /**
     * @param int $user_id User-id
     * 
     * @return string
     */
    public static function get_rates($id) {
        $contract           = Contract::find($id);
        $equiment_id        = $contract->gp_container_id;
        $equiments          = GroupContainer::with('containers')->find($equiment_id);
        $columns_rt_ident   = [];
        if($equiment_id == 1){
            $contenedores_rt = Container::where('gp_container_id',$equiment_id)->where('options->column',true)->get();
            foreach($contenedores_rt as $conten_rt){
                $conten_rt->options = json_decode($conten_rt->options);
                $columns_rt_ident[$conten_rt->code] = $conten_rt->options->column_name;
            }
        }

        //$rates = Rate::with('carrier','contract','port_origin','port_destiny','currency')->where('contract_id','=',$id)->get();
        $rates = DB::select('call  proc_rates_fcl('.$id.')');
        $ratescol = collect([]);
        foreach($rates as $rate){
            $containers = null;
            $containers = json_decode($rate->containers,true);
            $containers = json_decode($containers,true);
           // dd($containers,$rate->containers);
            $colec = ['id'              =>  $rate->id,
                      'contract_id'     =>  $id,            //
                      'origin'          =>  $rate->origin,    //
                      'destiny'         =>  $rate->destiny,    //
                      'carrier'         =>  $rate->carrier,   //
//                      'twuenty'         =>  $rate->twuenty, //    
//                      'forty'           =>  $rate->forty,   //  
//                      'fortyhc'         =>  $rate->fortyhc, //
//                      'fortynor'        =>  $rate->fortynor, //
//                      'fortyfive'       =>  $rate->fortyfive, //
                      'currency'        =>  $rate->currency,  //
                      'operation'       =>  '2'
                     ];
            if($equiment_id == 1){
                foreach($equiments->containers as $containersEq){
                    if(strnatcasecmp($columns_rt_ident[$containersEq->code],'twuenty') == 0){
                        $colec['C'.$containersEq->code] = $rate->twuenty;
                    }else if(strnatcasecmp($columns_rt_ident[$containersEq->code],'forty') == 0){
                        $colec['C'.$containersEq->code] = $rate->forty;
                    } else if(strnatcasecmp($columns_rt_ident[$containersEq->code],'fortyhc') == 0){
                        $colec['C'.$containersEq->code] = $rate->fortyhc;
                    } else if(strnatcasecmp($columns_rt_ident[$containersEq->code],'fortynor') == 0){
                        $colec['C'.$containersEq->code] = $rate->fortynor;
                    } else if(strnatcasecmp($columns_rt_ident[$containersEq->code],'fortyfive') == 0){
                        $colec['C'.$containersEq->code] = $rate->fortyfive;
                    }
                }
            } else {
                foreach($equiments->containers as $containersEq){                    
                    if(array_key_exists('C'.$containersEq->code,$containers)){
                        $colec['C'.$containersEq->code] = $containers['C'.$containersEq->code];
                    } else{
                        $colec['C'.$containersEq->code] = 0;          
                    }
                }
            }
            $ratescol->push($colec);
        }
        return($ratescol);
    }
}