<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GlobalCharge extends Model
{
  protected $table    = "globalcharges";
  protected $fillable = 
    ['id','surcharge_id','typedestiny_id','company_user_id','calculationtype_id','ammount','currency_id','created_at','updated_at'];
  public function companyUser()
  {
    return $this->belongsTo('App\CompanyUser');
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
  public function globalcharport(){

    return $this->hasMany('App\GlobalCharPort','globalcharge_id');

  }
  public function globalcharcarrier(){
    return $this->hasMany('App\GlobalCharCarrier','globalcharge_id');

  }
  public function globalCharPortCarriers(){

    return $this->hasMany('App\GlobalCharPortCarrier','port');

  }
  public function typedestiny(){
    return $this->belongsTo('App\TypeDestiny');

  }
}
