<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InlandLocation extends Model
{ 
  protected $fillable = ['name','region','country_id','company_user_id'];
  public function companyUser()
  {
    return $this->belongsTo('App\CompanyUser');
  }
  public function country()
  {
    return $this->belongsTo('App\Country','country_id');
  }
}
