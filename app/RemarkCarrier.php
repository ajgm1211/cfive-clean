<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RemarkCarrier extends Model
{
  protected $table = "remark_carriers";
  protected $fillable = ['carrier_id', 'remark_condition_id'];

  public function carrier(){
    return $this->belongsTo('App\Carrier','carrier_id');
  }
}
