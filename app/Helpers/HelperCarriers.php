<?php
//app/Helpers/Envato/User.php
namespace App\Helpers;

use App\Carrier;

class HelperCarriers {

    public static function get_carrier($carrier){
        $carrier        = trim($carrier);
        $multiples      = [];
        $boolean        = false;
        $data_carrier   = $carrier.'_E_E';
        $caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':'];
        $carrier = str_replace($caracteres,'',$carrier);
        if(empty($carrier) != true){
            $carriersExists = Carrier::where('varation->type','like','%'.strtolower($carrier).'%')->get();
            if(count($carriersExists) > 1){
                foreach($carriersExists as $carrier){
                    array_push($multiples,$carrier->name);
                }
            } else if(count($carriersExists) == 1){
                foreach($carriersExists as $carrier){
                    $boolean        = true;
                    $data_carrier   = $carrier->id;
                    array_push($multiples,$carrier->name);
                }
            }
        }
        $arreglo = ['carrier' => $data_carrier, 'boolean' => $boolean, 'relation' => $multiples];
        return $arreglo;
    }

}