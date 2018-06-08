<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocalCharge extends Model
{
    protected $table    = "localcharges";
    protected $fillable = 
        ['id','surcharge_id','typedestiny_id','contract_id','calculationtype_id','ammount','currency_id','created_at','updated_at'];
    public function contract()
    {
        return $this->belongsTo('App\Contract');
    }

    public function currency(){

        return $this->belongsTo('App\Currency');

    }
    public function calculationtype(){

        return $this->belongsTo('App\CalculationType');

    }
    public function surcharge(){

        return $this->belongsTo('App\Surcharge');

    }
    public function localcharports(){

        return $this->hasMany('App\LocalCharPort','localcharge_id');

    }
    public function localcharcarriers(){
        return $this->hasMany('App\LocalCharCarrier','localcharge_id');

    }
    public function typedestiny(){
        return $this->belongsTo('App\TypeDestiny');

    }
}
