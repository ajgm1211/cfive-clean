<?php
namespace App\Helpers\Request;

use App\NewContractRequest;

class NRequest {
    
    public static function RequestFclBetween($dateStart,$dateEnd){
        
        $Nrequests   = NewContractRequest::whereBetween('created',[$dateStart,$dateEnd])->with('user','direction','Requestcarriers.carrier','companyuser')->get();
        $collection = collect([]);
        foreach($Nrequests as $Nrequest){
            $direction      = null;
            $carrier        = null;
            $time_elapsed   = null;

            if(empty($Nrequest->direction) == true){
                $direction = " -------- ";
            }else {
                $direction = $Nrequest->direction->name;
            }

            if(count($Nrequest->Requestcarriers) >= 1){
                $carrier = str_replace(['[',']','"'],'',$Nrequest->Requestcarriers->pluck('carrier')->pluck('name'));
            } else {
                $carrier = " -------- ";
            }

            if(empty($Nrequest->time_elapsed) != true){
                $time_elapsed = $Nrequest->time_elapsed;
            } else {
                $time_elapsed = '--------';
            }

            $arreglo = [
                'company'       => $Nrequest->companyuser->name,
                'reference'     => $Nrequest->namecontract,
                'direction'     => $direction,
                'carrier'       => $carrier,
                'validation'    => $Nrequest->validation,
                'date'          => $Nrequest->created,
                'user'          => $Nrequest->user->name,
                'username_load' => $Nrequest->username_load,
                'time_elapsed'  => $time_elapsed,
                'status'        => $Nrequest->status
            ];
            $collection->push($arreglo);
        }
        
        return $collection;
    }
}