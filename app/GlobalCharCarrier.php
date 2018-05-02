<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GlobalCharCarrier extends Model
{
    protected $table    = "globalcharcarrier";
    protected $fillable =   ['id','carrier_id','globalcharge_id'];

    public function globalcharge()
    {

        return $this->belongsTo('App\GlobalCharge','globalcharge_id');
    }
    public function carrier(){

        return $this->belongsTo('App\Carrier');

    }
}
