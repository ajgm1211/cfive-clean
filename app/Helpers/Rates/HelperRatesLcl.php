<?php
//app/Helpers/Envato/User.php
namespace App\Helpers\Rates;

use App\RateLcl;

class HelperRatesLcl {
   /**
     * @param int $user_id User-id
     * 
     * @return string
     */
   public static function get_rates($id) {
      $rates = RateLcl::with('carrier','contract','port_origin','port_destiny','currency')->where('contractlcl_id','=',$id)->get();
      $ratescol = collect([]);
      foreach($rates as $rate){
         $originRate     = '';
         $detinyRate     = '';
         $carrierRate    = '';
         $currencyRate   = '';

         $originRate     = $rate['port_origin']['name'];
         $detinyRate     = $rate['port_destiny']['name'];
         $carrierRate    = $rate['carrier']['name'];
         $currencyRate   = $rate->Currency->alphacode;

         $colec = ['id'              =>  $rate->id,
                   'contract_id'     =>  $id,            //
                   'origin_portLb'   =>  $originRate,    //
                   'destiny_portLb'  =>  $detinyRate,    //
                   'carrierLb'       =>  $carrierRate,   //
                   'w/m'             =>  $rate->uom, //    
                   'minimum'         =>  $rate->minimum,   //  
                   'currency_id'     =>  $currencyRate,  //
                   'operation'       =>  '2'
                  ];
         $ratescol->push($colec);
      }
      return($ratescol);
   }
}