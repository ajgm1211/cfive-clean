<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContractAddons extends Model
{
  protected $fillable = ['id', 'base_port','port','carrier_id','contract_id','twuenty_addons','forty_addons','fortyhc_addons','fortynor_addons','fortyfive_addons', 'currency_id'];
  public function contract()
  {
    return $this->belongsTo('App\Contract');
  }

  public function port_origin(){
    return $this->belongsTo('App\Harbor','origin_port');

  }
  public function port_destiny(){
    return $this->belongsTo('App\Harbor','destiny_port');

  }
  public function carrier(){

    return $this->belongsTo('App\Carrier');

  }
  public function currency(){

    return $this->belongsTo('App\Currency');

  }

}
