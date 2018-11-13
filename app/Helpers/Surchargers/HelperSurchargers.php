<?php
//app/Helpers/Envato/User.php
namespace App\Helpers\Surchargers;

use App\Harbor;
use App\LocalCharge;

class HelperSurchargers {
   /**
     * @param int $user_id User-id
     * 
     * @return string
     */
   public static function get_surchargers($id) {
      $goodsurcharges     = LocalCharge::where('contract_id','=',$id)->with('currency','calculationtype','surcharge','typedestiny','localcharcarriers.carrier','localcharports.portOrig','localcharports.portDest')->get();
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
            //dd($surcharge->localcharports);
            $PuertosOrigins = $surcharge->localcharports->pluck('port_orig')->unique();
            foreach($PuertosOrigins as $PortOrigin){
               $OriginObj = Harbor::find($PortOrigin);
               $origin = $origin.$OriginObj->name.' | ';
            }
             $origin = substr($origin,0,-2);
            //dd($surcharge->localcharports);
            $PuertosDestins = $surcharge->localcharports->pluck('port_dest')->unique();
            foreach($PuertosDestins as $PortDestiny){
               $DestinyObj = Harbor::find($PortDestiny);
               $destiny = $destiny.$DestinyObj->name.' | ';
            }
             $destiny = substr($destiny,0,-2);
             
            $carrierArre = $surcharge->localcharcarriers->pluck('carrier')->pluck('name');
            foreach($carrierArre as $carrierName){
               $carrier = $carrier.$carrierName.' | ';
            }
             $carrier = substr($carrier,0,-2);

            $surchargeName   = $surcharge->surcharge['name'];
            $typedestiny     = $surcharge->typedestiny->description;
            $calculationtype = $surcharge->calculationtype->name;
            $ammount         = $surcharge->ammount;
            $currency        = $surcharge->currency->alphacode;
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
}