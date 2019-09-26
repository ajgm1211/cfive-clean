<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
  protected $table    = "schedules";
  protected $fillable =['id','vessel','etd','transit_time','type','eta','quote_id'];
  
  public function quotes()
  {
    return $this->belongsTo('App\Quote','quote_id','id');
  }

}
