<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class GlobalCharge extends Model implements Auditable
{
  use \OwenIt\Auditing\Auditable;
  protected $table    = "globalcharges";
  protected $fillable = 
    ['id','surcharge_id','typedestiny_id','company_user_id','calculationtype_id','ammount','validity','expire','currency_id','account_importation_globalcharge_id','created_at','updated_at'];
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
  // Puerto a Puerto
  public function globalcharport(){

    return $this->hasMany('App\GlobalCharPort','globalcharge_id');

  }
  // Pais a Pais
  public function globalcharcountry(){

    return $this->hasMany('App\GlobalCharCountry','globalcharge_id');

  }
  // Pais a puerto 
  public function globalcharcountryport(){

    return $this->hasMany('App\GlobalCharCountryPort','globalcharge_id');

  }
  
  // Puerto a Pais
  public function globalcharportcountry(){

    return $this->hasMany('App\GlobalCharPortCountry','globalcharge_id');

  }

  public function globalcharcarrier(){
    return $this->hasMany('App\GlobalCharCarrier','globalcharge_id');
  }
  
  public function carrier(){
    return $this->hasOne('App\GlobalCharCarrier','globalcharge_id');

  }
  public function globalCharPortCarriers(){

    return $this->hasMany('App\GlobalCharPortCarrier','port');

  }
  public function typedestiny(){
    return $this->belongsTo('App\TypeDestiny');

  }
}
