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
  public function localcharports(){

    return $this->hasMany('App\LocalChargePortApi','localcharge_id');

  }
  public function localcharcountries(){

    return $this->hasMany('App\LocalChargeCountryApi','localcharge_id');

  }
  public function localcharcarriers(){
    return $this->hasMany('App\LocalChargeCarrierApi','localcharge_id');

  }
  public function typedestiny(){
    return $this->belongsTo('App\TypeDestiny');

  }
}
