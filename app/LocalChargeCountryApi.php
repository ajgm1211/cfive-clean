<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocalChargeCountryApi extends Model
{
  protected $fillable =   ['country_orig','country_dest','localcharge_id'];
  public $timestamps = false;
  public function localchargeapi()
  {
    return $this->belongsTo('App\LocalChargeApi','localcharge_id');
  }
  public function countryOrig(){
    return $this->belongsTo('App\Country','country_orig');
  }
  public function countryDest(){
    return $this->belongsTo('App\Country','country_dest');

  }

}
