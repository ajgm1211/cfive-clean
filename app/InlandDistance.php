<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InlandDistance extends Model
{
  protected $fillable = ['distance','zip','address','harbor_id','province_id'];
  public $timestamps = false;
  public function harbor()
  {
    return $this->belongsTo('App\Harbor');
  }
  public function inlandLocation()
  {
    return $this->belongsTo('App\InlandLocation','province_id');
  }
}
