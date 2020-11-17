<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InlandAdditionalKm extends Model
{
  protected $table    = "inland_additional_kms";
  protected $fillable =   ['km_20','km_40','km_40hc','currency_id','inland_id'];
  public $timestamps = false;
  public function inland()
  {
    return $this->belongsTo('App\Inland');
  }
  public function currency(){
    return $this->belongsTo('App\Currency');

  }
}
