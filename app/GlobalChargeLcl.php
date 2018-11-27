<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GlobalChargeLcl extends Model
{
    use SoftDeletes;
  protected $dates    = ['deleted_at'];
  protected $table    = "globalcharges_lcl";
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
  public function globalcharportslcl(){

    return $this->hasMany('App\GlobalCharPortLcl','globalchargelcl_id');

  }
  public function globalcharcountrieslcl(){

    return $this->hasMany('App\GlobalCharCountryLcl','globalchargelcl_id');

  }
  public function globalcharcarrierslcl(){
    return $this->hasMany('App\GlobalCharCarrierLcl','globalchargelcl_id');

  }
  public function typedestiny(){
    return $this->belongsTo('App\TypeDestiny');

  }
}
