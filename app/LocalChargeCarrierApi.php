<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocalChargeCarrierApi extends Model
{
  protected $fillable =   ['carrier_id','localcharge_id'];
  public $timestamps = false;
  public function localchargeapi()
  {

    return $this->belongsTo('App\LocalChargeApi','localcharge_id');
  }
  public function carrier(){

    return $this->belongsTo('App\Carrier');

  }
}
