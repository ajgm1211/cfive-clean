<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GlobalCharCountryPort extends Model
{
  	public $timestamps = false;
  public function globalcharge()
  {
    return $this->belongsTo('App\GlobalCharge','globalcharge_id');
  }
  public function countryOrig(){
    return $this->belongsTo('App\Country','country_orig');
  }
  public function portDest(){
    return $this->belongsTo('App\Harbor','port_dest');

  }
}
