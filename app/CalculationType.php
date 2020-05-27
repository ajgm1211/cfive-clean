<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CalculationType extends Model
{


    protected $table    = "calculationtype";
    protected $fillable = ['id', 'name','options','gp_pcontainer'];


    public function localcharge()
    {

        return $this->hasOne('App\LocalCharge');
    }
    
    public function containersCalculation(){
        return $this->hasMany('App\ContainerCalculation','calculationtype_id');
    }


}
