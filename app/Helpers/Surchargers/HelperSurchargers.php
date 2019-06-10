<?php
//app/Helpers/Envato/User.php
namespace App\Helpers\Surchargers;

use App\Harbor;
use App\LocalCharge;
use Illuminate\Support\Facades\DB;

class HelperSurchargers {
   /**
     * @param int $user_id User-id
     * 
     * @return string
     */
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
}