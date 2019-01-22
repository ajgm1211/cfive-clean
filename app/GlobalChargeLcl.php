<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GlobalChargeLcl extends Model
{

  protected $table    = "globalcharges_lcl";
  protected $fillable = ['id','surcharge_id','typedestiny_id','contractlcl_id','calculationtypelcl_id','ammount','currency_id','created_at','updated_at'];
  public function companyUser()
  {
    return $this->belongsTo('App\CompanyUser');
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
  public function globalcharportlcl(){

    return $this->hasMany('App\GlobalCharPortLcl','globalchargelcl_id');

  }
  public function globalcharcountrylcl(){

    return $this->hasMany('App\GlobalCharCountryLcl','globalchargelcl_id');

  }
  public function globalcharcarrierslcl(){
    return $this->hasMany('App\GlobalCharCarrierLcl','globalchargelcl_id');

  }

  public function typedestiny(){
    return $this->belongsTo('App\TypeDestiny');

  }
}
