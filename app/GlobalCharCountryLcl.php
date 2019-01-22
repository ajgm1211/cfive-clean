<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GlobalCharCountryLcl extends Model
{
  protected $table    = "globalcharcountry_lcl";
  protected $fillable =   ['country_orig','country_dest','globalchargelcl_id'];
  public $timestamps = false;
  public function globalchargelcl()
  {
    return $this->belongsTo('App\GlobalCharge','globalchargelcl_id');
  }
  public function countryOrig(){
    return $this->belongsTo('App\Country','country_orig');
  }
  public function countryDest(){
    return $this->belongsTo('App\Country','country_dest');

  }
}
