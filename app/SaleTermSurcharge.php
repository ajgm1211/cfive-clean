<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleTermSurcharge extends Model
{
  protected $fillable =['surcharge_id','sale_term_id'];

  public function saleterm()
  {
    return $this->belongsTo('App\SaleTerm','sale_term_id');
  }

  public function surcharge()
  {
    return $this->belongsTo('App\Surcharge');
  }

}

