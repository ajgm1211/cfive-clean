<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GlobalCharPortCountry extends Model
{
  public $timestamps = false;
  public function globalcharge()
  {
    return $this->belongsTo('App\GlobalCharge','globalcharge_id');
  }
  public function portOrig(){
    return $this->belongsTo('App\Harbor','port_orig');
  }
  public function countryDest(){
    return $this->belongsTo('App\Country','country_dest');

  }
}
