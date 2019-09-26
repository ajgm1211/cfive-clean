<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocalChargeApi extends Model
{
  protected $fillable = ['id','surcharge_id','typedestiny_id','contract_id','calculationtype_id','ammount','currency_id','created_at','updated_at'];

  public function contractApi()
  {
    return $this->belongsTo('App\ContractApi');
  }

  public function currency(){

    return $this->belongsTo('App\Currency');

  }
  public function calculationtype(){

    return $this->belongsTo('App\CalculationType');

  }
  public function surcharge(){

    return $this->belongsTo('App\Surcharge');

  }
  public function localcharportsapi(){

    return $this->hasMany('App\LocalCharPortApi','localcharge_id');

  }
  public function localcharcountriesapi(){

    return $this->hasMany('App\LocalCharCountryApi','localcharge_id');

  }
  public function localcharcarriersapi(){
    return $this->hasMany('App\LocalCharCarrierApi','localcharge_id');

  }
  public function typedestiny(){
    return $this->belongsTo('App\TypeDestiny');

  }
}
