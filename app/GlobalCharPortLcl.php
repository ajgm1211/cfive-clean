<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GlobalCharPortLcl extends Model
{
  protected $table    = "globalcharports_lcl";
  protected $fillable =   ['port_orig','port_dest','globalchargelcl_id'];
  public $timestamps = false;
  public function globalchargelcl()
  {
    return $this->belongsTo('App\GlobalChargeLcl');
  }
  public function portOrig(){
    return $this->belongsTo('App\Harbor','port_orig');
  }
  public function portDest(){
    return $this->belongsTo('App\Harbor','port_dest');

  }
}
