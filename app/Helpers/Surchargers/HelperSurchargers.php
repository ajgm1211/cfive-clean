<?php
//app/Helpers/Envato/User.php
namespace App\Helpers\Surchargers;

use App\Harbor;
use App\Surcharge;
use App\LocalCharge;
use Illuminate\Support\Facades\DB;

class HelperSurchargers {
    /**
     * @param int $user_id User-id
     * 
     * @return string
     */

    //Recargos LOCALCHARGE
    public static function get_surchargers($id) {
        $goodsurcharges     = DB::select('call proc_localchar('.$id.')');;
        $surchargecollection = collect([]);
        foreach($goodsurcharges as $surcharge){
            $origin             = '';
            $destiny            = '';
            $surchargeName      = '';
            $typedestiny        = '';
            $calculationtype    = '';
            $ammount            = '';
            $carrier            = '';
            $currency           = '';

            // Origen -----------------
            if(empty($surcharge->port_orig) != true){
                $origin = str_replace(',',' | ',$surcharge->port_orig);
            } else if(empty($surcharge->country_orig) != true){
                $origin = str_replace(',',' | ',$surcharge->country_orig);  
            }

            // Destino -----------------
            if(empty($surcharge->port_dest	) != true){
                $destiny = str_replace(',',' | ',$surcharge->port_dest);
            } else if(empty($surcharge->country_orig) != true){
                $destiny = str_replace(',',' | ',$surcharge->country_dest);  
            }

            // Carrier ----------------
            $carrier = str_replace(',',' | ',$surcharge->carrier);
            $surchargeName   = $surcharge->surcharge;
            $typedestiny     = $surcharge->changetype;
            $calculationtype = $surcharge->calculation_type;
            $ammount         = $surcharge->ammount;
            $currency        = $surcharge->currency;
            $arreglo = [
                'id'                => $surcharge->id,
                'surchargelb'       => $surchargeName,
                'origin_portLb'     => $origin,
                'destiny_portLb'    => $destiny,
                'carrierlb'         => $carrier,
                'typedestinylb'     => $typedestiny,
                'ammount'           => $ammount,
                'calculationtypelb' => $calculationtype,
                'currencylb'        => $currency,
                'operation'         => 2
            ];

            $surchargecollection->push($arreglo);
        }
        return($surchargecollection);
    }

    //Recargos SURCHARGE
    public static function get_single_surcharger($name){
        $data = null;
        $count= 0;
        $resp = false;
        $find = false;
        $posible_array = [];
        $surchargersFineds = Surcharge::where('variation->type','like','%'.trim(strtolower($name)).'%')
            ->where('company_user_id',null)
            ->get();

        if(count($surchargersFineds) == 1 ){
            $resp = true;
            $data = $surchargersFineds[0]->id;
            $count = 1;
        } elseif(count($surchargersFineds) > 1){
            foreach($surchargersFineds as  $surchargersFined){
                $array_varation = json_decode($surchargersFined->variation,true);                
                if(in_array(strtolower($name),$array_varation['type'])){
                    $find = true;
                    $resp = true;
                    //$data = $surchargersFined->id;
                    array_push($posible_array,$surchargersFined->id);
                    $count = $count + 1;
                    //break;
                }                
            }
            if($find == true){
                if(count($posible_array) == 1){
                    $data = $posible_array[0];
                } else if(count($posible_array) > 1){
                    $resp = false;
                    $posible_array = collect($posible_array);
                    $data = $name.' (Error) ['.$posible_array->implode(', ').']';
                }
            }
            if(!$find){
                $resp = false;
                $data = $name.' (Error) ['.$surchargersFineds->implode('id', ', ').']';
                $count = count($surchargersFineds);
            }
        }
        return ['data' => $data,'boolean' => $resp,'count'=> $count];


    }
}