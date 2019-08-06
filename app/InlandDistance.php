<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InlandDistance extends Model
{
  protected $fillable = ['distance','harbor_id','inland_location_id'];
  public $timestamps = false;
  public function harbor()
  {
    return $this->belongsTo('App\Harbor');
  }
  public function inlandLocation()
  {
    return $this->belongsTo('App\InlandLocation');
  }
}
