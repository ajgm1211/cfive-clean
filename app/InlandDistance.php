<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InlandDistance extends Model
{
  protected $fillable = ['distance','zip','address','harbor_id','province_id','display_name'];
  public $timestamps = false;
  public function harbor()
  {
    return $this->belongsTo('App\Harbor');
  }
  public function province()
  {
    return $this->belongsTo('App\Province','province_id');
  }
}
