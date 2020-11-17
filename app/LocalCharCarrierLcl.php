<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocalCharCarrierLcl extends Model
{
  protected $table    = "localcharcarriers_lcl";
  protected $fillable =   ['carrier_id','localchargelcl_id'];
  public $timestamps = false;
  public function localchargelcl()
  {

    return $this->belongsTo('App\LocalChargeLcl');
  }
  public function carrier(){

    return $this->belongsTo('App\Carrier');

  }
}
