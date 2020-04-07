<?php
//app/Helpers/Envato/User.php
namespace App\Helpers;

use App\Rate;
use App\Currency;

class HelperAll {
  /**
     * @param int $user_id User-id
     * 
     * @return string
     */
  public static function addOptionSelect($dataAll,$id,$name) {
    $data	= [null=>'Please Select'];
    foreach($dataAll as $dataRun){
      $data[$dataRun[$id]] = $dataRun[$name];
    }
    return $data;
  }

  public static function currencyJoin($statusCurrency,$currency_bol,$val_ps,$curr_ps){
    $data = null;
    if($statusCurrency == 2){ // Valores junto con la moneda
      if($currency_bol == true){
        $currencyObj  = Currency::find($curr_ps);
        $currency_val = $currencyObj->alphacode;
      } else {
        $currency_val = $curr_ps;
      }
      $data = $val_ps.' '.$currency_val;      
    } else { // Moneda especificada en el Select o columna
      $data = $val_ps;    
    }

    return $data;
  }
}