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

    $isDecimal = optional(Auth::user()->companyUser)->decimals;
    
    if ($isDecimal != null && $isDecimal == 1) {
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

function processOldContainers($array, $type)
{
    if (!empty($array)) {
        switch ($type) {
            case 'amounts':
                foreach ($array as $k => $amount_value) {
                    if ($k == 'c20') {
                        $array['c20DV'] = $amount_value;
                        unset($array['c20']);
                    } elseif ($k == 'c40') {
                        $array['c40DV'] = $amount_value;
                        unset($array['c40']);
                    } elseif ($k == 'c40hc') {
                        $array['c40HC'] = $amount_value;
                        unset($array['c40hc']);
                    } elseif ($k == 'c40nor') {
                        $array['c40NOR'] = $amount_value;
                        unset($array['c40nor']);
                    } elseif ($k == 'c45hc') {
                        $array['c45HC'] = $amount_value;
                        unset($array['c45hc']);
                    }
                }
                return $array;
                break;
            case 'markups':
                foreach ($array as $k => $markup_value) {
                    if ($k == 'm20') {
                        $array['m20DV'] = $markup_value;
                        unset($array['m20']);
                    } elseif ($k == 'm40') {
                        $array['m40DV'] = $markup_value;
                        unset($array['m40']);
                    } elseif ($k == 'm40hc') {
                        $array['m40HC'] = $markup_value;
                        unset($array['m40hc']);
                    } elseif ($k == 'm40nor') {
                        $array['m40NOR'] = $markup_value;
                        unset($array['m40nor']);
                    } elseif ($k == 'm45hc') {
                        $array['m45HC'] = $markup_value;
                        unset($array['m45hc']);
                    }
                }
                return $array;
                break;
        }
    }
}
