<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Surcharge extends Model
{
  protected $table    = "surcharges";
  protected $fillable = ['id', 'name', 'description'];
 
  public function companyUser()
  {
    return $this->belongsTo('App\CompanyUser');
  }

  public function localcharge()
  {

    return $this->hasOne('App\LocalCharge');
  }
  public function globalcharge()
  {

    return $this->hasOne('App\GlobalCharge');
  }


}
