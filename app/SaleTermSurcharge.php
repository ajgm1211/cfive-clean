<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class SaleTermSurcharge extends Model implements Auditable
{
  use \OwenIt\Auditing\Auditable;
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

