<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocalChargePortApi extends Model
{
  
  protected $fillable =   ['port_orig','port_dest','localcharge_id'];
  public $timestamps = false;
  public function localchargeapi()
  {
    return $this->belongsTo('App\LocalChargeApi');
  }
  public function portOrig(){
    return $this->belongsTo('App\Harbor','port_orig');
  }
  public function portDest(){
    return $this->belongsTo('App\Harbor','port_dest');

  }
}
