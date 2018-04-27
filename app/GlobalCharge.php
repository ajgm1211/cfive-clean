<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GlobalCharge extends Model
{
    protected $table    = "globalcharges";
    protected $fillable = 
        ['id','surcharge_id','port','changetype','carrier_id','user_id','calculationtype_id','ammount','currency_id','created_at','updated_at'];
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function ports(){
        return $this->belongsTo('App\Harbor','port');

    }

    public function carrier(){

        return $this->belongsTo('App\Carrier');

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
}
