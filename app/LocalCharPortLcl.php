<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocalCharPortLcl extends Model
{
  protected $table    = "localcharports_lcl";
  protected $fillable =   ['port_orig','port_dest','localchargelcl_id'];
  public $timestamps = false;
  public function localchargelcl()
  {
    return $this->belongsTo('App\LocalChargeLcl');
  }
  public function portOrig(){
    return $this->belongsTo('App\Harbor','port_orig');
  }
  public function portDest(){
    return $this->belongsTo('App\Harbor','port_dest');

  }
}
