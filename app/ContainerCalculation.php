<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContainerCalculation extends Model
{
    protected $fillable = ['id','container_id','calculationtype_id'];
    public $timestamps = false;
    public function container(){
        return $this->belongsTo('App\Container');
    }

    public function calculationtype(){
        return $this->belongsTo('App\CalculationType');
    }

}
