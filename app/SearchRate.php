<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SearchRate extends Model
{

  protected $fillable =   ['id','pick_up_date','user_id'];

  public function search_ports(){
    return $this->hasMany('App\SearchPort');
  }

}
