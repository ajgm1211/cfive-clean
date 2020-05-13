<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Surcharge extends Model
{
  protected $table    = "surcharges";
  protected $fillable = ['id', 'name', 'description','sale_term_id','company_user_id','options'];

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

  public function saleterm()
  {
    return $this->hasOne('App\SaleTerm','id','sale_term_id');
  }

}
