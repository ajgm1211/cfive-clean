<?php
//app/Helpers/Envato/User.php
namespace App\Helpers;

use App\Harbor;
use App\Country;

class Helpharbors {
    /**
     * @param int $user_id User-id
     *
     * @return string
     */
    public static function get_harbor($puerto) {

        $portExiBol = false;
        $sin_via = explode(' via ',$puerto);

        $caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':'];


        $portResul = str_replace($caracteres,'',trim(strtolower($sin_via[0])));

        if(empty($portResul) != true){

            $portExits = Harbor::where('varation->type','like','%'.$portResul.'%')
                ->get();

            if(count($portExits) > 1){
                $puerto = strtolower(trim($puerto));

                foreach($portExits as $multiples){

                    $jsonorigen = json_decode($multiples['varation']);

                    foreach($jsonorigen->type as $parameter){

                        if (strlen($puerto) == strlen($parameter)){
                            if(strcmp($puerto,$parameter) == 0){
                                $portVal = $multiples->id;
                                $portExiBol = true;
                                break;
                            }
                        }
                    }
                }

                if($portExiBol == false){
                    $portVal = $puerto.'_E_E';
                }

            } else{

                if(count($portExits) == 1){
                    $portExiBol = true;
                    foreach($portExits as $portRc){
                        $portVal = $portRc['id'];
                    }
                } else{
                    $portVal = $puerto.'_E_E';
                }

            }

        } else{
            $portVal    = '_E_E';
            $portExiBol = false;
        }

        $data = ['puerto' => $portVal, 'boolean' => $portExiBol];

        return ($data);
    }

    public static function get_country($country) {

        $countryExiBol = false;
        $sin_via = explode(' via ',$country);

        $caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':'];


        $countryResul = str_replace($caracteres,'',trim(strtolower($sin_via[0])));

        if(empty($countryResul) != true){

            $countryExits = Country::where('variation->type','like','%'.$countryResul.'%')
                ->get();

            if(count($countryExits) > 1){
                $country = strtolower(trim($country));

                foreach($countryExits as $multiples){

                    $jsonorigen = json_decode($multiples['variation']);

                    foreach($jsonorigen->type as $parameter){

                        if (strlen($country) == strlen($parameter)){
                            if(strcmp($country,$parameter) == 0){
                                $countryVal = $multiples->id;
                                $countryExiBol = true;
                                break;
                            }
                        }
                    }
                }

                if($countryExiBol == false){
                    $countryVal = $country.'_E_E';
                }

            } else{

                if(count($countryExits) == 1){
                    $countryExiBol = true;
                    foreach($countryExits as $portRc){
                        $countryVal = $portRc['id'];
                    }
                } else{
                    $countryVal = $country.'_E_E';
                }

            }

        } else{
            $countryVal    = '_E_E';
            $countryExiBol = false;
        }

        $data = ['country' => $countryVal, 'boolean' => $countryExiBol];

        return ($data);
    }

    public static function get_harbor_simple($puerto){
        $data = null;
        $resp =  false;
        $place_val     = Harbor::where('varation->type','like','%'.strtolower($puerto).'%')->get();
        if(count($place_val) == 1 ){
            $resp = true;
            $data = $place_val[0]->id;
        } elseif(count($place_val) == 0){
            $data = $puerto.'(Error)';
        } elseif(count($place_val) > 1){
            $data = $puerto.' (Error) ['.$place_val->implode('id', ', ').']';
        }
        return ['puerto' => $data,'boolean' => $resp];
    }
}
