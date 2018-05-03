<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocalCharCarrier extends Model
{
    protected $table    = "localcharcarriers";
    protected $fillable =   ['carrier_id','localcharge_id'];
    public $timestamps = false;
    public function localcharge()
    {

        return $this->belongsTo('App\LocalCharge');
    }
    public function carrier(){

        return $this->belongsTo('App\Carrier');

    }
}
