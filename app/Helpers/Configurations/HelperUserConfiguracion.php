<?php

namespace App\Helpers\Configurations;

use App\User;
use App\UserConfiguration;

class HelperUserConfiguracion {

    public static function arrays(){

        $json['notifications'] = [
            'request-importation-fcl'   => true,
            'request-importation-lcl'   => true,
            'request-importation-gcfcl' => true
        ];

        $json['colors'] = [
            'count'                     => 1,
            'color-nav'                 => true
        ];

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

            $data = $conf->userConfiguration->paramerters;
        }

        return json_decode($data,true);
    }

    public static function syncronize_json($user_id){
        $json = json_decode(self::arrays(),true);
        foreach($json as $arreglo => $keys){
            foreach($keys as $key => $all){
                $userConf = UserConfiguration::where('user_id',$user_id)->where('paramerters->'.$arreglo,'like','%'.$key.'%')->first();
                if(count($userConf) == 0){
                    $userConf_up = UserConfiguration::find($user_id);
                    $josn_user = json_decode($userConf_up->paramerters,true);
                    foreach($josn_user as $arreglo_u => $keys_u){
                        foreach($keys_u as $key_u => $all_u){
                            dd($josn_user['notifications']);
                        }
                    }
                } else{
                    dd('existe');
                }
            }

        }
        dd($userConf);
        return $userConf;
    }

}