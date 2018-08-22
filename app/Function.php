<?php
use App\User;
use Illuminate\Support\Facades\Auth;

function setearRouteKey($key)
{
  $user =  User::where('company_user_id', "=",Auth::user()->company_user_id)->with('companyUser')->first();
  
  if(!empty($user)){
    $hash = $user->companyUser->hash;
  }else{
    $hash = 'cargofivepapa';
  }
  $hashids = new \Hashids\Hashids($hash);

  return $hashids->encode($key);
}

function obtenerRouteKey($key)
{
  $user =  User::where('company_user_id', "=",Auth::user()->company_user_id)->with('companyUser')->first();
  if(!empty($user)){
    $hash =$user->companyUser->hash;
  }else{
    $hash = 'cargofivepapa';
  }
  $hashids = new \Hashids\Hashids($hash);
  $key = $hashids->decode($key);
  return $key[0];
}



