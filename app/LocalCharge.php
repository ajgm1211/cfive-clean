<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocalCharge extends Model
{
    protected $table    = "localcharges";
    protected $fillable = 
    ['id','surcharge_id','port','changetype','carrier_id','contract_id','calculationtype_id','ammount','currency_id','created_at','updated_at'];
    public function contract()
    {
        return $this->belongsTo('App\Contract');
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
