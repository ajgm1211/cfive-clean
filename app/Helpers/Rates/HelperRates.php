<?php
//app/Helpers/Envato/User.php
namespace App\Helpers\Rates;

use App\Rate;

class HelperRates {
   /**
     * @param int $user_id User-id
     * 
     * @return string
     */
   public static function get_rates($id) {
      $rates = Rate::with('carrier','contract','port_origin','port_destiny','currency')->where('contract_id','=',$id)->get();
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
                   'twuenty'         =>  $rate->twuenty, //    
                   'forty'           =>  $rate->forty,   //  
                   'fortyhc'         =>  $rate->fortyhc, //
                   'fortynor'        =>  $rate->fortynor, //
                   'fortyfive'       =>  $rate->fortyfive, //
                   'currency_id'     =>  $currencyRate,  //
                   'operation'       =>  '2'
                  ];
         $ratescol->push($colec);
      }
      return($ratescol);
   }
}