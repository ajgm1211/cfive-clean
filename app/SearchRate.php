<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SearchRate extends Model
{

  protected $fillable =   ['id','pick_up_date','user_id'];

  public function search_ports(){
    return $this->hasMany('App\SearchPort');
  }
  public function user()
  {
    return $this->belongsTo('App\User');
  }
  public function company()
  {
    return $this->belongsTo('App\CompanyUser','company_user_id');
  }
  public function incoterm()
  {
    return $this->belongsTo('App\Incoterm','incoterm_id');
  }

}
