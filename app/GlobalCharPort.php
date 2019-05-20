<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GlobalCharPort extends Model
{

    protected $table    = "globalcharport";
    protected $fillable =   ['id','port_orig','port_dest','typedestiny_id','globalcharge_id'];
    public $timestamps = false;
    public function globalcharge()
    {
        return $this->belongsTo('App\GlobalCharge','globalcharge_id');
    }
    public function portOrig(){
        return $this->belongsTo('App\Harbor','port_orig');
    }
    public function portDest(){
        return $this->belongsTo('App\Harbor','port_dest');

    }
    public function typedestiny(){
        return $this->belongsTo('App\TypeDestiny');

    }

    public function globalCarrier(){
        return $this->hasManyThrough('App\GlobalCharCarrier','App\GlobalCharge','id','globalcharge_id');

    }
}
