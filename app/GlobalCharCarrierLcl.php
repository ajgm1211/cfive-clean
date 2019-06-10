<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GlobalCharCarrierLcl extends Model
{
  protected $table    = "globalcharcarriers_lcl";
  protected $fillable =   ['carrier_id','globalchargelcl_id'];
  public $timestamps = false;
  public function globalchargelcl()
  {

    return $this->belongsTo('App\GlobalChargeLcl');
  }
  public function carrier(){

    return $this->belongsTo('App\Carrier');

  }
}
