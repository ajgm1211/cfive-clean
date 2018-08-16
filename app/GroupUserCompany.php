<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupUserCompany extends Model
{
  protected $table    = "group_users_companies";
  protected $fillable =   ['user_id','company_id'];
  public $timestamps = false;
  
  public function company()
  {
    return $this->belongsTo('App\Company');
  }
  public function user()
  {
    return $this->belongsTo('App\User', 'user_id');
  }
}


