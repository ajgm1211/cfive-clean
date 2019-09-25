<?php

namespace App\Helpers\Configurations;

use App\User;
use App\UserConfiguration;

class HelperUserConfiguracion {

    public static function arrays(){

        $json['notifications'] = [
            'request-importation-fcl'   => true,
            'request-importation-lcl'   => true,
            'request-importation-gcfcl' => true,
            'request-importation-gclcl' => true
        ];

        /*$json['colors'] = [
            'color-graper'  => true,
            'color-nav'     => true
        ];*/

        $json = json_encode($json);
        return $json;
    }

    public static function allData($user_id){
        $user = User::find($user_id);
        $user->load('userConfiguration');
        $json = self::arrays();
        if(count($user->userConfiguration) > 0){
            $data = self::syncronize_json($user_id);
        } else {
            $conf = new UserConfiguration();
            $conf->user_id      =  $user->id;
            $conf->paramerters  =  $json;
            $conf->save();

            $data = $conf->paramerters;
        }

        return json_decode($data,true);
    }

    public static function syncronize_json($user_id){
        $json = json_decode(self::arrays(),true);
        $user_found = null;
        foreach($json as $arreglo => $keys){
            foreach($keys as $key => $all){
                $userConf = UserConfiguration::where('user_id',$user_id)->where('paramerters->'.$arreglo,'like','%'.$key.'%')->first();
                if(count($userConf) == 0){

                    $userConf_up = UserConfiguration::where('user_id',$user_id)->first();
                    $josn_user = json_decode($userConf_up->paramerters,true);
                    foreach($josn_user as $arreglo_u => $keys_u){

                        if(array_key_exists($arreglo,$josn_user)){
                            foreach($keys_u as $key_u => $all_u){
                                if(array_key_exists($key,$josn_user[$arreglo]) != true){
                                    // Se crea claves hijas si faltan
                                    //dd($key); request-importation-fcl
                                    //$json[$arreglo][$key]; true
                                    $josn_user[$arreglo][$key] = $json[$arreglo][$key];
                                    $userConf_up->paramerters = json_encode($josn_user);
                                    $userConf_up->update();
                                }
                            }
                        } else {
                            // se crea clave padre si no existe
                            $josn_user[$arreglo] = $json[$arreglo];
                            $userConf_up->paramerters = json_encode($josn_user);
                            $userConf_up->update();
                        }
                    $user_found = $userConf_up;
                    }

                } else{
                    $user_found = $userConf;
                }
            }

        }
        return $user_found->paramerters;
    }

}