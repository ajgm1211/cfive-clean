<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class LocalChargeLcl extends Model
{
  use SoftDeletes;
  protected $dates    = ['deleted_at'];
  protected $table    = "localcharges_lcl";
  protected $fillable = ['id','surcharge_id','typedestiny_id','contractlcl_id','calculationtypelcl_id','ammount','currency_id','created_at','updated_at'];
  public function contract()
  {
     return $this->belongsTo('App\ContractLcl','contractlcl_id');
  }

  public function currency(){

    return $this->belongsTo('App\Currency');

  }
  public function calculationtypelcl(){

    return $this->belongsTo('App\CalculationTypeLcl');

  }
  public function surcharge(){

    return $this->belongsTo('App\Surcharge');

  }
  public function localcharportslcl(){

    return $this->hasMany('App\LocalCharPortLcl','localchargelcl_id');

  }
  public function localcharcountrieslcl(){

    return $this->hasMany('App\LocalCharCountryLcl','localchargelcl_id');

  }
  public function localcharcarrierslcl(){
    return $this->hasMany('App\LocalCharCarrierLcl','localchargelcl_id');

  }
  public function typedestiny(){
    return $this->belongsTo('App\TypeDestiny');

  }
}
