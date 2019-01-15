<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SearchPort extends Model
{

  protected $fillable =   ['id','search_rates_id','port_orig','port_dest'];
  public $timestamps = false;

  public function search_rate()
  {
    return $this->belongsTo('App\SearchRate');
  }

}
