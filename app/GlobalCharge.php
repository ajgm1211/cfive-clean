<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GlobalCharge extends Model
{
    protected $table    = "globalcharges";
    protected $fillable = 
        ['id','surcharge_id','changetype','user_id','calculationtype_id','ammount','currency_id','created_at','updated_at'];
    public function user()
    {
        return $this->belongsTo('App\User');
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
    public function globalcharport(){

        return $this->hasMany('App\GlobalCharPort','globalcharge_id');

    }
    public function globalcharcarrier(){
        return $this->hasMany('App\GlobalCharCarrier','globalcharge_id');

    }
    public function globalCharPortCarriers(){

        return $this->hasMany('App\GlobalCharPortCarrier','port');

    }
}
