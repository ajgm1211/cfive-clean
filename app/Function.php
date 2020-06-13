<?php

use App\CompanyUser;
use App\Currency;
use App\User;
use Illuminate\Support\Facades\Auth;


function extraerWith($patron, $cadena)
{

    $valor = explode($patron, $cadena);
    return $valor[1];
}


function setHashID()
{
    $user =  User::where('company_user_id', "=", Auth::user()->company_user_id)->with('companyUser')->first();
    if (!empty($user)) {
        $hash = $user->companyUser->hash;
    } else {
        $hash = 'cargofivepapa';
    }
    session(['hash' => $hash]);
}

function getHashID()
{
    $value = session('hash');
    return $value;
}

function setearRouteKey($key)
{


    /*$user =  User::where('company_user_id', "=",Auth::user()->company_user_id)->with('companyUser')->first();

  if(!empty($user)){
    $hash = $user->companyUser->hash;
  }else{
    $hash = 'cargofivepapa';
  }*/

    $hash = getHashID();

    $hashids = new \Hashids\Hashids($hash);

    return $hashids->encode($key);
}

function obtenerRouteKey($keyP)
{
    /*
  $user =  User::where('company_user_id', "=",Auth::user()->company_user_id)->with('companyUser')->first();
  if(!empty($user)){
    $hash =$user->companyUser->hash;
  }else{
    $hash = 'cargofivepapa';
  }*/

    $hash = getHashID();

    $hashids = new \Hashids\Hashids($hash);
    $key = $hashids->decode($keyP);
    if (isset($key[0])) {
        return $key[0];
    } else {
        return $keyP;
    }
}




function isDecimal($monto, $quote = false)
{

    $isDecimal = @Auth::user()->companyUser->decimals;

    if (isset($isDecimal) && $isDecimal) {
        if (!$quote) {
            if (is_string($monto))
                return $monto;
            else if (is_float($monto))
                return $monto;
            else
                return number_format($monto, 2, '.', '');
        }else{
            return number_format($monto, 2, '.', '');
        }
    } else {
        return round($monto);
    }
}

/**
 * ratesCurrencyFunction
 *
 * @param  mixed $id
 * @param  mixed $typeCurrency
 * @return void
 */
function ratesCurrencyFunction($id, $typeCurrency)
{
    $rates = Currency::where('id', '=', $id)->get();
    foreach ($rates as $rate) {
        if ($typeCurrency == "USD") {
            $rateC = $rate->rates;
        } else {
            $rateC = $rate->rates_eur;
        }
    }
    return $rateC;
}
