<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocalCharge extends Model
{
    protected $table    = "localcharges";
    protected $fillable = ['id','type','port','changetype','carrier_id','contract_id','validsince','validto','calculationtype','ammount','currency_id','created_at','updated_at'];
    public function contract()
    {
        return $this->belongsTo('App\Contract');
    }
    public function port(){
        return $this->belongsTo('App\Harbor','port');

    }
    public function carrier(){

        return $this->belongsTo('App\Carrier');

    }
    public function currency(){

        return $this->belongsTo('App\Currency');

    }
}
