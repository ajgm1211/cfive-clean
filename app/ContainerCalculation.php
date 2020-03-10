<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContainerCalculation extends Model
{
  
  public function container(){
    return $this->belongsTo('App\Container');
  }
  
  public function calculationtype(){
    return $this->belongsTo('App\CalculationType');
  }

}
